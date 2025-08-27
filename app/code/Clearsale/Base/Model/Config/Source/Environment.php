<?php

namespace Clearsale\Base\Model\Config\Source;

class Environment implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return array(
            array(
                'label' => __('Production'),
                'value' => 1
            ),
            array(
                'label' => __('Homolog'),
                'value' => 0
            )
        );
    }
}
