<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator MSI (System)
 */

namespace Amasty\StorePickupWithLocatorMSI\Api\Data;

interface LocationWithQtyInterface
{
    public const LOCATION = 'location';
    public const QTY = 'qty';

    /**
     * @return \Amasty\StorePickupWithLocatorMSI\Api\Data\LocationDataInterface
     */
    public function getLocation(): \Amasty\StorePickupWithLocatorMSI\Api\Data\LocationDataInterface;

    /**
     * @param \Amasty\StorePickupWithLocatorMSI\Api\Data\LocationDataInterface $location
     * @return void
     */
    public function setLocation(\Amasty\StorePickupWithLocatorMSI\Api\Data\LocationDataInterface $location): void;

    /**
     * @return int
     */
    public function getQty(): int;

    /**
     * @param int $qty
     * @return void
     */
    public function setQty(int $qty): void;
}
