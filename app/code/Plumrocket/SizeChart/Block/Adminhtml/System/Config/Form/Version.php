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

namespace Plumrocket\SizeChart\Block\Adminhtml\System\Config\Form;

class Version extends \Plumrocket\Base\Block\Adminhtml\System\Config\Form\Version
{
    protected $_wikiLink = 'http://wiki.plumrocket.com/wiki/Magento_2_Size_Chart_v2.x_Extension';
    protected $_moduleName = 'Size Chart';

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return parent::render($element) . $this->_includeJs();
    }

    protected function _includeJs()
    {
        return '<script type="text/javascript" src="'. $this->getViewFileUrl('Plumrocket_SizeChart::js/jscolor/jscolor.js') . '"></script>';
    }
}