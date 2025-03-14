<?php

namespace MestreMage\SigepWeb\Model\Config\Source;
use MestreMage\SigepWeb\PhpSigep\Config;

class ShippingMethodDefault implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
       $sedex_contrato_agencia = \MestreMage\SigepWeb\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_CONTRATO_AGENCIA;
       $pac_contato_agencia = \MestreMage\SigepWeb\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_CONTRATO_AGENCIA;
       $sedex_10 = \MestreMage\SigepWeb\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_10;
       $sedex_hoje = \MestreMage\SigepWeb\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_HOJE_40290;
       $sedex_xobrar = \MestreMage\SigepWeb\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_VAREJO_A_COBRAR;

        return [
            ['value' => $sedex_contrato_agencia, 'label' => 'Sedex Com Contrato | '.$sedex_contrato_agencia],
            ['value' => $pac_contato_agencia, 'label' => 'PAC Com Contrato | '.$pac_contato_agencia],
            ['value' => $sedex_10, 'label' => 'Sedex 10 | '.$sedex_10],
            ['value' => $sedex_hoje, 'label' => 'Sedex HOJE | '.$sedex_hoje],
            ['value' => $sedex_xobrar, 'label' => 'Sedex a Cobrar | '.$sedex_xobrar]
        ];
    }
}