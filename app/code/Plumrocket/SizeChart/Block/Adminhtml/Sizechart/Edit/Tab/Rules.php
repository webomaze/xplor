<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_SizeChart
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\SizeChart\Block\Adminhtml\Sizechart\Edit\Tab;
/**
 * Admin blog sizechart edit form rules tab
 */
class Rules extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_rendererFieldset;
    protected $_conditions;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
     * @param \Magento\Rule\Block\Conditions $conditions,
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Magento\Rule\Block\Conditions $conditions,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('current_model');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_authorization->isAllowed('Plumrocket_SizeChart::sizechart')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $isElementDisabled = false;

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('sizechart_');

        /**
         * Conditions
         */
        $renderer = $this->_rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('*/*/newConditionHtml/form/sizechart_conditions_fieldset')
        );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            ['legend' => __('Size Chart Display Rules (leave blank to show size chart based only on product and category settings)')]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            ['name' => 'conditions', 'label' => __('Conditions'), 'title' => __('Conditions')]
        )->setRule(
            $model
        )->setRenderer(
            $this->_conditions
        );

        $fieldset = $form->addFieldset('priority_fieldset', ['legend' => '']);

        $fieldset->addField(
            'conditions_is_main',
            'select',
            [
                'label' => __('Override Catalog And Product Settings'),
                'title' => __('Override Catalog And Product Settings'),
                'name' => 'conditions_is_main',
                // 'options' => $model->getAvailableStatuses(),
                'values'    => ['No', 'Yes'],
                'disabled' => $isElementDisabled,
                'after_element_html'  => '<div class="form-list"><p class="note">
                    If this option is enabled, display rules will replace all other size charts inherited or defined on catalog or product level.
                <br/></p></div>',
            ]
        );

        $fieldset->addField(
            'conditions_priority',
            'text',
            [
                'name' => 'conditions_priority',
                'label' => __('Rules Priority'),
                'title' => __('Rules Priority'),
                'disabled' => $isElementDisabled,
                'after_element_html'  => '<div class="form-list"><p class="note">When two or more size charts are linked to the same product, only size chart with higher priority will be displayed. The higher the number - the higher is the priority.<br/><br/></p></div>
                    ',
            ]
        );

        $this->_eventManager->dispatch('plumrocket_sizechart_edit_tab_rules_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Display Rules');
    }
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Display Rules');
    }
    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}