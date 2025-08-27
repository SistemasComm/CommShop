<?php

namespace Amasty\Oaction\Model\Source;

class Status extends \Magento\Sales\Model\Config\Source\Order\Status
{
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $options[0] = [
            'value' => '',
            'label' => __('Magento Default')
        ];
        return $options;
    }
}
