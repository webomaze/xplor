<?php
/*
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

namespace Plumrocket\SizeChart\Block;

use Magento\Store\Model\ScopeInterface;

class Style extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Plumrocket\SizeChart\Helper\Data
     */
    protected $_helper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Plumrocket\SizeChart\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getBorderColor()
    {
    	return $this->_scopeConfig->getValue(
            'prsizechart/button_settings/border_color',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getBackgroundColor()
    {
        return $this->_scopeConfig->getValue(
            'prsizechart/button_settings/background_color',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getTextColor()
    {
        return $this->_scopeConfig->getValue(
            'prsizechart/button_settings/text_color',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getIconButton() {

        $icon = $this->_scopeConfig->getValue(
            'prsizechart/button_settings/icon',
            ScopeInterface::SCOPE_STORE
        );

        if (!$icon) {
            return $this->getViewFileUrl('Plumrocket_SizeChart::images/rule.png');
        }

        return $this->_storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'sizechart/icon/' . $icon;

    }

    public  function isModuleEnable()
    {
        return $this->_helper->moduleEnabled();
    }

}