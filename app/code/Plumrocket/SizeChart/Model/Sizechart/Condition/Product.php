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

class Product extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        parent::loadAttributeOptions();
        $attributes = [];
        foreach($this->getAttributeOption() as $code => $label) {
            $attributes[$code] = __('Product') .' '. $label;
        }

        $this->setAttributeOption($attributes);
        return $this;
    }

    /**
     * Validate product attribute value for condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $result = false;
        if (!$model instanceof \Magento\Catalog\Model\Product) {
            $model = $model->getProduct();
        }

        if ($model && $model->getId()) {
            $result = parent::validate($model);
        }

        return $result;
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
            case 'sku':
            case 'category_ids':
                $url = 'catalog_rule/promo_widget/chooser/attribute/' . $this->getAttribute();
                $url .= '/form/' . ($this->getJsFormObject() ?: 'sizechart_conditions_fieldset' );
                break;
            default:
                break;
        }
        return $url !== false ? $this->_backendData->getUrl($url) : '';
    }
}