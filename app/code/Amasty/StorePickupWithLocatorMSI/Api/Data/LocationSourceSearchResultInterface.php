<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator MSI (System)
 */

namespace Amasty\StorePickupWithLocatorMSI\Api\Data;

interface LocationSourceSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return \Amasty\StorePickupWithLocatorMSI\Api\Data\LocationWithQtyInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param \Amasty\StorePickupWithLocatorMSI\Api\Data\LocationWithQtyInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
