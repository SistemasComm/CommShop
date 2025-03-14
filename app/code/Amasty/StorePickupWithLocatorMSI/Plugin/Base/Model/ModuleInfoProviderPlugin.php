<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator MSI (System)
 */

namespace Amasty\StorePickupWithLocatorMSI\Plugin\Base\Model;

use Amasty\Base\Model\ModuleInfoProvider;

class ModuleInfoProviderPlugin
{
    /**
     * @param ModuleInfoProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetRestrictedModules(ModuleInfoProvider $subject, $result): array
    {
        array_push($result, 'Amasty_StorePickupWithLocatorMSI');

        return $result;
    }
}
