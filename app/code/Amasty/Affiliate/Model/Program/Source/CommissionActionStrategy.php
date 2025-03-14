<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model\Program\Source;

class CommissionActionStrategy extends AbstractOptions
{
    public const EXCLUDE = 0;
    public const INCLUDE = 1;

    public function toArray(): array
    {
        return [
            self::EXCLUDE => __('Exclude'),
            self::INCLUDE => __('Include')
        ];
    }
}
