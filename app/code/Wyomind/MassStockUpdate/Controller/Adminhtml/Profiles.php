<?php
namespace Wyomind\MassStockUpdate\Controller\Adminhtml;
 
abstract class Profiles extends \Wyomind\MassStockUpdate\Controller\Adminhtml\AbstractController
{

    protected $_coreRegistry = null;
    protected $_configHelper = null;
    protected $_directoryRead = null;
    protected $_parserHelper = null;
    protected $_cacheManager = null;
    


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Wyomind\MassStockUpdate\Logger\Logger $logger,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Model\Context $contextModel,
        \Magento\Framework\Registry $coreRegistry,
        \Wyomind\MassStockUpdate\Helper\Config $configHelper,
        \Magento\Framework\Filesystem\Directory\ReadFactory $directoryRead
       
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_configHelper = $configHelper;
        $this->_cacheManager = $contextModel->getCacheManager();
        $this->_directoryRead = $directoryRead->create("");
       
        parent::__construct($context, $logger, $resultForwardFactory, $resultRawFactory, $resultPageFactory);
    }
}
