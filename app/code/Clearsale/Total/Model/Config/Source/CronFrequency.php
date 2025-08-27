<?php

namespace Clearsale\Total\Model\Config\Source;

class CronFrequency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '*/1 * * * *', 'label' => '1'],
            ['value' => '*/2 * * * *', 'label' => '2'],
			['value' => '*/5 * * * *', 'label' => '5'],
			['value' => '*/10 * * * *', 'label' => '10'],
			['value' => '*/15 * * * *', 'label' => '15'],
			['value' => '*/30 * * * *', 'label' => '30'],
			['value' => '*/60 * * * *', 'label' => '60']
        ];
    }
}
