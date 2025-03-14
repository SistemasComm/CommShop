<?php
/**
 * Clearsale Total
 *
 * @author Trezo <contato@trezo.com.br>
 *
 */

namespace Clearsale\Total\Model\Config\Source;

use Magento\Customer\Model\Address;
use Magento\Framework\Option\ArrayInterface;

class CustomerAttributes implements ArrayInterface
{
    protected $_customer;

    public function __construct(
        Address $customer
    ) {
        $this->_customer = $customer;
    }

    public function toOptionArray()
    {
        $customerAttributes = $this->_customer->getAttributes();
        $attributesArrays = [];
        $attributesArrays[] = [
            'label' => null,
            'value' => 0
        ];

        foreach ($customerAttributes as $key => $value) {
            $attributesArrays[] = [
                'label' => $key,
                'value' => $key
            ];
        }

        return $attributesArrays;
    }
}
