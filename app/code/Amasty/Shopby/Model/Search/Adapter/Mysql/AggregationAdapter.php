<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Model\Search\Adapter\Mysql;

use Amasty\Shopby\Model\Adapter\Mysql\Aggregation\GroupDataProviderFactory;
use Magento\Framework\Search\RequestInterface;

class AggregationAdapter
{
    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Mapper
     */
    protected $mapper;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory
     */
    protected $temporaryStorageFactory;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Container
     */
    protected $aggregationContainer;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderContainer
     */
    protected $dataProviderContainer;

    /**
     * @var GroupDataProviderFactory
     */
    protected $groupDataProviderFactory;

    public function __construct(
        \Magento\Framework\Search\Adapter\Mysql\Mapper $mapper,
        \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory,
        \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Container $aggregationContainer,
        \Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderContainer $dataProviderContainer,
        GroupDataProviderFactory $groupDataProvider
    ) {
        $this->mapper = $mapper;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->aggregationContainer = $aggregationContainer;
        $this->dataProviderContainer = $dataProviderContainer;
        $this->groupDataProviderFactory = $groupDataProvider;
    }

    /**
     * @param RequestInterface $request
     * @param $attributeCode
     * @return array
     */
    public function getBucketByRequest(RequestInterface $request, $attributeCode)
    {
        $query = $this->mapper->buildQuery($request);
        $temporaryStorage = $this->temporaryStorageFactory->create();
        $documentsTable = $temporaryStorage->storeDocumentsFromSelect($query);
        $dataProvider = $this->dataProviderContainer->get($request->getIndex());

        $bucketAggregation = $request->getAggregation();
        $attributeCode = $attributeCode . "_bucket";

        $currentBucket = null;
        foreach ($bucketAggregation as $requestBucket) {
            if ($requestBucket->getName() == $attributeCode) {
                $currentBucket = $requestBucket;
                break;
            }
        }

        if (is_null($currentBucket)) {
            return [];
        }

        $aggregationBuilder = $this->aggregationContainer->get($currentBucket->getType());

        $responseBucket = $aggregationBuilder->build(
            $dataProvider,
            $request->getDimensions(),
            $currentBucket,
            $documentsTable
        );

        return $responseBucket;
    }

    /**
     * @param RequestInterface $request
     * @param $groups
     * @param $attributeCode
     * @return array
     */
    public function getBucketByRequestGroup(
        RequestInterface $request,
        $groups,
        $attributeCode
    ) {
        $query = $this->mapper->buildQuery($request);

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $documentsTable = $temporaryStorage->storeDocumentsFromSelect($query);
        $dataProvider = $this->groupDataProviderFactory->create();
        $buckets = $request->getAggregation();
        $attributeCode = $attributeCode . "_bucket";

        $currentBucket = null;
        foreach ($buckets as $bucket) {
            if ($bucket->getName() == $attributeCode) {
                $currentBucket = $bucket;
                break;
            }
        }

        if (is_null($currentBucket)) {
            return [];
        }
        $aggregation = [];
        foreach ($groups as $code => $group) {
            $dimensions = $request->getDimensions();
            $dimensions['groups'] = $group;
            $select = $dataProvider->getDataSet($currentBucket, $dimensions, $documentsTable);
            $data = $dataProvider->execute($select);
            $count = $this->calcProducts($data);
            if ($count) {
                $aggregation[$code] = ['value' => $code, 'count' => $count];
            }
        }

        return $aggregation;
    }

    /**
     * @param $data
     * @return int
     */
    protected function calcProducts($data)
    {
        $array = [];
        $count = 0;
        foreach ($data as $value) {
            if (!in_array($value['entity_id'], $array)) {
                $array[] = $value['entity_id'];
                $count++;
            }
        }

        return $count;
    }
}
