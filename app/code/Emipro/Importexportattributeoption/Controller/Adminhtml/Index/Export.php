<?php
namespace Emipro\Importexportattributeoption\Controller\Adminhtml\Index;

use Magento\Framework\App\Response\Http\FileFactory;

class Export extends \Magento\Backend\App\Action {

    protected $_attrOptionCollectionFactory;
    protected $_storename;
    protected $fileFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context, 
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory, 
        \Magento\Store\Model\ResourceModel\Store\Collection $store, 
        FileFactory $fileFactory
    ) {
        $this->_attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->_storename = $store;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    public function execute() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $id = $this->getRequest()->getParam("attribute");
        $attributename = $this->getRequest()->getParam("attributename");

        $storeid = [];
        foreach ($this->_storename->getData("store_id") as $store) {
            array_push($storeid, $store["store_id"]);
        }
        $valuear = [];
        $position = [];
        for ($i = 0; $i < count($storeid); $i++) {
            $finalarray = [];
            $ind_position = 0;
        
       
            $options = $this->_attrOptionCollectionFactory->create()->setAttributeFilter(
                            $id
                    )
                    ->setPositionOrder(
                            'asc', true
                    )->setStoreFilter($storeid[$i]);

                $options->getSelect()->joinLeft(
                    ['swatch_table' => $options->getTable('eav_attribute_option_swatch')],
                    'swatch_table.option_id = main_table.option_id AND swatch_table.store_id = '.$storeid[$i],
                    'swatch_table.value AS label'
                );
            if(!empty($options)){
                if (count($options) > 0) {
                    foreach ($options as $option) {
                        $values[$option->getId()] = $option->getValue();
                        if($option->getLabel()){
                            $swatchValue = $option->getLabel();
                        }
                        else{
                            $swatchValue = '';
                        }
                        array_push($finalarray, $swatchValue);
                        array_push($finalarray, $option["store_default_value"]);
                        $ind_position = $option["sort_order"];
                        array_push($position, $ind_position-1);
                    }
                } else {
                    $this->fileFactory->create('export.csv', "No Options Available", 'var');
                }
            }else{
                    $this->fileFactory->create('export.csv', "No Options Available", 'var');
            }
            array_push($valuear, $finalarray);
        }
        
        /*
         * for header information
         */
        $csvdata = "";
        $csvdata.="VisualSwatch,Admin";
        for ($i = 1; $i < count($storeid); $i++) {
            $csvdata.=',TextSwatch'.$i.',StoreView'.$i;
        }
        $csvdata.=",Position";
        $csvdata.="\n";

        /*
         * attribute option
         */
        for ($i = 0; $i < count($valuear[0]); $i++) {
            foreach ($valuear as $val) {
                $csvdata.=$val[$i] . ",";
                $csvdata.=$val[$i+1] . ",";
            }
            $i++;
            $csvdata.=$position[$i] . "\n";
        }
        $this->fileFactory->create($attributename .'_Options.csv', $csvdata, 'var');
    }
}
