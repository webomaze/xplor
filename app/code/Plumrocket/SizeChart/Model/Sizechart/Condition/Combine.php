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

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Plumrocket\SizeChart\Model\Sizechart\Condition\General
     */
    protected $_conditionGeneral;

    /**
     * @var \Plumrocket\SizeChart\Model\Sizechart\Condition\Product
     */
    protected $_conditionProduct;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param General $conditionGeneral
     * @param Product $conditionProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        General $conditionGeneral,
        Product $conditionProduct,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setType('Plumrocket\SizeChart\Model\Sizechart\Condition\Combine');

        $this->_conditionGeneral = $conditionGeneral;
        $this->_conditionProduct = $conditionProduct;
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $generalAttributes = $this->_conditionGeneral->loadAttributeOptions()->getAttributeOption();
        $general = [];
        foreach ($generalAttributes as $code=>$label) {
            $general[] = ['value'=>'Plumrocket\SizeChart\Model\Sizechart\Condition\General|'.$code, 'label'=>$label];
        }

        $productAttributes = $this->_conditionProduct->loadAttributeOptions()->getAttributeOption();
        $product = [];
        foreach ($productAttributes as $code=>$label) {
            $product[] = ['value'=>'Plumrocket\SizeChart\Model\Sizechart\Condition\Product|'.$code, 'label'=>$label];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            ['value'=>'Plumrocket\SizeChart\Model\Sizechart\Condition\Combine', 'label'=>__('Conditions combination')],
            ['label'=>__('General'), 'value'=>$general],
            ['label'=>__('Current Product Page'), 'value'=>$product],
        ]);

        return $conditions;
    }
}
