<?php
namespace Emipro\Importexportattributeoption\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Index extends \Magento\Backend\App\Action {

    public function execute() 
    {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->create("Emipro\Importexportattributeoption\Helper\Data")->validateImportExportData();

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
