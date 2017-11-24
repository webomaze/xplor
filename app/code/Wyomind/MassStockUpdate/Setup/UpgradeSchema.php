<?php

/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Upgrade schema for Simple Google Shopping
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $installer = $setup;
        $installer->startSetup();
        // $context->getVersion() = version du module actuelle
        // 4.0.0 = version en cours d'installation
        if (version_compare($context->getVersion(), '5.0.02') < 0) {

            $tableName = $setup->getTable('massstockupdate_profiles');

            if ($setup->getConnection()->isTableExists($tableName) == true) {



                // webservice 
                $setup->getConnection()->addColumn(
                        $tableName, 'webservice_params', ['type' => Table::TYPE_TEXT, 'length' => 900, 'nullable' => true, "comment" => 'Webservice params']
                );
                $setup->getConnection()->addColumn(
                        $tableName, 'webservice_login', ['type' => Table::TYPE_TEXT, 'length' => 300, 'nullable' => true, "comment" => 'Webservice login']
                );
                $setup->getConnection()->addColumn(
                        $tableName, 'webservice_password', ['type' => Table::TYPE_TEXT, 'length' => 300, 'nullable' => true, "comment" => 'Webservice password']
                );

                $setup->getConnection()->addColumn(
                        $tableName, 'default_values', ['type' => Table::TYPE_TEXT, 'length' => 900, 'nullable' => true, "comment" => 'Default Values']
                );
                $setup->getConnection()->dropColumn($tableName, "auto_set_total");
            }

            $installer->endSetup();
        }
    }

}
