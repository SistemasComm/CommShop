<?php

namespace Clearsale\Base\Model\Config\Source;

class AddressAttribute implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 0,
                'label' => __('Street 1')
            ),
            array(
                'value' =>  1,
                'label' =>__('Street 2')
            ),
            array(
                'value' =>  2,
                'label' =>__('Street 3')
            ),
            array(
                'value' =>  3,
                'label' =>__('Street 4')
            )
        );
    }
}
