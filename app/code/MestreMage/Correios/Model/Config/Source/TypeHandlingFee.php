<?php

namespace MestreMage\Correios\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class TypeHandlingFee implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Valor Fixo')],
            ['value' => '1', 'label' => __('Valor Percentual')]
        ];
    }
}
