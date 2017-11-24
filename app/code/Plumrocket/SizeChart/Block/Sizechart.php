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

namespace Plumrocket\SizeChart\Block;

use \Magento\Cms\Model\Block as CmsBlock;

class Sizechart extends \Magento\Framework\View\Element\Template
{
    const SIZE_ATTR_IDS_CACHE_KEY = 'prsizechart_size_attr_ids';

    static protected $_categories = [];
    static protected $_categoriesSC = [];

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    /**
     * @var \Plumrocket\SizeChart\Helper\Data
     */
    protected $helperData;
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $helperCatalog;
    /**
     * @var \Plumrocket\SizeChart\Model\SizechartFactory
     */
    protected $sizechartFactory;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @var \Plumrocket\SizeChart\Model\ResourceModel\Sizechart\CollectionFactory
     */
    protected $sizechartCollectionFactory;
    /**
     * @var \Plumrocket\SizeChart\Model\Sizechart\Space
     */
    protected $sizechartSpace;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory
     */
    protected $attrCollectionFactory;

    /**
     * @var string
     */
    protected $_template = 'Plumrocket_SizeChart::sizechart.phtml';

    /**
     * @var \Plumrocket\SizeChart\Model\Sizechart
     */
    protected $sizechart;

    /**
     * @var array|null
     */
    protected $keys = null;

