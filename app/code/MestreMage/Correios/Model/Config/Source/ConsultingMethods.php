<?php

namespace MestreMage\Correios\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ConsultingMethods implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'1', 'label'=>__('Consulta em Tempo Real (valores mais precisos)')),
            array('value'=>'2', 'label'=>__('Consulta Híbrida (caso o Correios demore consulte o Base Offline)')),
            array('value'=>'3', 'label'=>__('Consulta Offline (de acordo com o que você cadastrou em sua Base Offline)')),
            array('value'=>'4', 'label'=>__('Consulta Invertida (Consulta primeiro a Base Offline se não tiver retorno consulta a Base Online)')),
        );
    }
}
