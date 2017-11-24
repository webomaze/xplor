<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbyBrand\Observer\Admin;

use Magento\Catalog\Model\Category\Attribute\Source\Page;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Event\ObserverInterface;

class OptionFormFeatured implements ObserverInterface
{
    /** @var Page */
    protected $page;

    /** @var Yesno */
    protected $yesno;

    protected $buildAfter;

    public function __construct(
        Page $page,
        Yesno $yesno,
        \Amasty\ShopbyBrand\Observer\Admin\OptionFormBuildAfter $buildAfter
    ) {
        $this->page = $page;
        $this->yesno = $yesno;
        $this->buildAfter = $buildAfter;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Form $form */
        $form = $observer->getData('form');

        $featuredFieldset = $form->addFieldset(
            'featured_fieldset',
            ['legend' => __('Slider Options'), 'class'=>'form-inline']
        );

        $featuredFieldset->addField(
            'is_featured',
            'select',
            [
                'name' => 'is_featured',
                'label' => __('Show in Brand Slider'),
                'title' => __('Show in Brand Slider'),
                'values' => $this->yesno
            ]
        );

        $featuredFieldset->addField(
            'slider_position',
            'text',
            [
                'name' => 'slider_position',
                'label' => __('Position in Slider'),
                'title' => __('Position in Slider')
            ]
        );

        $this->buildAfter->addOtherFieldset($observer);
    }
}
