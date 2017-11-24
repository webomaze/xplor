<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbyBrand\Plugin;

use Magento\Framework\Registry;
use Amasty\ShopbyBrand\Block\Widget\BrandList;
use Magento\CatalogSearch\Model\Adapter\Aggregation\RequestCheckerComposite;

class MysqlAggregationCheckerPlugin
{
    /** @var  Registry */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param RequestCheckerComposite $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsApplicable(RequestCheckerComposite $subject, $result)
    {
        if ($this->registry->registry(BrandList::BRAND_LIST_REQUEST)) {
            return true;
        }

        return $result;
    }

}
