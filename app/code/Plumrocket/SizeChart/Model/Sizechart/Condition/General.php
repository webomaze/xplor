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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\SizeChart\Model\Sizechart\Condition;

class General extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendHelper;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->_backendHelper = $backendHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'current_page_url'  => __('Current Page URL'),
            'category_ids'      => __('Current Category Page'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * This value will define which operators will be available for this condition.
     *
     * Possible values are: string, numeric, date, select, multiselect, grid, boolean
     *
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'category_ids':
                return 'category';
        }
        return 'string';
    }

    /**
     * Value element type will define renderer for condition value element
     *
     * @see \Magento\Framework\Data\Form\Element
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Default operator input by type map getter
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            /*
             * '{}' and '!{}' are left for back-compatibility and equal to '==' and '!='
             */
            $this->_defaultOperatorInputByType['string'] = ['==', '!=', '{}', '!{}', '()', '!()'];
            $this->_defaultOperatorInputByType['multiselect'] = ['()', '!()'];
            $this->_defaultOperatorInputByType['category'] = ['()', '!()'];
            $this->_defaultOperatorInputByType['numeric'] = ['==', '!=', '>=', '>', '<=', '<'];
            $this->_arrayInputTypes[] = 'category';
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'category_ids':
                $url = 'catalog_rule/promo_widget/chooser/attribute/' . $this->getAttribute();
                $url .= '/form/' . ($this->getJsFormObject() ?: 'sizechart_conditions_fieldset' );
                break;
            default:
                break;
        }
        return $url !== false ? $this->_backendHelper->getUrl($url) : '';
    }


    /**
     * @return bool
     */
    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'category_ids':
                return true;
        }
        switch ($this->getInputType()) {
            case 'date':
                return true;
        }
        return false;
    }

    /**
     * Retrieve after element HTML
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'category_ids':
                $image = $this->_assetRepo->getUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' .
                $image .
                '" alt="" class="v-middle rule-chooser-trigger" title="' .
                __(
                    'Open Chooser'
                ) . '" /></a>';
        }
        return $html;
    }

    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                /*case 'category_ids':
                    $options = Mage::getModel('adminhtml/system_config_source_category')
                        ->toOptionArray();
                    break;*/

                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }
}
