<?php

/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Install Data needed for Simple Google Shopping
 */
class InstallData implements InstallDataInterface
{

    /**
     * @version 2.0.0
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function install(
    ModuleDataSetupInterface $setup, ModuleContextInterface $context
    )
    {

        $installer = $setup;
        $installer->startSetup();

        $sample = array(
            "id" => null,
            "sql" => 0,
            "sql_path" => "var/sample",
            "sql_file" => "xml_inventory.sql",
            "name" => "xml_inventory",
            "file_path" => "/var/sample/xml_inventory.xml",
            "field_delimiter" => "",
            "field_enclosure" => "",
            "auto_set_instock" => "0",
            "mapping" => "[{\"label\":\"Is In Stock\",\"id\":\"Stock/is_in_stock\",\"storeviews\":[]},{\"label\":\"Qty\",\"id\":\"Stock/qty\",\"storeviews\":[]}]",
            "cron_settings" => "{\"days\":[],\"hours\":[]}",
            "imported_at" => date("y-m-d H:i:s"),
            "identifier_offset" => "1",
            "use_custom_rules" => "0",
            "custom_rules" => "",
            "identifier" => "sku",
            "file_system_type" => "1",
            "use_sftp" => "",
            "ftp_host" => "",
            "ftp_login" => "",
            "ftp_password" => "",
            "ftp_active" => "",
            "ftp_dir" => "",
            "file_type" => "2",
            "xml_xpath_to_product" => "/products/product",
            "default_values" => "[]",
        );


        $installer->getConnection()->insert($installer->getTable("massstockupdate_profiles"), $sample);
        $installer->endSetup();
    }

}
