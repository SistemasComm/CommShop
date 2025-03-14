<?php

namespace MestreMage\Correios\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class WeightType implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'kg', 'label'=>__('Quilos')),
            array('value'=>'gr', 'label'=>__('Gramas')),
        );
    }
}
