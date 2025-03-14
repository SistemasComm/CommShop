<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator MSI (System)
 */

namespace Amasty\StorePickupWithLocatorMSI\Api;

use Amasty\StorePickupWithLocatorMSI\Api\Data\LocationSourceInterface;

interface LocationSourceRepositoryInterface
{
    /**
     * @param LocationSourceInterface $locationSource
     * @return LocationSourceInterface
     */
    public function save(LocationSourceInterface $locationSource): LocationSourceInterface;

    /**
     * @param LocationSourceInterface $locationSource
     * @return bool true on success
     */
    public function delete(LocationSourceInterface $locationSource): bool;
}
