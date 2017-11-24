<?php

namespace Wyomind\MassStockUpdate\Model\ResourceModel\Type;

abstract class AbstractResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    const ENABLE = ["true", "yes", "in stock", "enable", "enabled"];
    const DISABLE = ["false", "no", "out of stock", "disable", "disabled"];

    public $table;
    public $queries = array();
    public $entity_type_id;
    protected $_helperData = null;
    protected $_coreHelper = null;
    protected $_entityAttributeCollection = null;

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context,
            \Wyomind\Core\Helper\Data $coreHelper, \Wyomind\MassStockUpdate\Helper\Data $helperData,
            \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $entityAttributeCollection,
            $connectionName = null)
    {

        $this->_helperData = $helperData;
        $this->_coreHelper = $coreHelper;
        $this->_entityAttributeCollection = $entityAttributeCollection;

        parent::__construct($context, $connectionName);
        $read = $this->getConnection();
        $tableEet = $this->_resources->getTableName('eav_entity_type');
        $select = $read->select()->from($tableEet)->where('entity_type_code=\'catalog_product\'');
        $data = $read->fetchAll($select);
        $this->entity_type_id = $data[0]['entity_type_id'];
    }

    public function _construct()
    {
        return;
    }

    public function getIndexes()
    {
        return [];
    }

    public function beforeCollect($profile, $columns)
    {
        return;
    }

    public function collect($productId, $value, $strategy, $profile)
    {
        return;
    }

    public function prepareQueries($productId, $profile)
    {
        return $this->queries;
    }

    public function getValue($value)
    {
        if (in_array($value, self::ENABLE)) {
            return "1";
        } else if (in_array($value, self::DISABLE)) {
            return "0";
        }
        return (string) $value;
    }

    public function afterCollect()
    {
        $queries = [];
        foreach ($this->queries as $query) {
            $queries[] = str_replace("\n", " ", $query);
        }
        return implode("\n", $queries);
    }

    public function afterProcess($profile)
    {
        return;
    }

    public function getDropdown()
    {
        return [];
    }

    public function getAttributesList($fields = array("backend_type", "frontend_input", "attribute_code", "attribute_code"),
            $conditions = array(
        array("nin" => array("static")),
        array("nin" => array("media_image", "gallery")),
        array("nin" => array("image_label", "thumbnail_label", "small_image_label")),
        array("nin" => array("tax_class_id", "visibility", "status"))
    ), $and = true)
    {





        /*  Liste des  attributs disponible dans la bdd */

        $attributesList = $this->_entityAttributeCollection->create()
                ->setEntityTypeFilter($this->entity_type_id);
        if ($and) {
            foreach ($fields as $i => $field) {
                $attributesList->addFieldToFilter($field, $conditions[$i]);
            }
        } else {
            $attributesList->addFieldToFilter($fields, $conditions);
        }



        $data = $attributesList->addSetInfo()
                ->getData();

        usort($data, [$this, 'attributesSort']);


        return $data;
    }

    public function attributesSort(
    $a, $b
    )
    {
        return ($a['frontend_label'] < $b['frontend_label']) ? -1 : 1;
    }

}
