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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\SizeChart\Block\Adminhtml\Sizechart\Grid\Filter;

class PlumrocketStore extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Store
{
    public function getCondition()
    {
        $value = $this->getValue();
        if ($value === null || $value == self::ALL_STORE_VIEWS) {
            return null;
        }
        if ($value == '_deleted_') {
            return ['null' => true];
        } else {
            return ['finset' => $value];
        }
    }
}