<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ConfigurableProduct\Test\Unit\Block\Product\View\Type;
class ConfigurableTestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $context;
    /**
     * @var \Magento\Framework\Stdlib\ArrayUtils|\PHPUnit_Framework_MockObject_MockObject
     */
    private $arrayUtils;
    /**
     * @var \Magento\Framework\Json\EncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $jsonEncoder;
    /**
     * @var \Magento\ConfigurableProduct\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    private $helper;
    /**
     * @var \Magento\Catalog\Helper\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $product;
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $currentCustomer;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceCurrency;
    /**
     * @var \Magento\ConfigurableProduct\Model\ConfigurableAttributeData|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configurableAttributeData;
    /**
     * @var \Magento\Framework\Locale\Format|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeFormat;
    /**
     * @var \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable|\PHPUnit_Framework_MockObject_MockObject
     */
    private $block;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;
    protected function setUp()
    {
        $this->mockContextObject();
        $this->arrayUtils = $this->getMockBuilder(\Magento\Framework\Stdlib\ArrayUtils::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->jsonEncoder = $this->getMockBuilder(\Magento\Framework\Json\EncoderInterface::class)
            ->getMockForAbstractClass();
        $this->helper = $this->getMockBuilder(\Magento\ConfigurableProduct\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->product = $this->getMockBuilder(\Magento\Catalog\Helper\Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->currentCustomer = $this->getMockBuilder(\Magento\Customer\Helper\Session\CurrentCustomer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->priceCurrency = $this->getMockBuilder(\Magento\Framework\Pricing\PriceCurrencyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configurableAttributeData = $this->getMockBuilder(
            \Magento\ConfigurableProduct\Model\ConfigurableAttributeData::class
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->localeFormat = $this->getMockBuilder(\Magento\Framework\Locale\Format::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->block = new \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable(
            $this->context,
            $this->arrayUtils,
            $this->jsonEncoder,
            $this->helper,
            $this->product,
            $this->currentCustomer,
            $this->priceCurrency,
            $this->configurableAttributeData,
            [],
            $this->localeFormat
        );
    }
    /**
     * Check that getJsonConfig() method returns expected value
     */
    public function testGetJsonConfig()
    {
        $productId = 1;
        $amount = 10.50;
        $priceQty = 1;
        $percentage = 10;
        $amountMock = $this->getMockBuilder(\Magento\Framework\Pricing\Amount\AmountInterface::class)
            ->setMethods([
                'getValue',
                'getBaseAmount',
            ])
            ->getMockForAbstractClass();
        $amountMock->expects($this->any())
            ->method('getValue')
            ->willReturn($amount);
        $amountMock->expects($this->any())
            ->method('getBaseAmount')
            ->willReturn($amount);
        $priceMock = $this->getMockBuilder(\Magento\Framework\Pricing\Price\PriceInterface::class)
            ->setMethods([
                'getAmount',
            ])
            ->getMockForAbstractClass();
        $priceMock->expects($this->any())
            ->method('getAmount')
            ->willReturn($amountMock);
        $tierPrice = [
            'price_qty' => $priceQty,
            'price' => $amountMock,
        ];
        $tierPriceMock = $this->getMockBuilder(\Magento\Catalog\Pricing\Price\TierPriceInterface::class)
            ->setMethods([
                'getTierPriceList',
                'getSavePercent',
            ])
            ->getMockForAbstractClass();
        $tierPriceMock->expects($this->any())
            ->method('getTierPriceList')
            ->willReturn([$tierPrice]);
        $tierPriceMock->expects($this->any())
            ->method('getSavePercent')
            ->willReturn($percentage);
        $productMock = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productTypeMock = $this->getProductTypeMock($productMock);
        $priceInfoMock = $this->getMockBuilder(\Magento\Framework\Pricing\PriceInfo\Base::class)
            ->disableOriginalConstructor()
            ->getMock();
        $priceInfoMock->expects($this->any())
            ->method('getPrice')
            ->willReturnMap([
                ['regular_price', $priceMock],
                ['final_price', $priceMock],
                ['tier_price', $tierPriceMock],
            ]);
        $productMock->expects($this->any())
            ->method('getTypeInstance')
            ->willReturn($productTypeMock);
        $productMock->expects($this->any())
            ->method('getPriceInfo')
            ->willReturn($priceInfoMock);
        $productMock->expects($this->any())
            ->method('isSaleable')
            ->willReturn(true);
        $productMock->expects($this->any())
            ->method('getId')
            ->willReturn($productId);
        $this->registry->expects($this->any())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);
        $this->helper->expects($this->any())
            ->method('getOptions')
            ->with($productMock, [$productMock])
            ->willReturn([]);
        $this->product->expects($this->any())
            ->method('getSkipSaleableCheck')
            ->willReturn(true);
        $attributesData = [
            'attributes' => [],
            'defaultValues' => [],
        ];
        $this->configurableAttributeData->expects($this->any())
            ->method('getAttributesData')
            ->with($productMock, [])
            ->willReturn($attributesData);
        $this->localeFormat->expects($this->any())
            ->method('getPriceFormat')
            ->willReturn([]);
        $this->localeFormat->expects($this->any())
            ->method('getNumber')
            ->willReturnMap([
                [$amount, $amount],
                [$priceQty, $priceQty],
                [$percentage, $percentage],
            ]);
        $expectedArray = $this->getExpectedArray($productId, $amount, $priceQty, $percentage);
        $expectedJson = \Zend_Json::encode($expectedArray);
        $this->jsonEncoder->expects($this->once())
            ->method('encode')
            ->with($expectedArray)
            ->willReturn($expectedJson);
        $result = $this->block->getJsonConfig();
        $this->assertEquals($expectedJson, $result);
    }
    /**
     * Retrieve array with expected parameters for method getJsonConfig()
     *
     * @param $productId
     * @param $amount
     * @param $priceQty
     * @param $percentage
     * @return array
     */
    private function getExpectedArray($productId, $amount, $priceQty, $percentage)
    {
        $expectedArray = [
            'attributes' => [],
            'template' => '<%- data.price %>',
            'currencyFormat' => '%s',
            'optionPrices' => [
                $productId => [
                    'oldPrice' => [
                        'amount' => $amount,
                    ],
                    'basePrice' => [
                        'amount' => $amount,
                    ],
                    'finalPrice' => [
                        'amount' => $amount,
                    ],
                    'tierPrices' => [
                        0 => [
                            'qty' => $priceQty,
                            'price' => $amount,
                            'percentage' => $percentage,
                        ],
                    ],
                ],
            ],
            'priceFormat' => [],
            'prices' => [
                'oldPrice' => [
                    'amount' => $amount,
                ],
                'basePrice' => [
                    'amount' => $amount,
                ],
                'finalPrice' => [
                    'amount' => $amount,
                ],
            ],
            'productId' => $productId,
            'chooseText' => __('Choose an Option...'),
            'images' => [],
            'index' => [],
        ];
        return $expectedArray;
    }
    /**
     * Retrieve mocks of \Magento\ConfigurableProduct\Model\Product\Type\Configurable object
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $productMock
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getProductTypeMock(\PHPUnit_Framework_MockObject_MockObject $productMock)
    {
        $currencyMock = $this->getMockBuilder(\Magento\Directory\Model\Currency::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currencyMock->expects($this->any())
            ->method('getOutputFormat')
            ->willReturn('%s');
        $storeMock = $this->getMockBuilder(\Magento\Store\Api\Data\StoreInterface::class)
            ->setMethods([
                'getCurrentCurrency',
            ])
            ->getMockForAbstractClass();
        $storeMock->expects($this->any())
            ->method('getCurrentCurrency')
            ->willReturn($currencyMock);
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);
        $productTypeMock = $this->getMockBuilder(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productTypeMock->expects($this->any())
            ->method('getStoreFilter')
            ->with($productMock)
            ->willReturn($storeMock);
        $productTypeMock->expects($this->any())
            ->method('getUsedProducts')
            ->with($productMock)
            ->willReturn([$productMock]);
        return $productTypeMock;
    }
    /**
     * Create mocks for \Magento\Catalog\Block\Product\Context object
     *
     * @return void
     */
    protected function mockContextObject()
    {
        $this->storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->getMockForAbstractClass();
        $this->registry = $this->getMockBuilder(\Magento\Framework\Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context = $this->getMockBuilder(\Magento\Catalog\Block\Product\Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context->expects($this->any())
            ->method('getStoreManager')
            ->willReturn($this->storeManager);
        $this->context->expects($this->any())
            ->method('getRegistry')
            ->willReturn($this->registry);
    }
}