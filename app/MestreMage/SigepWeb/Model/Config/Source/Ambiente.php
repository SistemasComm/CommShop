<?php

namespace MestreMage\SigepWeb\Model\Config\Source;
use MestreMage\SigepWeb\PhpSigep\Config;

class Ambiente implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => Config::ENV_DEVELOPMENT, 'label' => 'Homolog'],
            ['value' => Config::ENV_PRODUCTION, 'label' => 'Produção']
        ];
    }
}