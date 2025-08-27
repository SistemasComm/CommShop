<?php

namespace MestreMage\PriceInstallment\Model\Source;

use Magento\Backend\App\Action;

class TypeInterest implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            'simple' => __('Simple Interest'),
            'compound' => __('Compound Interest'),
        ];
    }
}