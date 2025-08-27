<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator MSI (System)
 */

namespace Amasty\StorePickupWithLocatorMSI\Model\ResourceModel;

use Amasty\StorePickupWithLocatorMSI\Api\Data\LocationSourceInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LocationSource extends AbstractDb
{
    public const TABLE_NAME = 'amasty_amlocator_location_inventory_source';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, LocationSourceInterface::ENTITY_ID);
    }
}
