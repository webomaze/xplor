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
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\SizeChart\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Plumrocket\SizeChart\Model\SizechartFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * @var \Plumrocket\SizeChart\Model\SizechartFactory
     */
    private $_sizechartFactory;

    /**
     * Constructor
     * @param SizechartFactory $sizechartFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory, SizechartFactory $sizechartFactory)
    {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_sizechartFactory = $sizechartFactory;
    }

    /**
     * Install Data
     * @param  ModuleDataSetupInterface $setup
     * @param  ModuleContextInterface   $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
         */

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'pl_size_chart',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Size Chart',
                'input' => 'select',
                'class' => '',
                'source' => 'Plumrocket\SizeChart\Model\Source\Sizechart',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => 0,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'position' => 260
            ]
        );

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $attributeSetId   = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'General Information');

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'pl_size_chart',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Size Chart',
                'input' => 'select',
                'class' => '',
                'source' => 'Plumrocket\SizeChart\Model\Source\Sizechart',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => 0,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'position' => 260
            ]
        );

        $eavSetup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'pl_size_chart', '260');

        $sizecharts = [
            [
                'name' => 'Men\'s Shoes',
                'display_type' => 0,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'content' => '<div class="window-title"><h3>Men\'s Shoes</h3></div>'."\r\n".
                    '<table class="table_size">'."\r\n".
                    '<tbody>'."\r\n".
                    '<tr><th class="header-cell cell">Men US</th><th class="header-cell cell">EU</th><th class="header-cell cell">UK</th><th class="header-cell cell">CN</th></tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">5</td>'."\r\n".
                    '<td class="cell">38</td>'."\r\n".
                    '<td class="cell">4</td>'."\r\n".
                    '<td class="cell">38</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">5.5</td>'."\r\n".
                    '<td class="cell">38.5</td>'."\r\n".
                    '<td class="cell">4.5</td>'."\r\n".
                    '<td class="cell">39</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">6</td>'."\r\n".
                    '<td class="cell">39</td>'."\r\n".
                    '<td class="cell">5</td>'."\r\n".
                    '<td class="cell">39.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">6.5</td>'."\r\n".
                    '<td class="cell">39.5</td>'."\r\n".
                    '<td class="cell">5.5</td>'."\r\n".
                    '<td class="cell">40</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">7</td>'."\r\n".
                    '<td class="cell">40</td>'."\r\n".
                    '<td class="cell">6</td>'."\r\n".
                    '<td class="cell">41</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">7.5</td>'."\r\n".
                    '<td class="cell">40.5</td>'."\r\n".
                    '<td class="cell">6.5</td>'."\r\n".
                    '<td class="cell">41.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">8</td>'."\r\n".
                    '<td class="cell">41</td>'."\r\n".
                    '<td class="cell">7</td>'."\r\n".
                    '<td class="cell">42</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">8.5</td>'."\r\n".
                    '<td class="cell">41.5</td>'."\r\n".
                    '<td class="cell">7.5</td>'."\r\n".
                    '<td class="cell">43</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">9</td>'."\r\n".
                    '<td class="cell">42</td>'."\r\n".
                    '<td class="cell">8</td>'."\r\n".
                    '<td class="cell">43.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">9.5</td>'."\r\n".
                    '<td class="cell">42.5</td>'."\r\n".
                    '<td class="cell">8.5</td>'."\r\n".
                    '<td class="cell">44</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">10</td>'."\r\n".
                    '<td class="cell">43</td>'."\r\n".
                    '<td class="cell">9</td>'."\r\n".
                    '<td class="cell">44.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">10.5</td>'."\r\n".
                    '<td class="cell">43.5</td>'."\r\n".
                    '<td class="cell">9.5</td>'."\r\n".
                    '<td class="cell">45</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">11</td>'."\r\n".
                    '<td class="cell">44</td>'."\r\n".
                    '<td class="cell">10</td>'."\r\n".
                    '<td class="cell">45.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td class="cell">12</td>'."\r\n".
                    '<td class="cell">44.5</td>'."\r\n".
                    '<td class="cell">10.5</td>'."\r\n".
                    '<td class="cell">46</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td colspan="4">'."\r\n".
                    '<p><strong><strong>Fiercely Fitted = Lorem Ipsum.<br /></strong></strong>Please check the measurement chart carefully.</p>'."\r\n".
                    '</td>'."\r\n".
                    '</tr>'."\r\n".
                    '</tbody>'."\r\n".
                    '</table>'
            ],
            [
                'name' => 'Women\'s Dresses',
                'display_type' => 0,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'content' => '<div class="window-title"><h3>Women\'s Dresses</h3></div>'."\r\n".
                    '<table class="table_size">'."\r\n".
                    '<tbody>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>US SIZE</td>'."\r\n".
                    '<td colspan="2">BUST</td>'."\r\n".
                    '<td colspan="2">WAIST</td>'."\r\n".
                    '<td colspan="2">HIPS</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>&nbsp;</td>'."\r\n".
                    '<td>INCHES</td>'."\r\n".
                    '<td>CM</td>'."\r\n".
                    '<td>INCHES</td>'."\r\n".
                    '<td>CM</td>'."\r\n".
                    '<td>INCHES</td>'."\r\n".
                    '<td>CM</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>0</td>'."\r\n".
                    '<td>30</td>'."\r\n".
                    '<td>76</td>'."\r\n".
                    '<td>22.75</td>'."\r\n".
                    '<td>58</td>'."\r\n".
                    '<td>32.75</td>'."\r\n".
                    '<td>83.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>2</td>'."\r\n".
                    '<td>30</td>'."\r\n".
                    '<td>76</td>'."\r\n".
                    '<td>22.75</td>'."\r\n".
                    '<td>58</td>'."\r\n".
                    '<td>32.75</td>'."\r\n".
                    '<td>83.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>4</td>'."\r\n".
                    '<td>31</td>'."\r\n".
                    '<td>78.5</td>'."\r\n".
                    '<td>23.75</td>'."\r\n".
                    '<td>60.5</td>'."\r\n".
                    '<td>33.75</td>'."\r\n".
                    '<td>86</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>6</td>'."\r\n".
                    '<td>32</td>'."\r\n".
                    '<td>81</td>'."\r\n".
                    '<td>24.75</td>'."\r\n".
                    '<td>63</td>'."\r\n".
                    '<td>34.75</td>'."\r\n".
                    '<td>88.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>8</td>'."\r\n".
                    '<td>34</td>'."\r\n".
                    '<td>86</td>'."\r\n".
                    '<td>26.75</td>'."\r\n".
                    '<td>68</td>'."\r\n".
                    '<td>36.75</td>'."\r\n".
                    '<td>93.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>10</td>'."\r\n".
                    '<td>36</td>'."\r\n".
                    '<td>91</td>'."\r\n".
                    '<td>28.75</td>'."\r\n".
                    '<td>73</td>'."\r\n".
                    '<td>38.75</td>'."\r\n".
                    '<td>98.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>12</td>'."\r\n".
                    '<td>38</td>'."\r\n".
                    '<td>96</td>'."\r\n".
                    '<td>30.75</td>'."\r\n".
                    '<td>78</td>'."\r\n".
                    '<td>40.75</td>'."\r\n".
                    '<td>103.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>14</td>'."\r\n".
                    '<td>40</td>'."\r\n".
                    '<td>101</td>'."\r\n".
                    '<td>32.75</td>'."\r\n".
                    '<td>83</td>'."\r\n".
                    '<td>42.75</td>'."\r\n".
                    '<td>108.5</td>'."\r\n".
                    '</tr>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>16</td>'."\r\n".
                    '<td>43</td>'."\r\n".
                    '<td>108.5</td>'."\r\n".
                    '<td>35.75</td>'."\r\n".
                    '<td>90.5</td>'."\r\n".
                    '<td>45.75</td>'."\r\n".
                    '<td>116</td>'."\r\n".
                    '</tr>'."\r\n".
                    '</tbody>'."\r\n".
                    '</table>'."\r\n".
                    '<h3>How to measure</h3>'."\r\n".
                    '<p>To choose the correct size for you, measure your body as follows:</p>'."\r\n".
                    '<table class="measure">'."\r\n".
                    '<tbody>'."\r\n".
                    '<tr>'."\r\n".
                    '<td>'."\r\n".
                    '<p>1. BUST<br />Measure around fullest part</p>'."\r\n".
                    '<p>2. WAIST<br />Measure around natural waistline</p>'."\r\n".
                    '<p>3. HIPS<br />Measure 20cm down from the natural waistline</p>'."\r\n".
                    '</td>'."\r\n".
                    '<td><img src="{{view url="Plumrocket_SizeChart::images/sizechart_womens_dresses.png"}}" alt="" /></td>'."\r\n".
                    '</tr>'."\r\n".
                    '</tbody>'."\r\n".
                    '</table>'
            ]
        ];
        foreach ($sizecharts as $data) {
            $this->_sizechartFactory->create()->setData($data)->save();
        }
    }

}
