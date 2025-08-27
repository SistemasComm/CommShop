<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator for Magento 2
 */

namespace Amasty\StorePickupWithLocator\Api;

use Amasty\StorePickupWithLocator\Api\Data\QuoteInterface;

interface GuestLocationPickupValuesInterface
{
    /**
     * @deprecated
     * @param string $cartId
     * @param int $locationId
     * @param string|null $date
     * @param string|null $timePeriod
     *
     * @return mixed
     */
    public function saveSelectedPickupValues(
        $cartId,
        $locationId,
        $date = null,
        $timePeriod = null
    );

    /**
     * @param string $cartId
     * @param \Amasty\StorePickupWithLocator\Api\Data\QuoteInterface $quotePickupData
     * @return bool
     */
    public function saveSelectedPickupData(
        string $cartId,
        QuoteInterface $quotePickupData
    ): bool;
}
