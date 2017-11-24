<?php
namespace Codazon\AjaxCartPro\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{	

	protected $_customerSession;	
	protected $collectionFactory;
	
	 public function __construct(
        \Magento\Customer\Model\Session $customerSession,		 
		\Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory  $collectionFactory
    ) {
        $this->_customerSession = $customerSession;		
		$this->collectionFactory = $collectionFactory; 
    }
	
	
	public function getWishlistCollection($currentCustomerId){
		
	
		$wishlistCollection = $this->collectionFactory->create();		
		$wishlistItems  = $wishlistCollection->addFieldToFilter('customer_id', $currentCustomerId);	
		$wishlistdatas = $wishlistItems->getData();
		$arrOfProductIds = array();			
		foreach($wishlistdatas as $wishlistdata){
			$wishlist_id =  $wishlistdata['wishlist_id'];
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			$tableName = $resource->getTableName('wishlist_item'); //gives table name with prefix
			 
			//Select Data from table
			$sql = "Select * FROM " . $tableName . " Where wishlist_id='$wishlist_id'";
			$results = $connection->fetchAll($sql);
			foreach($results as $result){
				
				$arrOfProductIds[] = $result['product_id'];
				
			}
			
		}
		
		return $arrOfProductIds;
		
		
	}
	
	
}