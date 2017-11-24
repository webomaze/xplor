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

namespace Plumrocket\SizeChart\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        $tableName = $setup->getTable('plumrocket_sizechart');

        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection->addColumn(
                    $tableName,
                    'conditions_is_main',
                    ['type' => Table::TYPE_BOOLEAN, 'nullable' => false, 'default' => 0, 'comment' => 'Override Catalog And Product Settings']
                );
                $connection->addColumn(
                    $tableName,
                    'conditions_priority',
                    ['type' => Table::TYPE_INTEGER, 'length' => 11, 'unsigned' => true, 'nullable' => false, 'comment' => 'Rules Priority']
                );
                $connection->addColumn(
                    $tableName,
                    'conditions_serialized',
                    ['type' => Table::TYPE_TEXT, 'length' => '2M', 'nullable' => false, 'comment' => 'Conditions Serialized']
                );
            }
        }

        if (version_compare($context->getVersion(), '2.1.2', '<')) {
            $connection->addColumn(
                $tableName,
                'store_id',
                ['type' => Table::TYPE_INTEGER, 'length' => 3, 'unsigned' => true, 'nullable' => false, 'comment' => 'Store ID']
            );
            $connection->addIndex(
                $tableName,
                $setup->getIdxName($tableName, ['store_id']),
                ['store_id']
            );
        }

         if (version_compare($context->getVersion(), '2.2.2', '<')) {
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection->changeColumn(
                    $tableName,
                    'store_id',
                    'store_id',
                    ['type' => Table::TYPE_TEXT, 'length' => 255, 'nullable' => false, 'default' => '0', 'comment' => 'Store ID']
                );
            }
         }

        $setup->endSetup();
    }
}
