<?php
namespace Emipro\Importexportattributeoption\Block\Adminhtml;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class Importexport extends \Magento\Framework\View\Element\Template {

    protected $_eavConfig;
    protected $formKey;
    protected $_filesystem;
    protected $_uploadFactory;
    protected $_httpFactory;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, CollectionFactory $collectionFactory, \Magento\Framework\Data\Form\FormKey $formKey) {
        $this->_eavConfig = $collectionFactory;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getExporturl() {
        return $this->_urlBuilder->getUrl("importexportattributeoption/index/export");
    }

    public function getImporturl() {
        return $this->_urlBuilder->getUrl("importexportattributeoption/index/import");
    }

    public function getSelectAttributes() {
        $options = array();
        $mod = $this->_eavConfig->create();
        foreach ($mod as $item) {
            if ($item->getFrontendInput() == "select" || $item->getFrontendInput() == "multiselect") {
                array_push($options, array("label" => $item->getFrontendLabel(), "code" => $item->getAttributeId()));
            }
        }
        return $options;
    }

    public function getFormKey() {
        return $this->formKey->getFormKey();
    }

}