    /**
     * Block\Sizechart constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context                                $context
     * @param \Plumrocket\SizeChart\Helper\Data                                     $helperData
     * @param \Plumrocket\SizeChart\Model\SizechartFactory                          $sizechartFactory
     * @param \Magento\Catalog\Model\CategoryFactory                                $categoryFactory
     * @param \Plumrocket\SizeChart\Model\ResourceModel\Sizechart\CollectionFactory $sizechartCollectionFactory
     * @param \Plumrocket\SizeChart\Model\Sizechart\Space                           $sizechartSpace
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory   $attrCollectionFactory
     * @param array                                                                 $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context                                  $context,
        \Plumrocket\SizeChart\Helper\Data                                       $helperData,
        \Plumrocket\SizeChart\Model\SizechartFactory                            $sizechartFactory,
        \Magento\Catalog\Model\CategoryFactory                                  $categoryFactory,
        \Plumrocket\SizeChart\Model\ResourceModel\Sizechart\CollectionFactory   $sizechartCollectionFactory,
        \Plumrocket\SizeChart\Model\Sizechart\Space                             $sizechartSpace,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory     $attrCollectionFactory,
        array $data = []
    ) {
        $this->coreRegistry                 = $context->getRegistry();
        $this->helperData                   = $helperData;
        $this->helperCatalog                = $context->getCatalogHelper();
        $this->sizechartFactory             = $sizechartFactory;
        $this->categoryFactory              = $categoryFactory;
        $this->sizechartCollectionFactory   = $sizechartCollectionFactory;
        $this->sizechartSpace               = $sizechartSpace;

        $this->attrCollectionFactory        = $attrCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getIdentities()
    {
        $product = $this->getProduct();
        $category = $this->getCategory();
        return [CmsBlock::CACHE_TAG . '_' .
            $this->_storeManager->getStore()->getId() . '_' .
            'size_chart_p' . $product->getId() . 'c' . ( $category ? $category->getId() : '' )
        ];
    }

    public function getContent()
    {
        if (!$this->hasData('content')) {
            $this->setData('content', false);
            if ($this->helperData->moduleEnabled()) {
                if ($sizechart = $this->getSizeChart()) {
                    $this->setData('content',
                        $this->helperCatalog->getPageTemplateProcessor()->filter($sizechart->getContent())
                    );
                }
            }
        }
        return $this->getData('content');
    }


    public function getSizeChart()
    {
        if ($this->sizechart === null) {
            $this->sizechart = false;

            if ($sizeChartId = $this->_getPlSizeChartIdByProduct($this->getProduct())) {
                if (is_object($sizeChartId) && $sizeChartId->getId()) {
                    $sizechart = $sizeChartId;
                } else {
                    $sizechart = $this->sizechartFactory->create()->load($sizeChartId);
                }
                if ($sizechart->isEnabled()) {
                    $storeId = $sizechart->getStoreId();
                    if ($storeId == '0' ||  in_array($this->_storeManager->getStore()->getId(), explode(',', $storeId))) {
                         $this->sizechart = $sizechart;
                    }
                }
            }
        }
        return $this->sizechart;
    }


    protected function _getPlSizeChartIdByProduct($product)
    {
        if ($product->getPlSizeChart() == -1) {
            return null;
        } else if ($sizechartByRules = $this->_getPlSizeChartByRules($product, true)) {
            return $sizechartByRules;
        } else if ($product->getPlSizeChart()) {
            return $product->getPlSizeChart();
        } else {
            if ($category = $this->getCategory()) {
                if ($sizeChartId = $this->_getPlSizeChartIdByCategory($category)) {
                    return ($sizeChartId > 0) ? $sizeChartId : null;
                }
            }

            $categories = $product->getCategoryCollection()->addAttributeToSelect('pl_size_chart');
            foreach($categories as $category) {
                $sizeChartId = $this->_getPlSizeChartIdByCategory($category);
                if ($sizeChartId > 0) {
                    return $sizeChartId;
                }
            }
        }

        if ($sizechartByRules = $this->_getPlSizeChartByRules($product)) {
            return $sizechartByRules;
        }

        return null;
    }


    protected function _getPlSizeChartIdByCategory($category)
    {
        $categoryId = (int)(is_object($category) ? $category->getId() : $category);
        if (!$categoryId) {
            return null;
        }

        if (isset(self::$_categoriesSC[$categoryId])) {
            return self::$_categoriesSC[$categoryId];
        }

        if (!is_object($category)) {
            if (isset(self::$_categories[$categoryId])) {
                $category = self::$_categories[$categoryId];
            } else {
                $category = $this->categoryFactory->create()->load($categoryId);
                self::$_categories[$categoryId] = $category;
            }
        } else {
            self::$_categories[$category->getId()] = $category;
        }


        if ($category->getPlSizeChart() == -1) {
            $sizeChartId = -1;
        } else if ($category->getPlSizeChart()) {
            $sizeChartId = $category->getPlSizeChart();
        } else {
            $sizeChartId = $this->_getPlSizeChartIdByCategory($category->getParentId());
        }

        return self::$_categoriesSC[$categoryId] = $sizeChartId;

    }


    protected function _getPlSizeChartByRules($product, $isMain = false)
    {
        $sizecharts = $this->sizechartCollectionFactory->create()
            ->addEnabledFilter()
            ->addFieldToFilter('conditions_serialized', ['neq' => ''])
            ->addFieldToFilter('conditions_is_main', (bool)$isMain)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('conditions_priority', 'DESC');

        $space = $this->sizechartSpace->getSpace($product);
        foreach ($sizecharts as $sizechart) {
            $_sizechart = $this->sizechartFactory->create()->load($sizechart->getId());
            if ($_sizechart->validate($space)) {
                return $_sizechart;
            }
        }

        return null;
    }


    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }

    public function getCategory()
    {
        if (!$this->hasData('category')) {
            $this->setData('category', $this->coreRegistry->registry('category'));
        }
        return $this->getData('category');
    }

    public function getLabel()
    {
        return $this->_scopeConfig->getValue('prsizechart/button_settings/label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getJsLayout()
    {
        $data = $this->jsLayout;
        $data['components']['prsizechart.js']['attributes']  = json_encode(
            [
                'id'     => $this->sizechart->getId(),
                'params' => $this->getSizeParams(),
                'move'   => !$this->getDiscardMovingButton(),
            ]
        );
        return json_encode($data);
    }

    public function getSizeParams()
    {
        if ($this->getDiscardMovingButton()) {
            return false;
        }
        $keys   = $this->getKeys();
        $result = $this->getAttributesIdsForSize($keys);

        return [
                'attributesIds' => $result,
                'keys' => $keys
            ];
    }

    public function getAttributesIdsForSize($keys)
    {
        $cacheKey = self::SIZE_ATTR_IDS_CACHE_KEY . md5(implode(',', $keys));
        $ids = $this->_cache->load($cacheKey);
        if ($ids) {
            return json_decode($ids);
        } else {
            $arrayForFilter = [];
            foreach ($keys as $key) {
                $arrayForFilter[] = ['like' => '%' . $key . '%'];
            }

            $attributeInfo = $this->attrCollectionFactory->create()->addFieldToFilter('attribute_code', $arrayForFilter);

            $ids = $attributeInfo->getAllIds();

            $this->_cache->save(json_encode($ids), $cacheKey, ['sizechart_attributeIds_cache'], 15*60);
            return  $ids;
        }
    }

    public function getKeys()
    {
        if ($this->keys === null) {
            $keys = $this->_scopeConfig->getValue(
                'prsizechart/additional/size_attributes',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $keys = explode(',', $keys);
            $result = [];
            foreach ($keys as $value) {
                $key = strtolower(trim($value));
                if ($key) {
                    $result[] = $key;
                }
            }

            if (!in_array('size', $result)) {
                $result[] = 'size';
            }
            $this->keys = $result;
        }

        return $this->keys;
    }
}