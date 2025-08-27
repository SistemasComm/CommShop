<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model\Source;

class ParameterType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'code', 'label' => __('Affiliate Code')],
            ['value' => 'id', 'label' => __('Affiliate ID')],
        ];
    }
}
