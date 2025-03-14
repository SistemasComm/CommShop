<?php

namespace MestreMage\Correios\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class FunctionMode implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => __('Only Offline')),
            array('value' => 2, 'label' => __('Hybrid')),
            array('value' => 3, 'label' => __('Only Online')),
        );
    }
}
