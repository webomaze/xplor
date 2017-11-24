<?php

namespace Wyomind\MassStockUpdate\Block\Adminhtml\Profiles\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic
        implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_dataHelper = null;
    protected $_attributeRepository = null;
    protected $_objectManager = null;
    protected $_storageHelper = null;
    protected $_configHelper = null;
    protected $_dateTime = null;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry,
            \Magento\Framework\Data\FormFactory $formFactory,
            \Wyomind\MassStockUpdate\Helper\Data $dataHelper,
            \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
            \Magento\Framework\ObjectManager\ObjectManager $objectManager,
            \Wyomind\MassStockUpdate\Helper\Storage $storageHelper,
            \Wyomind\MassStockUpdate\Helper\Config $configHelper,
            \Magento\Framework\Stdlib\DateTime\DateTime $dateTime, array $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_attributeRepository = $attributeRepository;
        $this->_dataHelper = $dataHelper;
        $this->_objectManager = $objectManager;
        $this->_configHelper = $configHelper;
        $this->_storageHelper = $storageHelper;
        $this->_dateTime = $dateTime;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('profile');
        $form = $this->_formFactory->create();




        $fieldset = $form->addFieldset('massstockupdate_general_settings', ['legend' => __('Profile Settings')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }


        $fieldset->addField(
                'name', 'text', [
            'name' => 'name',
            'label' => __('Profile name'),
            "required" => true,
                ]
        );




        $fieldset->addField(
                'sql', 'select', [
            'name' => 'sql',
            'label' => __('SQL mode'),
            "required" => true,
            'values' => [
                "1" => __('Yes'),
                '0' => __('No')
            ],
            "note" => __("When SQL mode is enabled, no stocks are updated. Running the profile will only produce a SQL file. This file could be executed directly in your database manager")
                ]
        );

        $fieldset->addField(
                'sql_file', 'text', [
            'name' => 'sql_file',
            'label' => __('SQL file name'),
            "required" => true,
            "note" => __("Name of the SQL file to generate.")
                ]
        );

        $fieldset->addField(
                'sql_path', 'text', [
            'name' => 'sql_path',
            'label' => __('SQL file path'),
            "required" => true,
            "note" => __("Path where the SQL file will be generated (relative to Magento root folder).")
                ]
        );

        $fieldset = $form->addFieldset('massstockupdate_file_location', ['legend' => __('File Location')]);



        $fieldset->addField(
                'file_system_type', 'select', [
            'name' => 'file_system_type',
            'label' => __('File location'),
            'class' => "update-preview",
            "required" => true,
            'values' => [
                \Wyomind\MassStockUpdate\Helper\Data::LOCATION_MAGENTO => __('Magento file system'),
                \Wyomind\MassStockUpdate\Helper\Data::LOCATION_FTP => __('Ftp server'),
                \Wyomind\MassStockUpdate\Helper\Data::LOCATION_URL => __('Url'),
                \Wyomind\MassStockUpdate\Helper\Data::LOCATION_WEBSERVICE => __('Web service')
            ],
            "note" => __("From where to fetch the stock update file.<br/>")
            . __("- <b>Magento file system</b> : the file is located on the server where Magento is installed <i>(relative to Magento root Folder)</i><br/>")
            . __("- <b>Ftp server</b> : the file is located on a Ftp server<br/>")
            . __("- <b>Url</b> : the file is located on an external server, and it can be fetched using an external url <i>(eg : http://www.website.com/import.csv)</i><br/>")
                ]
        );


        /* FTP */

        $fieldset->addField(
                'use_sftp', 'select', [
            'label' => __('Use SFTP'),
            'name' => 'use_sftp',
            'id' => 'use_sftp',
            'class' => "update-preview",
            'required' => true,
            'values' => [
                "1" => __('Yes'),
                '0' => __('No')
            ]
                ]
        );
        $fieldset->addField(
                'ftp_active', 'select', [
            'label' => __('Use active mode'),
            'name' => 'ftp_active',
            'class' => "update-preview",
            'id' => 'ftp_active',
            'required' => true,
            'values' => [
                "1" => __('Yes'),
                '0' => __('No')
            ]
                ]
        );


        $fieldset->addField(
                'ftp_host', 'text', [
            'label' => __('Host'),
            'name' => 'ftp_host',
            'class' => "update-preview",
            'id' => 'ftp_host',
                ]
        );

        $fieldset->addField(
                'ftp_port', 'text', [
            'label' => __('Port'),
            'name' => 'ftp_port',
            'class' => "update-preview",
            'id' => 'ftp_port',
                ]
        );

        $fieldset->addField(
                'ftp_login', 'text', [
            'label' => __('Login'),
            'name' => 'ftp_login',
            'class' => "update-preview",
            'id' => 'ftp_login',
                ]
        );
        $fieldset->addField(
                'ftp_password', 'password', [
            'label' => __('Password'),
            'name' => 'ftp_password',
            'class' => "update-preview",
            'id' => 'ftp_password',
                ]
        );
        $fieldset->addField(
                'ftp_dir', 'text', [
            'label' => __('Directory'),
            'name' => 'ftp_dir',
            'class' => "update-preview",
            'id' => 'ftp_dir',
            'note' => __("<a style='margin:10px; display:block;' href='javascript:MassStockUpdate.ftp.test(\"%1\")'>Test Connection</a>", $this->getUrl('*/*/ftp'))
                ]
        );

        /* Common */

        $fieldset->addField(
                'file_path', 'text', [
            'name' => 'file_path',
            'class' => "update-preview",
            'label' => __('Path to file'),
            "required" => true,
            "note" => __("- <b>Magento file system</b> : File path relative to Magento root folder</i><br/>")
            . __("- <b>Ftp server</b> : File path relative to ftp user root folder<br/>")
            . __("- <b>Url</b> : Url of the file<br/>")
            . __("- <b>Web service</b> : Url of the web service<br/>")
                ]
        );

        /* Web service */

        $fieldset->addField(
                'webservice_params', 'textarea', [
            'label' => __('Parameters'),
            'name' => 'webservice_params',
            'id' => 'webservice_params',
                ]
        );

        $fieldset->addField(
                'webservice_login', 'text', [
            'label' => __('Login'),
            'name' => 'webservice_login',
            'id' => 'webservice_login',
                ]
        );
        $fieldset->addField(
                'webservice_password', 'password', [
            'label' => __('Parameters'),
            'name' => 'webservice_password',
            'id' => 'webservice_password',
                ]
        );

        $configUrl = $this->getUrl('adminhtml/system_config/edit', ['section' => 'massstockupdate']);


        $fieldset = $form->addFieldset('massstockupdate_file_type', ['legend' => __('File Type')]);


        $fieldset->addField(
                'file_type', 'select', [
            'name' => 'file_type',
            'class' => "update-preview",
            'label' => __('File type'),
            "required" => false,
            'values' => [
                \Wyomind\MassStockUpdate\Helper\Data::CSV => __('csv'),
                \Wyomind\MassStockUpdate\Helper\Data::XML => __('xml')
            ]
                ]
        );


        /* CSV */

        $fieldset->addField(
                'field_delimiter', 'select', [
            'name' => 'field_delimiter',
            'class' => "update-preview",
            'label' => __('Field delimiter'),
            "required" => false,
            'values' => $this->_dataHelper->getFieldDelimiters(),
                ]
        );
        $fieldset->addField(
                'field_enclosure', 'select', [
            'name' => 'field_enclosure',
            'class' => "update-preview",
            'label' => __('Field enclosure'),
            "required" => false,
            'values' => $this->_dataHelper->getFieldEnclosures(),
                ]
        );

        /* XML */
        $fieldset->addField(
                'xml_xpath_to_product', 'text', [
            'name' => 'xml_xpath_to_product',
            'class' => "update-preview",
            'label' => __('Xpath to products'),
            "required" => true,
            "note" => __("xPath where the product data is stored in the XML file, e.g.:/catalog/products/product")
                ]
        );







        $fieldset->addField(
                'run', "hidden", [
            'name' => 'run',
            'class' => 'debug',
            'value' => ''
                ]
        );

        $fieldset->addField(
                'run_i', 'hidden', [
            'name' => 'run_i',
            'value' => ''
                ]
        );



        $fieldset = $form->addFieldset('massstockupdate_identifier_settings', ['legend' => __('Identifier Settings')]);


        $fieldset->addField(
                'identifier_offset', 'text', [
            'name' => 'identifier_offset',
            "required" => true,
            "class" => "update-preview validate-number",
            'label' => __("Product identifier column number"),
            'note' => __("Id of the column that contains the product identifier in the imported file (1st column is id 1)>")
                ]
        );



        $fieldset->addField(
                'identifier', 'select', [
            'name' => 'identifier',
            'class' => "update-preview",
            'label' => __('Product Identifier type'),
            'values' => $this->_dataHelper->getProductIdentifiers(),
            "note" => __("Products to update must match the above unique identifier")
                ]
        );



        $fieldset = $form->addFieldset('massstockupdate_stock_settings', ['legend' => __('Stock Settings')]);

        $fieldset->addField(
                'auto_set_instock', 'select', [
            'name' => 'auto_set_instock',
            'label' => __('Automatic stock status update'),
            'options' => [
                1 => __('yes'),
                0 => __('no')
            ],
            'note' => __("Stock status will be automatically updated (in stock / out of stock)")
                ]
        );



        $block = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');

        $this->setChild('form_after', $block
                        ->addFieldMap('use_custom_rules', 'use_custom_rules')
                        ->addFieldMap('custom_rules', 'custom_rules')
                        ->addFieldMap('file_system_type', 'file_system_type')
                        ->addFieldMap('file_type', 'file_type')
                        ->addFieldMap('field_delimiter', 'field_delimiter')
                        ->addFieldMap('field_enclosure', 'field_enclosure')
                        ->addFieldMap('xml_xpath_to_product', 'xml_xpath_to_product')
                        ->addFieldMap('use_sftp', 'use_sftp')
                        ->addFieldMap('ftp_host', 'ftp_host')
                        ->addFieldMap('ftp_login', 'ftp_login')
                        ->addFieldMap('ftp_password', 'ftp_password')
                        ->addFieldMap('ftp_dir', 'ftp_dir')
                        ->addFieldMap('ftp_port', 'ftp_port')
                        ->addFieldMap('ftp_active', 'ftp_active')
                        ->addFieldMap('sql', 'sql')
                        ->addFieldMap('sql_file', 'sql_file')
                        ->addFieldMap('sql_path', 'sql_path')
                        ->addFieldMap('webservice_params', 'webservice_params')
                        ->addFieldMap('webservice_login', 'webservice_login')
                        ->addFieldMap('webservice_password', 'webservice_password')

                        // SHELL MODE
                        ->addFieldDependence('sql_file', 'sql', \Wyomind\MassStockUpdate\Helper\Data::YES)
                        ->addFieldDependence('sql_path', 'sql', \Wyomind\MassStockUpdate\Helper\Data::YES)
                        // FTP
                        ->addFieldDependence('ftp_host', 'file_system_type', \Wyomind\MassStockUpdate\Helper\Data::LOCATION_FTP)
                        ->addFieldDependence('use_sftp', 'file_system_type', \Wyomind\MassStockUpdate\Helper\Data::LOCATION_FTP)
                        ->addFieldDependence('ftp_login', 'file_system_type', \Wyomind\MassStockUpdate\Helper\Data::LOCATION_FTP)
                        ->addFieldDependence('ftp_password', 'file_system_type', \Wyomind\MassStockUpdate\Helper\Data::LOCATION_FTP)
                        ->addFieldDependence('ftp_active', 'file_system_type', \Wyomind\MassStockUpdate\Helper\Data::LOCATION_FTP)
                        ->addFieldDependence('ftp_dir', 'file_system_type', \Wyomind\MassStockUpdate\Helper\Data::LOCATION_FTP)
                        ->addFieldDependence('ftp_port', 'file_system_type', \Wyomind\MassStockUpdate\Helper\Data::LOCATION_FTP)
                        ->addFieldDependence('ftp_active', 'use_sftp', \Wyomind\MassStockUpdate\Helper\Data::NO)
                        // WEB SERVICE
                        ->addFieldDependence('webservice_params', 'file_system_type', \Wyomind\MassStockUpdate\Helper\Data::LOCATION_WEBSERVICE)
                        ->addFieldDependence('webservice_login', 'file_system_type', \Wyomind\MassStockUpdate\Helper\Data::LOCATION_WEBSERVICE)
                        ->addFieldDependence('webservice_password', 'file_system_type', \Wyomind\MassStockUpdate\Helper\Data::LOCATION_WEBSERVICE)

                        // RULES
                        ->addFieldDependence('custom_rules', 'use_custom_rules', \Wyomind\MassStockUpdate\Helper\Data::YES)
                        // CSV / XML
                        ->addFieldDependence('field_enclosure', 'file_type', \Wyomind\MassStockUpdate\Helper\Data::CSV)
                        ->addFieldDependence('field_delimiter', 'file_type', \Wyomind\MassStockUpdate\Helper\Data::CSV)
                        ->addFieldDependence('xml_xpath_to_product', 'file_type', \Wyomind\MassStockUpdate\Helper\Data::XML));


        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Source file');
    }

    public function getTabTitle()
    {
        return __('Source file');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

}
