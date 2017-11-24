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

namespace Plumrocket\SizeChart\Controller\Adminhtml\Sizechart;

class Save extends \Plumrocket\SizeChart\Controller\Adminhtml\Sizechart
{
	protected function _beforeSave($model, $request)
    {
    	$data = $request->getParams();
    	if (!empty($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }
        unset($data['rule']);

        if (is_array($data['stores'])) {
            if (in_array('0', $data['stores']) || in_array('', $data['stores'])) {
                $data['store_id'] = '0';
            } elseif (is_array($data['stores'])) {
                $data['store_id'] = implode(',', $data['stores']);
            }
        } else {
            $data['store_id'] = $data['stores'] ? $data['stores'] : 0;
        }
        unset($data['stores']);

        $model->loadPost($data);
    }
}
