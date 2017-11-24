<?php
namespace Mageplaza\Productslider\Block;
/**
 * Class FeaturedProducts
 * @package Mageplaza\Productslider\Block
 */
class BestSellerProducts extends \Mageplaza\Productslider\Block\AbstractSlider
{
	/**
	 * get collection of feature products
	 * @return mixed
	 */		 	 
	public function getProductCollection()
	{
		$collection = $this->_collectionFactory->create()->setModel('Magento\Catalog\Model\Product');        		return $collection;
	}

	public function getProductCacheKey()
	{
		return 'mageplaza_product_slider_bestseller';
	}


}
