<?php

namespace Commshop\Adminorder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\ResourceModel\Order\Grid\CollectionFactory as OrderGridCollectionFactory;

class SyncOrderToGrid implements ObserverInterface
{
    protected $orderGridCollectionFactory;
    protected $orderGridRawCollectionFactory;  
    protected $resource;

    public function __construct(
        OrderGridCollectionFactory $orderGridCollectionFactory,
        OrderGridCollectionFactory $orderGridRawCollectionFactory,  
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->orderGridCollectionFactory = $orderGridCollectionFactory;
        $this->orderGridRawCollectionFactory = $orderGridRawCollectionFactory; 
        $this->resource = $resource;
    }

    public function execute(Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $createdBy = $order->getCreatedByAdminUserId();
        
        if ($createdBy) {
            $gridTable = $this->resource->getTableName('sales_order_grid');
            $bind = ['created_by_admin_user_id' => $createdBy];
            $where = ['entity_id = ?' => (int)$order->getId()];
            $this->resource->getConnection()->update($gridTable, $bind, $where);
        }
    }
    
}
