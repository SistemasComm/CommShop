<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Pickup with Locator for Magento 2
 */

namespace Amasty\StorePickupWithLocator\Model;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class PickupDate
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;
    }

    public function getDateFormat(): string
    {
        return $this->timezone->getDateFormat(\IntlDateFormatter::SHORT);
    }
}
