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

namespace Plumrocket\SizeChart\Model\Source;

class Sizechart extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $_options;
    protected $_model;

    public function __construct(\Plumrocket\SizeChart\Model\Sizechart $model)
    {
        $this->_model = $model;
    }

    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options[] = [
                'label' => __('Disabled'),
                'value' => -1,
            ];

            $this->_options[] = [
                'label' => __('Use Size Chart From Parent Category'),
                'value' => 0,
            ];

            $items = $this->_model->getCollection()
                ->addEnabledFilter()
                ->setOrder('name');

            foreach($items as $item) {
                $this->_options[] = [
                    'label' => $item->getName(),
                    'value' => $item->getId(),
                ];
            }
        }
        return $this->_options;
    }

}
