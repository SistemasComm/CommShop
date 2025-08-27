<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator for Magento 2
 */

namespace Amasty\StorePickupWithLocator\Model\Location\MapProvider;

use Amasty\Storelocator\Model\ResourceModel\Location\Collection;
use Amasty\StorePickupWithLocator\Api\LocationCollectionForMapProviderInterface;
use Amasty\StorePickupWithLocator\Model\LocationProvider;

class LocationCollectionForMapProvider implements LocationCollectionForMapProviderInterface
{
    /**
     * @var LocationProvider
     */
    private $locationProvider;

    public function __construct(LocationProvider $locationProvider)
    {
        $this->locationProvider = $locationProvider;
    }

    /**
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return $this->locationProvider->getPreparedCollection(false);
    }
}
