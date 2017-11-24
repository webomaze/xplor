<?php

namespace Wyomind\MassStockUpdate\Block\Adminhtml\Profiles\Edit\Tab;

class Mapping extends \Magento\Backend\Block\Widget\Form\Generic
        implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_dataHelper = null;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry,
            \Wyomind\MassStockUpdate\Helper\Data $dataHelper,
            \Magento\Framework\Data\FormFactory $formFactory, array $data = []
    )
    {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {


        $model = $this->_coreRegistry->registry('profile');

        $form = $this->_formFactory->create();


        /* CUSTOM RULES */

        $fieldset = $form->addFieldset('massstockupdate_custom_rules', ['legend' => __('Custom rules')]);

        $values = [ '0' => __('No')];
        $comment = "";
        if ($this->_dataHelper->execPhp('')) {
            $values[1] = __('Yes');
        } else {
            $comment = __("In order to use custom rules, PHP feature must be enabled. Have a look to this FAQ to enable it : <a target='_blank' href='https://www.wyomind.com/magento2/mass-stock-update-magento.html?section=faq#I%20cant%20use%20the%20custom%20rules%20in%20the%20profile%20configuration'>https://www.wyomind.com/magento2/mass-stock-update-magento.html?section=faq#I%20cant%20use%20the%20custom%20rules%20in%20the%20profile%20configuration</a>");
        }


        $fieldset->addField(
                'use_custom_rules', 'select', [
            'name' => 'use_custom_rules',
            'label' => __('Use custom rules'),
            'class' => "update-preview",
            'note' => '',
            'values' => $values,
            'note' => $comment
                ]
        );

        $fieldset->addField(
                'custom_rules', 'textarea', [
            'name' => 'custom_rules',
            'label' => __('Rules'),
            'class' => "update-preview",
            'note' => '',
            'values' => [
                "1" => __('Yes'),
                '0' => __('No')
            ],
            "note" => __("Rules are based on php scripts. <br/>Column values can be updated through their variable names, e.g.: \$cell[1]=\$cell[2]+\$cell[3];")
                ]
        );

        $fieldset->addField(
                'mapping', "hidden", [
            'name' => 'mapping',
            'class' => 'debug'
                ]
        );
        $fieldset->addField(
                'default_values', "hidden", [
            'name' => 'default_values',
            'class' => 'debug'
                ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);


        return parent::_prepareForm();
    }

    public function getProfileId()
    {
        $model = $this->_coreRegistry->registry('profile');
        return $model->getId();
    }

    public function getTabLabel()
    {
        return __('Mapping & rules');
    }

    public function getTabTitle()
    {
        return __('Mapping & rules');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getAttributes()
    {
        return $this->_dataHelper->getJsonAttributes();
    }

}
