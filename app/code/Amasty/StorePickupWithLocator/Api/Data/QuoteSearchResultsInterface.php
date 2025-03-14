<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator for Magento 2
 */

namespace Amasty\StorePickupWithLocator\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface QuoteSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return QuoteInterface[]
     */
    public function getItems();

    /**
     * @param QuoteInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
