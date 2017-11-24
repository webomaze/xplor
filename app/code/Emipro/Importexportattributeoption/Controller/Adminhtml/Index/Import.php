<?php
namespace Emipro\Importexportattributeoption\Controller\Adminhtml\Index;

use Magento\Framework\App\Filesystem\DirectoryList;

class Import extends \Magento\Backend\App\Action {

    protected $_eavConfig;
    protected $_uploaderFactory;
    protected $_basepath;
    protected $_attributeProcess;
    protected $_attributeOptions;
    protected $_directoryList;
    protected $_entityAttribute;
    protected $_sourceTable;
    protected $_coreRegistry = null;
    protected $_eavAttribute;

    public function __construct(
        \Magento\Backend\App\Action\Context $context, 
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory, 
        \Magento\Eav\Model\Config $attribute,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        \Magento\Eav\Model\Entity\Attribute\Source\Table $sourceTable,
        \Magento\Framework\Filesystem $filesystem
        )
    {
        $this->_eavConfig = $attribute;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_coreRegistry = $registry;
        $this->_directoryList = $directoryList;
        $this->_entityAttribute = $entityAttribute;
        $this->_eavAttribute = $eavAttribute;
        $this->_sourceTable = $sourceTable;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        parent::__construct($context);
        $this->_basepath = $this->getBaseDir();
    }

    public function execute() {
        $attribute_id = $this->getRequest()->getParam("attribute");
        $resultRedirect = $this->resultRedirectFactory->create();
        $post = $this->getRequest()->getPost();
        try {
            if (isset($_FILES['fileToUpload']['name']) && $_FILES['fileToUpload']['name'] != '') {
                $fileName = $_FILES['fileToUpload']['name'];
                $fileExt = strtolower(substr(strrchr($fileName, "."), 1));
                $fileNamewoe = rtrim($fileName, $fileExt);
                $fileName = "attribute_options_import_" . date("Y-m-d") . ".csv";
                $uploader = $this->_uploaderFactory->create(['fileId' => $_FILES['fileToUpload']]);
                $uploader->setAllowedExtensions(['csv']);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                if (!is_dir($this->_basepath)) {
                    mkdir($this->_basepath, 0777, true);
                }
                $uploader->save($this->_basepath . "/", $fileName);
                $_result = $this->callFile($post["attribute"], $fileName, $post["delimiter"]);
                if($_result=='invalid'){
                    $this->messageManager->addError(__("Invalid csv file."));
                    return $resultRedirect->setPath('importexportattributeoption/index/index');
                }
                $this->messageManager->addSuccess(__("Attribute options has been imported successfully...!!!"));
                return $resultRedirect->setPath('importexportattributeoption/index/index');
            } else {
                $this->messageManager->addError(__("Please select correct csv file."));
                return $resultRedirect->setPath('importexportattributeoption/index/index');
            } 
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError(__("Attribute options has not been imported successfully...!!!"));
            return $resultRedirect->setPath('importexportattributeoption/index/index');
        }
    }

    public function getBaseDir() {
        return $this->_directoryList->getRoot() . "/var/import/";
    }

    /*
     *  getData form csv file
     */
    public function callFile($name, $fileName, $delimiter) {
        $firstColumn=0;
        $data = [];
        $file = fopen($this->_basepath . $fileName, "r");
        while (!feof($file)) {
            if ($delimiter == "comma") {
                $data = fgetcsv($file, 0, ",");
            } else if ($delimiter == "semicolon") {
                $data = fgetcsv($file, 0, ";");
            } else if ($delimiter == "colon") {
                $data = fgetcsv($file, 0, ":");
            } else if ($delimiter == "pipe") {
                $data = fgetcsv($file, 0, "|");
            }
            if ($data[1] == "") {
                return;
            }
           $storecode = count($this->getStoresid()) * 2 + 1;
           $positionCode = $storecode+1; 
            if(!empty($data[$positionCode]))
            {
                if (is_numeric($data[$positionCode])) {
                    $position = $data[$positionCode];
                    array_pop($data);
                }
                else {
                    $position = 0;
                } 
            }
            else {
                $position = 0;
            }
            if($firstColumn!=0){
                $this->addAttributeValue($name, $data, $position, $storecode);
            }
            $firstColumn=1;
        }
    }

