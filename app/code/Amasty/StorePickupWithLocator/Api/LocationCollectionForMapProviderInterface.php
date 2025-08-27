<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator for Magento 2
 */

namespace Amasty\StorePickupWithLocator\Api;

use Amasty\Storelocator\Model\ResourceModel\Location\Collection;

interface LocationCollectionForMapProviderInterface
{
    /**
     * @return Collection
     */
    public function getCollection(): Collection;
}
