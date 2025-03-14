<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator MSI (System)
 */

namespace Amasty\StorePickupWithLocatorMSI\Block;

use Magento\Framework\View\Element\AbstractBlock;

class Location extends \Amasty\StorePickupWithLocator\Block\Location
{
    public const MAP_UPDATE_ROUTE = 'amstorepickupmsi/map/update';

    /**
     * @param array $params
     * @return string
     */
    public function getUpdateUrl($params = []): string
    {
        return $this->getUrl(self::MAP_UPDATE_ROUTE, $params);
    }
}
