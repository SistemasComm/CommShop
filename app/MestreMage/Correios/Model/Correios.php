<?php
 /**
 * Mestre Magento
 * www.modulomagento.com.br
 *
 * LICENÇA DE USO
 * Este arquivo está sujeito ao EULA (contrato de licença para usuário final).
 * @copyright Copyright (c) Mestre Magento. ( https://www.modulomagento.com.br/ )
 * @license   https://www.modulomagento.com.br/Mestre-Magento-Licenca.txt
 *
 * ##############################################################################
 * #																			#
 * #  Nós programadores nos dedicamos muito, da mesma forma que você, então		#
 * #  valorize sua profissão e não aceite usar esse módulo de forma ilícita.	#
 * #  Denuncie anônimamente a empresa que utilizar esse módulo de forma ilegal.	#
 * #  Estamos abertos a parcerias com desenvolvedores ;)						#
 * #																			#
 * ##############################################################################
 *
 */

namespace MestreMage\Correios\Model;

use MestreMage\Correios\Api\Data\CorreiosInterface;

class Correios extends \Magento\Framework\Model\AbstractModel implements CorreiosInterface
{
    const CACHE_TAG = 'mm_correios_offline';
 
    protected $_cacheTag = 'mm_correios_offline';
 
    protected $_eventPrefix = 'mm_correios_offline';
 
    protected function _construct()
    {
        $this->_init('MestreMage\Correios\Model\ResourceModel\Correios');
    }

    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }
 
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }
 
    public function getServico()
    {
        return $this->getData(self::Servico);
    }
 
    public function setServico($servico)
    {
        return $this->setData(self::Servico, $servico);
    }
     
    public function getPrazo()
    {
        return $this->getData(self::Prazo);
    }
 
    public function setPrazo($prazo)
    {
        return $this->setData(self::Prazo, $prazo);
    } 
    
    public function getPesoInicial()
    {
        return $this->getData(self::PesoInicial);
    }
 
    public function setPesoInicial($peso_inicial)
    {
        return $this->setData(self::PesoInicial, $peso_inicial);
    } 

    public function getPesoFinal()
    {
        return $this->getData(self::PesoFinal);
    }
 
    public function setPesoFinal($peso_final)
    {
        return $this->setData(self::PesoFinal, $peso_final);
    } 

    public function getValor()
    {
        return $this->getData(self::Valor);
    }
 
    public function setValor($valor)
    {
        return $this->setData(self::Valor, $valor);
    } 

    public function getCepInicial()
    {
        return $this->getData(self::CepInicial);
    }
 
    public function setCepInicial($cep_inicial)
    {
        return $this->setData(self::CepInicial, $cep_inicial);
    } 

    public function getCepFinal()
    {
        return $this->getData(self::CepFinal);
    }
 
    public function setCepFinal($cep_final)
    {
        return $this->setData(self::CepFinal, $cep_final);
    }
 
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }
}
