<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator MSI (System)
 */

namespace Amasty\StorePickupWithLocatorMSI\Api;

interface LocationSourceManagementInterface
{
    /**
     * @param int $productId
     * @return \Amasty\StorePickupWithLocatorMSI\Api\Data\LocationSourceSearchResultInterface
     */
    public function getLocationsByProduct(
        int $productId
    ): \Amasty\StorePickupWithLocatorMSI\Api\Data\LocationSourceSearchResultInterface;
}
