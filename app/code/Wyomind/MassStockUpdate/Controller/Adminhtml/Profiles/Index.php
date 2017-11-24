<?php
namespace Wyomind\MassStockUpdate\Controller\Adminhtml\Profiles;
 
class Index extends \Wyomind\MassStockUpdate\Controller\Adminhtml\Profiles
{
    
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu("Magento_Backend::system_convert");
        $resultPage->getConfig()->getTitle()->prepend(__('Mass Stock Update > Profiles'));
        $resultPage->addBreadcrumb(__('Mass Stock Update'), __('Mass Stock Update'));
        return $resultPage;
    }
}
