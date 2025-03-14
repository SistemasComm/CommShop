<?php

namespace Clearsale\Base\Model\Config\Source;

class PaymentMethods implements \Magento\Framework\Option\ArrayInterface
{
    protected $_paymentHelper;

    public function __construct(\Magento\Payment\Helper\Data $paymentHelper)
    {
        $this->_paymentHelper = $paymentHelper;
    }

    public function toOptionArray()
    {
        $data = $this->_paymentHelper->getPaymentMethods();

        $result = array();

        foreach ($data as $code => $child) {
            if (!array_key_exists('title', $child)) {
                continue;
            }

            $result [] = ['value' => $code, 'label' => $child['title'] ];
        }

        return $result;
    }
}
