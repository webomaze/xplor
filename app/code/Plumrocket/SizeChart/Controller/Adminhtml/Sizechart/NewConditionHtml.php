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

namespace Plumrocket\SizeChart\Controller\Adminhtml\Sizechart;

class NewConditionHtml extends \Plumrocket\SizeChart\Controller\Adminhtml\Sizechart
{
    /**
     * @var \Plumrocket\SizeChart\Model\SizechartFactory
     */
    protected $sizechartFactory;

    /**
     * NewConditionHtml constructor.
     *
     * @param \Magento\Backend\App\Action\Context          $context
     * @param \Plumrocket\SizeChart\Model\SizechartFactory $sizechartFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Plumrocket\SizeChart\Model\SizechartFactory $sizechartFactory
    ) {
        $this->sizechartFactory = $sizechartFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->_objectManager->create(
            $type
        )->setId(
            $id
        )->setType(
            $type
        )->setRule(
            $this->sizechartFactory->create()
        )->setPrefix(
            'conditions'
        );
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof \Magento\Rule\Model\Condition\AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}
