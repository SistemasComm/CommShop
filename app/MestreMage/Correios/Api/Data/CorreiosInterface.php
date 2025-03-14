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

namespace MestreMage\Correios\Api\Data;

interface CorreiosInterface
{
    const ENTITY_ID = 'entity_id';
    const Servico = 'servico';
    const Prazo = 'prazo';
    const PesoInicial = 'peso_inicial';
    const PesoFinal = 'peso_final';
    const Valor = 'valor';
    const CepInicial = 'cep_inicial';
    const CepFinal = 'cep_final';
    const UPDATE_TIME = 'update_time';
 

    public function getEntityId();
    public function setEntityId($entityId);
 
    public function getServico();
    public function setServico($servico);

    public function getPrazo();
    public function setPrazo($prazo);

    public function getPesoInicial();
    public function setPesoInicial($peso_inicial);

    public function getPesoFinal();
    public function setPesoFinal($peso_final);

    public function getValor();
    public function setValor($valor);

    public function getCepInicial();
    public function setCepInicial($cep_inicial);

    public function getCepFinal();
    public function setCepFinal($cep_final);

    public function getUpdateTime();
    public function setUpdateTime($updateTime);
}
