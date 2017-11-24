<?php

namespace Wyomind\MassStockUpdate\Controller\Adminhtml\Mapping;

class Refresh extends \Wyomind\MassStockUpdate\Controller\Adminhtml\Mapping
{

    public function execute()
    {
        $jsonAttributes = $this->_dataHelper->getJsonAttributes($this->getRequest());
        $jsonData = json_encode($this->_profileModelFactory->create()->load($this->getRequest()->getParam('id'))->getImportData($this->getRequest(), $this->_configHelper->getSettingsNbPreview()));
        return $this->getResponse()->representJson('{"mapping":' . $jsonAttributes . ',"data":' . $jsonData . '}');
    }
}
