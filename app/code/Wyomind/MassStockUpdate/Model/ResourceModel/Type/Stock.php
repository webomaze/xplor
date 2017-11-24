<?php

namespace Wyomind\MassStockUpdate\Model\ResourceModel\Type;

class Stock extends \Wyomind\MassStockUpdate\Model\ResourceModel\Type\AbstractResource
{

    protected $_backorders;
    protected $_minQty;
    public $fields;
    protected $_setStockStatus;
    public $qtyField = false;
    protected $_moduleList = null;

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context,
            \Wyomind\Core\Helper\Data $coreHelper, \Wyomind\MassStockUpdate\Helper\Data $helperData,
            \Magento\Framework\Module\ModuleList $moduleList,
            \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $entityAttributeCollection,
            $connectionName = null)
    {
        $this->_moduleList = $moduleList;

        parent::__construct($context, $coreHelper, $helperData, $entityAttributeCollection, $connectionName);
    }

    public function _construct()
    {

        $this->table = $this->getTable("cataloginventory_stock_item");
        $this->_backorders = $this->_coreHelper->getStoreConfig("cataloginventory/item_options/backorders");
        $this->_minQty = $this->_coreHelper->getStoreConfig("cataloginventory/item_options/min_qty");
    }

    public function getIndexes()
    {
        return ["cataloginventory_stock"];
    }

    public function collect($productId, $value, $strategy, $profile)
    {
        $field = $strategy['option'][0];
        if ($field == "qty") {
            $this->qtyField = $value;
        }
        $this->fields[$field] = "'" . $value . "'";


        return;
    }

    public function prepareQueries($productId, $profile)
    {
        if ($profile->getAutoSetInstock()) {

            if (is_numeric($this->qtyField) || is_string($this->qtyField)) {
                $field = $this->qtyField;
            } else {
                $field = "qty";
            }
            $this->fields["is_in_stock"] = " IF ($field > $this->_minQty OR (backorders>0 AND use_config_backorders=0) "
                    . " OR (use_config_backorders=1 AND $this->_backorders>0),1,0)";
        }
        if (is_integer($productId)) {
            $update = array();
            foreach ($this->fields as $field => $value) {
                $update[] = "`" . $field . "`=" . $this->getValue($value) . "";
            }

            $this->queries[] = "UPDATE `" . $this->table . "` SET \n"
                    . implode(",\n", $update) . " \n WHERE `product_id`=$productId AND `stock_id`= 1;";
        } else {
            $insert["fields"][] = "product_id";
            $insert["values"][] = $productId;
            $insert["fields"][] = "stock_id";
            $insert["values"][] = "1";
            foreach ($this->fields as $field => $value) {

                $insert["fields"][] = $field;
                $insert["values"][] = "" . $value . "";
            }
            $this->queries[] = "INSERT INTO `" . $this->table . "` (" . implode(",", $insert["fields"]) . " ) VALUES(" . implode(",", $insert["values"]) . ")";
        }

        
    }

    public function getDropdown()
    {
        $dropdown = [];
        /* STOCK MAPPING */
        $i = 0;
        $fields = $this->getStockFields();
        if ($this->isAdvancedInventoryEnabled()) {
            $dropdown['Stocks'][$i]['label'] = __("Multi stock enabled");
            $dropdown['Stocks'][$i]["id"] = "AdvancedInventory/manage_local_stock";
            $dropdown['Stocks'][$i]['style'] = "stock";
            $i++;
        }
        foreach ($fields as $field) {
            $dropdown['Stocks'][$i]['label'] = $field["comment"];
            $dropdown['Stocks'][$i]["id"] = "Stock/" . $field["field"];
            $dropdown['Stocks'][$i]['style'] = "stock";
            $i++;
        }
        return $dropdown;
    }

    public function getStockFields()
    {
        $read = $this->getConnection();
        $table = $this->_resources->getTableName(\Magento\CatalogInventory\Model\Stock\Item::ENTITY);

        $sql = "SHOW FULL COLUMNS FROM $table";

        $r = $read->fetchAll($sql);
        $fields = [];
        $exclude = ["item_id", "product_id", "stock_id"];

        foreach ($r as $data) {
            if (!in_array($data['Field'], $exclude)) {
                $fields[] = [
                    'field' => $data['Field'],
                    'comment' => $data['Comment']
                ];
            }
        }

        return $fields;
    }

    public function isAdvancedInventoryEnabled()
    {
        $advancedInventory = $this->_moduleList->getOne("Wyomind_AdvancedInventory");
        return $advancedInventory != null;
    }

}
