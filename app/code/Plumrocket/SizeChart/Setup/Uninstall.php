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

namespace Plumrocket\SizeChart\Setup;

/* Uninstall Size Chart */
class Uninstall extends \Plumrocket\Base\Setup\AbstractUninstall
{
	protected $_configSectionId = 'prsizechart';
	protected $_attributes = [
		\Magento\Catalog\Model\Product::ENTITY => ['pl_size_chart'],
		\Magento\Catalog\Model\Category::ENTITY => ['pl_size_chart'],
	];
	protected $_tables = ['plumrocket_sizechart'];
	protected $_pathes = ['/app/code/Plumrocket/SizeChart'];
}