<?php

namespace MestreMage\Correios\Model\Config\Source;

use MestreMage\Correios\Model\Carrier\Correios;
use Magento\Framework\Option\ArrayInterface;

class PostingMethods implements ArrayInterface
{
    private $correios;
    public function __construct(
        Correios $correios
    ) {
        $this->correios = $correios;
    }

    public function toOptionArray()
    {
        $methods = $this->correios->getMethodsData();

        $return = [];
        if (is_array($methods)) {
            foreach ($methods as $method) {
                $return[] = [
                    'value' => $method['code'],
                    'label' => $method['name'] . ' (' . $method['code'] . ')'
                ];
            }
        }

        return $return;
    }
}