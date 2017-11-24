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

namespace Plumrocket\SizeChart\Helper;

class Data extends Main
{
    /**
     * @var string
     */
    protected $_configSectionId = 'prsizechart';
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var \Magento\Config\Model\Config
     */
    protected $config;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Config\Model\Config              $config
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Config\Model\Config $config
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->config = $config;
        parent::__construct($objectManager, $context);
    }


    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId.'/general/enabled', $store);
    }


    public function disableExtension()
    {
        $connection = $this->resourceConnection->getConnection('core_write');
        $connection->delete($this->resourceConnection->getTableName('core_config_data'),
            [$connection->quoteInto('path = ?', $this->_configSectionId.'/general/enabled')]
        );

        $this->config->setDataByPath($this->_configSectionId.'/general/enabled', 0);
        $this->config->save();
    }

}
