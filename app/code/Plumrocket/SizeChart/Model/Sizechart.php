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

namespace Plumrocket\SizeChart\Model;

class Sizechart extends \Magento\Rule\Model\AbstractModel
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'Plumrocket_SizeChart';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'sizechart';

    /**
     * Check if conditions was erased
     *
     * @var bool
     */
    protected $_eraseConditions = false;

    /**
     * @var \Plumrocket\SizeChart\Model\Sizechart\Condition\CombineFactory
     */
    protected $_combineFactory;

    /**
     * @var \Magento\Rule\Model\Action\CollectionFactory
     */
    protected $_actionCollectionFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Plumrocket\SizeChart\Model\Sizechart\Condition\CombineFactory $combineFactory
     * @param \Magento\Rule\Model\Action\CollectionFactory $actionCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Plumrocket\SizeChart\Model\Sizechart\Condition\CombineFactory $combineFactory,
        \Magento\Rule\Model\Action\CollectionFactory $actionCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Plumrocket\SizeChart\Model\ResourceModel\Sizechart');
    }


    public function isEnabled()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }


    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }


    public function getAvailableDisplayTypes()
    {
        return [__('In Popup'), self::STATUS_ENABLED => __('On Page')];
    }

    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    public function beforeSave()
    {
        if ($this->getConditions()) {
            $conditions = $this->getConditions()->asArray();
            if (empty($conditions['conditions'][0])) {
                $this->_eraseConditions = true;
                $this->setConditionsSerialized('');
                $this->unsConditions();
            }
        }

        return parent::beforeSave();
    }

    public function getConditions()
    {
        if ($this->_eraseConditions) {
            return null;
        }

        return parent::getConditions();
    }
}
