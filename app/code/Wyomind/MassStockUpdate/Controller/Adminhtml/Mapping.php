<?php
namespace Wyomind\MassStockUpdate\Controller\Adminhtml;
 
abstract class Mapping extends \Wyomind\MassStockUpdate\Controller\Adminhtml\AbstractController
{

    protected $_profileModelFactory = null;
    protected $_dataHelper = null;
    protected $_configHelper = null;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Wyomind\MassStockUpdate\Logger\Logger $logger,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Wyomind\MassStockUpdate\Model\ProfilesFactory $profileModelFactory,
        \Wyomind\MassStockUpdate\Helper\Data $dataHelper,
        \Wyomind\MassStockUpdate\Helper\Config $configHelper
    ) {
        parent::__construct($context, $logger, $resultForwardFactory, $resultRawFactory, $resultPageFactory);
        $this->_profileModelFactory = $profileModelFactory;
        $this->_dataHelper = $dataHelper;
        $this->_configHelper = $configHelper;
    }
}
