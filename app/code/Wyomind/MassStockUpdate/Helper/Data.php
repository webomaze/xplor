<?php

/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const LOCATION_MAGENTO = 1;
    const LOCATION_FTP = 2;
    const LOCATION_URL = 3;
    const LOCATION_WEBSERVICE = 4;
    const NO = 0;
    const YES = 1;
    const TMP_FOLDER = "/var/tmp/massstockupdate/";
    const TMP_FILE_PREFIX = "massstockupdate_";
    const TMP_FILE_EXT = "orig";
    const CSV = 1;
    const XML = 2;
   
    const MODULES = [ "Stock", "AdvancedInventory"];

    protected $_driverFileFactory = null;
    protected $_logger = null;
    protected $_attributeRepository = null;
    protected $_objectManager = null;
    protected $_storeManager = null;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
            \Magento\Framework\Filesystem\Driver\FileFactory $driverFileFactory,
            \Wyomind\MassStockUpdate\Logger\Logger $logger,
            \Magento\Store\Model\StoreManager $storeManager,
            \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
            \Magento\Framework\ObjectManager\ObjectManager $objectManager
    )
    {
        parent::__construct($context);
        $this->_driverFileFactory = $driverFileFactory;
        $this->_logger = $logger;

        $this->_attributeRepository = $attributeRepository;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
    }

    public function getData(
    $file, $fileType, $options, $customRules, $mapping, $limit = INF
    )
    {
        $data = [];
        $categories = [];
        $driverFile = $this->_driverFileFactory->create();

        $offset = $options['identifier_offset'] - 1;

        switch ($fileType) {
            case self::CSV:
                $inCh = $driverFile->fileOpen($file, 'r');
                $rowCounter = 0;
                while ($rowCounter < $limit && ($row = $driverFile->fileGetCsv($inCh, 0, $options['field_delimiter'])) != false) {
// move the identifier column at index 0
                    if (isset($row[$offset]) && $offset != 0) {
                        $bckp = $row[0];
                        $row[0] = $row[$offset];
                        $row[$offset] = $bckp;
                    }

                    if ($customRules['use_custom_rules']) {
                        $row = $this->applyCustomRules($row, $customRules['custom_rules'], $limit);
                    }

                    $data[] = $row;

                    $rowCounter++;
                }
                break;

            case self::XML:
                $xml = (new \SimpleXMLElement($driverFile->fileGetContents($file)))->xpath($options['xml_xpath_to_product']);

                $columns = [];
                $products = [];

                foreach ($xml as $product) {
                    $tmp = [];
                    foreach ($product as $key => $value) {
                        if (!in_array($key, $columns)) {
                            $columns[] = $key;
                        }
                        $tmp[$key] = (string) $value;
                    }
                    $products[] = $tmp;
                }
// move the identifier column at index 0
                if (isset($columns[$offset]) && $columns[$offset] != 1) {
                    $bckp = $columns[0];
                    $columns[0] = $columns[$offset];
                    $columns[$offset] = $bckp;
                }

                $data[] = $columns;

                $rowCounter = 1;
                foreach ($products as $product) {
                    if ($rowCounter >= $limit) {
                        break;
                    }
                    $tmp = [];
                    foreach ($columns as $column) {
                        if (array_key_exists($column, $product)) {
                            $tmp[] = trim(str_replace("\n", " ", $product[$column]));
                        } else {
                            $tmp[] = "";
                        }
                    }

                    if ($customRules['use_custom_rules']) {
                        $tmp = $this->applyCustomRules($tmp, $customRules['custom_rules'], $limit);
                    }

                    $data[] = $tmp;

                    $rowCounter++;
                }




                break;
        }
        $index = 0;

        $mapping = json_decode($mapping);

        if (json_last_error() === JSON_ERROR_NONE) {
            foreach ($mapping as $i => $column) {
                if ($column->id == "Category/mapping") {
                    $index = $i + 1;
                }
            };
        }



        if ($index) {
            foreach ($products as $product) {
                $p = array_values($product);

                $categories[] = $p[$index];
            }
        }

        return ['error' => false, 'data' => $data, 'categories' => array_unique($categories)];
    }

    public function execPhp(
    $script, &$row = null
    )
    {
// comment this line to use custom rules
// return false;
// uncomment below lines to use custom rules
        eval($script);
        return true;
    }

    public function applyCustomRules(
    $row, $customRules, $preview
    )
    {
        $before = $row;

        $customRules = str_replace('$cell', '$row', $customRules);
        try {
            $this->execPhp("?>" . $customRules, $row);
            if ($preview != INF) {
                $after = $row;
                foreach ($row as $k => $value) {
                    if (!isset($before[$k])) {
                        $before[$k] = true;
                        $after[$k] = false;
                    }
                    if ($before[$k] !== $after[$k]) {
                        if ($before[$k] == "") {
                            $before[$k] = __("null");
                        }
                        $row[$k] = "<span class='dynamic'>" . __("Dynamic value = ") . "<i> " . $after[$k] . "</i></span>" . "<br><span class='previous'>" . __("Original value = ") . " <i>" . $before[$k] . "</i></span>";
                    }
                }
            }
        } catch (\Exception $e) {
            return [$e->getMessage()];
        }
        return $row;
    }

    public function getJsonAttributes()
    {



        $dropdown = array();

        /* Store views */
        $stores = [];
        $w = 0;
        $g = 0;
        $s = 0;

        $websites = $this->_storeManager->getWebsites();
        $stores["label"] = __("Default value");
        $stores["value"] = "0";

        foreach ($websites as $website) {

            $stores["children"][$w]["label"] = $website->getName();
            $g = 0;
            $storegroups = $website->getGroupCollection();
            foreach ($storegroups as $storegroup) {
                $stores["children"][$w]["children"][$g]["label"] = $storegroup->getName();
                $s = 0;
                $storeviews = $storegroup->getStoreCollection();
                foreach ($storeviews as $storeview) {

                    $stores["children"][$w]["children"][$g]["children"][$s]["label"] = $storeview->getName();
                    $stores["children"][$w]["children"][$g]["children"][$s]["value"] = $storeview->getStoreId();
                    $s++;
                }
                $g++;
            }
            $w++;
        }

        $dropdown["storeviews"] = $stores;

        foreach (self::MODULES as $module) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get("\Wyomind\MassStockUpdate\Model\ResourceModel\Type\\" . $module);
            $options = $resource->getDropdown($this);
            $dropdown = array_merge($dropdown, $options);
        }



        /* OTHER MAPPING */
        $i = 0;
        $dropdown['Other'][$i]['label'] = __("Ignored column");
        $dropdown['Other'][$i]['id'] = "Ignored/ignored";
        $dropdown['Other'][$i]['style'] = "ignored";

        $i++;

        return json_encode($dropdown);
    }

    public function getFieldDelimiters()
    {
        return [
            ';' => ';',
            ',' => ',',
            '|' => '|',
            "\t" => '\tab',
        ];
    }

    public function getFieldEnclosures()
    {
        return [
            "none" => 'none',
            '"' => '"',
            '\'' => '\'',
        ];
    }

    public function getProductIdentifiers()
    {
        $typeCode = \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE;
        $searchCriteria = $this->_objectManager->create('\Magento\Framework\Api\SearchCriteria');
        $attributeList = $this->_attributeRepository->getList($typeCode, $searchCriteria)->getItems();

        $uId = [];
        $uId[] = ["label" => "ID", "value" => "entity_id"];
        foreach ($attributeList as $attribute) {
            if ($attribute->getIsUnique()) {
                $uId[] = ["label" => $attribute->getDefaultFrontendLabel(), "value" => $attribute->getAttributeCode()];
            }
        }
        return $uId;
    }

}
