<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model\Program\Source;

use Magento\Framework\Data\OptionSourceInterface;

abstract class AbstractOptions implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $options = [];

        foreach ($this->toArray() as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $options;
    }

    abstract public function toArray(): array;
}
