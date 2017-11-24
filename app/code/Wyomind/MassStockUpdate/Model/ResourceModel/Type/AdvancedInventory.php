<?php

namespace Wyomind\MassStockUpdate\Model\ResourceModel\Type;

class AdvancedInventory extends \Wyomind\MassStockUpdate\Model\ResourceModel\Type\AbstractResource
{

    public $fields;
    protected $_tableItems;
    protected $_stockIds = [];
    protected $_stockId;
    protected $_autoInc;
    protected $_substractedStocks = [];
    protected $_sum = [];
    protected $_pointOfSaleModel;
    protected $_moduleList = null;
    protected $_itemCollectionFactory;
    protected $_stockCollectionFactory;

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context,
            \Wyomind\Core\Helper\Data $coreHelper, \Wyomind\MassStockUpdate\Helper\Data $helperData,
            \Magento\Framework\Module\ModuleList $moduleList,
            \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $entityAttributeCollection,
            $connectionName = null)
    {
        $this->_moduleList = $moduleList;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if ($this->isAdvancedInventoryEnabled()) {
            $this->_itemCollectionFactory = $objectManager->create("\Wyomind\AdvancedInventory\Model\ResourceModel\Item\CollectionFactory");
            $this->_stockCollectionFactory = $objectManager->create("\Wyomind\AdvancedInventory\Model\ResourceModel\Stock\CollectionFactory");
        }

        parent::__construct($context, $coreHelper, $helperData, $entityAttributeCollection, $connectionName);
    }

    public function _construct()
    {

        $this->_init('advancedinventory_stock', 'id');
        $this->table = $this->getTable("advancedinventory_stock");
        $this->_tableItems = $this->getTable("advancedinventory_item");
    }

    public function getIndexes()
    {
        return ["cataloginventory_stock"];
    }

    public function beforeCollect($profile, $columns)
    {

        $sql = "SELECT MAX(id) AS INC FROM " . $this->_tableItems . " ;";
        $this->_autoInc = $this->_resources->getConnection("core_read")->fetchOne($sql);


        $stocks = $this->_itemCollectionFactory->create();
        foreach ($stocks as $s) {
            $this->_stockIds[$s->getProductId()] = $s->getId();
        };
    }

    public function collect($productId, $value, $strategy, $profile)
    {

        if (isset($this->_stockIds[$productId])) {
            $this->_stockId = $this->_stockIds[$productId];
        } else {
            $this->_autoInc++;
            $this->_stockId = $this->_autoInc;
        }

        if ($strategy["option"][0] == "manage_local_stock") {
            $val = $this->getValue($value);
            $this->queries[] = "REPLACE INTO `" . $this->_tableItems . "` values(" . $this->_stockId . " , " . $productId . ", '" . $val . "');";
        } else {
            $placeId = $strategy["option"][0];
            $field = $strategy["option"][1];
            if (!isset($this->fields[md5($productId)][$placeId])) {
                $this->fields[md5($productId)][$placeId] = array(
                    "product_id" => $productId,
                    "item_id" => $this->_stockId,
                    "place_id" => $placeId
                );
            }
            if (!isset($this->fields[md5($productId)][$placeId]['id'])) {
                $this->fields[md5($productId)][$placeId]["id"] = "IFNULL((SELECT `id` FROM " . $this->table . " AS a WHERE `place_id` = " . $placeId . " AND `product_id` = " . $productId . "), NULL)";
            }
            if (!isset($this->fields[md5($productId)][$placeId]["manage_stock"])) {
                $this->fields[md5($productId)][$placeId]["manage_stock"] = "IFNULL((SELECT `manage_stock` FROM `" . $this->table . "` AS `a` WHERE `place_id` = " . $placeId . " AND `product_id` = " . $productId . "), 1)";
            }
            if (!isset($this->fields[md5($productId)][$placeId]["quantity_in_stock"])) {
                $this->fields[md5($productId)][$placeId]["quantity_in_stock"] = "IFNULL((SELECT `quantity_in_stock` FROM `" . $this->table . "` AS `b` WHERE `place_id` = " . $placeId . " AND `product_id` = " . $productId . "), 0), ";
            }
            if (!isset($this->fields[md5($productId)][$placeId]["backorder_allowed"])) {
                $this->fields[md5($productId)][$placeId]["backorder_allowed"] = "IFNULL((SELECT `backorder_allowed` FROM `" . $this->table . "` AS `b` WHERE `place_id` = " . $placeId . " AND `product_id` = " . $productId . "), 0)";
            }
            if (!isset($this->fields[md5($productId)][$placeId]["use_config_setting_for_backorders"])) {
                $this->fields[md5($productId)][$placeId]["use_config_setting_for_backorders"] = "IFNULL((SELECT `use_config_setting_for_backorders` FROM `" . $this->table . "` AS `c` WHERE `place_id` = " . $placeId . " AND `product_id` = " . $productId . "), 1)";
            }

            if ($field == "quantity_in_stock") {
                if (!isset($this->_sum[md5($productId)])) {
                    $this->_sum[md5($productId)] = 0;
                }
                $this->_sum[md5($productId)]+=(float)$value;
            }
            $this->fields[md5($productId)][$placeId][$field] = $this->getValue($value);

            $this->_substractedStocks[$placeId] = "-IFNULL((SELECT `quantity_in_stock` FROM `" . $this->table . "` WHERE `place_id`=" . $placeId . " AND  `product_id`=" . $productId . " ),0)";
        }
    }

    public function prepareQueries($productId, $profile)
    {
        foreach ($this->fields[md5($productId)] as $warehouse) {


            $fields = [];
            $values = [];
            foreach ($warehouse as $field => $value) {

                $fields[] = "`" . $field . "`";
                $values[] = $value;
            }
            $this->queries[] = "REPLACE INTO `" . $this->table . "` (" . implode(",", $fields) . ") "
                    . " VALUES (" . implode(",", $values) . ");";
        }



        $totalQty = "(SELECT (IFNULL(SUM(`quantity_in_stock`),0) " . implode('', $this->_substractedStocks) . "+" . $this->_sum[md5($productId)] . ") FROM " . $this->table . " WHERE `product_id`=$productId)";
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $stock = $objectManager->get("\Wyomind\MassStockUpdate\Model\ResourceModel\Type\Stock");


        $stock->qtyField = $totalQty;
        $stock->fields["qty"] = $totalQty;
    }

    public function getDropdown()
    {
        $dropdown = [];
        /* Advanced Inventory */
        if ($this->isAdvancedInventoryEnabled()) {
            $attributes = [
                "Qty" => "quantity_in_stock",
                "Manage stock" => "manage_stock",
                "Backorders allowed" => "backorder_allowed",
                "Use config settings for backorders" => "use_config_settings_for_backorders"
            ];

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_pointOfSaleModel = $objectManager->get("\Wyomind\PointOfSale\Model\PointOfSale");

            $places = $this->_pointOfSaleModel->getPlaces();


            foreach ($places as $p) {
                $i = 0;
                foreach ($attributes as $name => $field) {
                    $dropdown[$p->getName()][$i]['label'] = $p->getName() . " - " . $name;
                    $dropdown[$p->getName()][$i]["id"] = "AdvancedInventory/" . $p->getId() . "/" . $field;
                    $dropdown[$p->getName()][$i]['style'] = "AdvancedInventory";
                    $i++;
                }
            }
        }
        return $dropdown;
    }

    public function isAdvancedInventoryEnabled()
    {
        $advancedInventory = $this->_moduleList->getOne("Wyomind_AdvancedInventory");
        return $advancedInventory != null;
    }

}
