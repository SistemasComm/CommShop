<?php

namespace Clearsale\Total\Model\Config\Source;

class MassOrderQuantity implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => '1'],
            ['value' => 2, 'label' => '2'],
			['value' => 5, 'label' => '5'],
			['value' => 10, 'label' => '10'],
			['value' => 15, 'label' => '15'],
			['value' => 20, 'label' => '20'],
			['value' => 50, 'label' => '50'],
			['value' => 100, 'label' => '100']
        ];
    }
}