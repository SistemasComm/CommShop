<?php

namespace MestreMage\SigepWeb\Model\Config\Source;
use MestreMage\SigepWeb\PhpSigep\Model\Diretoria;

class DiretoriaList implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {


        $diretoria = new Diretoria();

        $return = [];
        foreach($diretoria->getListDiretoria() as $key => $item){
            $return[] = ['value' => $key, 'label' => $item[0].' - '.$item[1]];
        }
        return $return;
    }
}