    /*
     *  check attribute options is exists then update either add new options
     */
    public function addAttributeValue($arg_attribute, $arg_value, $position, $storecode) 
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $argValueTemp = [];
        $attribute = $this->_entityAttribute->load($arg_attribute);
        if(empty($attribute->getData())){
            return;
        }
        $attribute_option = $this->_sourceTable->setAttribute($attribute);
        if (!$this->attributeValueExists($arg_attribute, $arg_value, $position, $storecode)) {            
            for ($i = 0; $i < count($arg_value); $i++) {
                $i++;
                if(isset($arg_value[$i])){
                    $argValueTemp[]=$arg_value[$i];
                }
            }
            
            $value['option'] = $argValueTemp;
            $result = ['value' => $value];
            $attribute->setData('option', $result);
            $attribute->save();
            
            $val_no = $arg_value[1];
            $this->updateValue($arg_attribute, $val_no, $arg_value, $position, $storecode);
        }
    }

    public function attributeValueExists($arg_attribute, $arg_value, $position, $storecode) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attribute = $this->_eavAttribute->load($arg_attribute);
        $attribute_option = $this->_sourceTable->setAttribute($attribute);
        $options = $attribute_option->getAllOptions(false, true);
        foreach ($options as $option) {
            if ($option['label'] == $arg_value[1]) {
                $this->updateValue($arg_attribute, $option['label'], $arg_value, $position, $storecode);
                return $option['value'];
            }
        }
        return false;
    }

    /*
     *  check atrrbute option is DropDown, TextSwatch, or VisualSwatch
     */
    public function updateValue($arg_attribute, $option_val, $arg_value, $position, $storecode) 
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $allStores =$this->getStoresid();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attr_model = $this->_eavAttribute->load($arg_attribute);
        try{
            $attribute_option = $objectManager->create("\Magento\Eav\Model\Entity\Attribute\Source\Table");
            $attribute_option->setAttribute($attr_model);
            $options = $attribute_option->getAllOptions(false, true);
            $val_no = 0;

            foreach ($options as $option) {
                if ($option['label'] == $option_val) {
                    $val_no = $option['value'];
                    break;
                }
            }
           
            if($attr_model->getAdditionalData()!=''){
                $unserialData = unserialize(($attr_model->getAdditionalData()));
                if(isset($unserialData['swatch_input_type'])){
                    if($unserialData['swatch_input_type'] == 'text'){
                        $value_final = $this->textOption($allStores,$option_val,$arg_value,$position,$val_no);
                    }
                    if($unserialData['swatch_input_type'] == 'visual'){
                        $value_final = $this->visualOption($allStores,$option_val,$arg_value,$position,$val_no);
                    }
                }else{
                    $value_final = $this->selectOption($allStores,$option_val,$arg_value,$position,$val_no);
                }
            }else
            {
                $value_final = $this->selectOption($allStores,$option_val,$arg_value,$position,$val_no);
            }
            
            $attr_model->addData($value_final);
            $attr_model->save();
            return true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return false;
        }
    }

    /*
     *  return data for VisualSwatch
     */
    public function visualOption($allStores,$option_val,$arg_value,$position,$val_no){
        $value = [];
        $value[0] = $option_val;
        $swatchVisual = '';

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        for ($i = 0; $i < (count($allStores)*2); $i++) 
        {
            if($i == 0){
                $swatchVisual = $arg_value[$i];
            }
            $i++;
            if(isset($arg_value[$i])) 
            {
                if ($arg_value[$i+2]) 
                {
                    $value[] = $arg_value[$i+2];
                }
            }
        }
        $value = [$val_no => $value];
        $value = ['value' => $value];
        $value1['order'][$val_no] = $position;
        $value_final = array_merge($value, $value1);
        
        $dstPath = 'pub/media/tmp/catalog/product';
        $tmpPath = 'pub/media/import/'.$swatchVisual;
        
        if(file_exists($tmpPath) && $swatchVisual!=''){
            $swatchVisual = '/'.$swatchVisual;
            if(!file_exists($this->_directory->getAbsolutePath($dstPath))){
                mkdir($this->_directory->getAbsolutePath($dstPath),0777,true);
            }
            copy($tmpPath,$dstPath.$swatchVisual);
            
            $media_dir = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

            $path = $media_dir.'tmp/catalog/product'.$swatchVisual;

            $result['url'] = $path;
            $result['file'] = $swatchVisual.'.tmp';
            
            $swatchHelper = $objectManager->create('Magento\Swatches\Helper\Media');

            $newFile = $swatchHelper->moveImageFromTmp($result['file']);
            $swatchHelper->generateSwatchVariations($newFile);

            $value_final = ['optionvisual' => $value_final,'swatchvisual' => ['value' => [$val_no => $newFile]]];
        }else{
            $value_final = ['optionvisual' => $value_final,'swatchvisual' => ['value' => [$val_no => $swatchVisual]]];
        }
        return $value_final;
    }

    /*
     *  return data for DropDown
     */
    public function selectOption($allStores,$option_val,$arg_value,$position,$val_no){
        $value = [];
        $value[0] = $option_val;
        for ($i = 0; $i < (count($allStores)*2); $i++) 
        {
            $i++;
            if(isset($arg_value[($i + 2)]))
            {
                $value[] = $arg_value[($i + 2)];
            }
        }
        $value = [$val_no => $value];
        $value = ['value' => $value];
        $value1['order'][$val_no] = $position;
        $value_final = array_merge($value, $value1);
        $value_final = ['option' => $value_final];
        return $value_final;
    }

    /*
     *  return data for TextSwatch
     */
    public function textOption($allStores,$option_val,$arg_value,$position,$val_no){
        $value = [];
        $value[0] = $option_val;
        $swatchValue = [];
        $j = 0;
        try{        
            for ($i = 0; $i < (count($allStores)*2); $i++) 
            {
                $i++;
                $j++;
                if(isset($arg_value[$i])) 
                {
                    if ($arg_value[$i+2]) 
                    {
                        $value[$j] = $arg_value[$i+2];
                    }
                    if($arg_value[$i+1]){
                            $swatchValue[$j] = $arg_value[$i+1]; 
                    }
                }
            }
            
            $value = [$val_no => $value];
            $value = ['value' => $value];
            $value1['order'][$val_no] = $position;
            $value_final = array_merge($value, $value1);
            $swatchValue = [$val_no => $swatchValue];
            $swatchValue = ['value' => $swatchValue];
            $value_final = ['optiontext' => $value_final,'swatchtext' => $swatchValue];
            return $value_final;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError(__("Attribute  successfully...!!!"));
            $resultRedirect->setPath('importexportattributeoption/index/index');
        }

    }

    /*
     *  return data for number if storeview
     */
    public function getStoresid() 
    {
        $isAvailable = $this->_coreRegistry->registry('import_exportstore');
        if ($isAvailable) {
            return $isAvailable;
        } 
        else 
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $stores = $objectManager->create('Magento\Store\Model\Store');
            $store_id = [];
            foreach ($stores->getCollection() as $store) {
                array_push($store_id, $store->getStoreId());
            }
            $this->_coreRegistry->register("import_exportstore", $store_id);
            return $store_id;
        }
    }
}
