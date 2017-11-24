<?php

namespace Wyomind\MassStockUpdate\Block\Adminhtml\Profiles\Edit\Tab;

class Cron extends \Magento\Backend\Block\Widget\Form\Generic
        implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry,
            \Magento\Framework\Data\FormFactory $formFactory, array $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('profile');
        $form = $this->_formFactory->create();
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getCronSettings()
    {
        $model = $this->_coreRegistry->registry('profile');
        return $model->getCronSettings();
    }

    public function getTabLabel()
    {
        return __('Scheduled tasks');
    }

    public function getTabTitle()
    {
        return __('Scheduled tasks');
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
