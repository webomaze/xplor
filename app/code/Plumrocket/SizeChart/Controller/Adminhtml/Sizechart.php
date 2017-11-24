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

namespace Plumrocket\SizeChart\Controller\Adminhtml;

class Sizechart extends \Plumrocket\Base\Controller\Adminhtml\Actions
{
	const ADMIN_RESOURCE = 'Plumrocket_SizeChart::sizechart';

	protected $_formSessionKey  = 'sizechart_form_data';

    protected $_modelClass      = 'Plumrocket\SizeChart\Model\Sizechart';
    protected $_activeMenu     = 'Plumrocket_SizeChart::sizechart';
    protected $_objectTitle     = 'Size Chart';
    protected $_objectTitles    = 'Size Charts';

    protected $_statusField     = 'status';

}
