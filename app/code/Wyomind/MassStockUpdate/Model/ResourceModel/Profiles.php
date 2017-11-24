<?php

namespace Wyomind\MassStockUpdate\Model\ResourceModel;

class Profiles extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('massstockupdate_profiles', 'id');
    }
}
