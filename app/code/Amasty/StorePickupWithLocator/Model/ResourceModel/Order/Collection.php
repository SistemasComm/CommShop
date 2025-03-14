<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator for Magento 2
 */

namespace Amasty\StorePickupWithLocator\Model\ResourceModel\Order;

use Amasty\StorePickupWithLocator\Api\Data\OrderInterface;
use Amasty\StorePickupWithLocator\Model\Order;
use Amasty\StorePickupWithLocator\Model\ResourceModel\Order as OrderResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection for Order
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = OrderInterface::ID;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(Order::class, OrderResource::class);
    }
}
