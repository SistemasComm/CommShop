<?php

namespace MestreMage\Correios\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Sandbox implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'1', 'label'=>__('Sandbox (Testes e Validações)')),
            array('value'=>'0', 'label'=>__('Produção')),
        );
    }
}
