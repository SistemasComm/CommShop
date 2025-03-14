<?php
namespace MestreMage\SigepWeb\PhpSigep\Model;

use MestreMage\SigepWeb\PhpSigep\Bootstrap;
use MestreMage\SigepWeb\PhpSigep\Config;
/**
 * @author: Stavarengo
 */
class AccessDataHomologacao extends AccessData
{
    /**
     * Atalho para criar uma {@link AccessData} com os dados do ambiente de homologação.
     */
    public function __construct()
    {
        parent::__construct(
            array(
                'usuario'           => $this->getConfig('section_module_sigepweb/sigepweb/usuario'),
                'senha'             => $this->getConfig('section_module_sigepweb/sigepweb/senha'),
                'codAdministrativo' => $this->getConfig('section_module_sigepweb/sigepweb/cod_administrativo'),
                'numeroContrato'    => $this->getConfig('section_module_sigepweb/sigepweb/numero_contrato'),
                'cartaoPostagem'    => $this->getConfig('section_module_sigepweb/sigepweb/cartao_postagem'),
                'cnpjEmpresa'       => $this->getConfig('section_module_sigepweb/sigepweb/cnpj_empresa'), // Obtido no método 'buscaCliente'.
                'anoContrato'       => null, // Não consta no manual.
                'diretoria'         => new Diretoria($this->getConfig('section_module_sigepweb/sigepweb/diretoria')), // Obtido no método 'buscaCliente'.
            )

//            array(
//                'usuario'           => 'sigep',
//                'senha'             => 'n5f9t8',
//                'codAdministrativo' => '17000190',
//                'numeroContrato'    => '9992157880',
//                'cartaoPostagem'    => '0057018901',
//                'cnpjEmpresa'       => '34028316000103', // Obtido no método 'buscaCliente'.
//                'anoContrato'       => null, // Não consta no manual.
//                'diretoria'         => new Diretoria(Diretoria::DIRETORIA_DR_BRASILIA), // Obtido no método 'buscaCliente'.
//            )

        );
        try {Bootstrap::getConfig()->setEnv($this->getConfig('section_module_sigepweb/sigepweb/ambiente_type'));} catch (\Exception $e) {}
    }

    public function getConfig($string){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($string, $storeScope);
    }
}