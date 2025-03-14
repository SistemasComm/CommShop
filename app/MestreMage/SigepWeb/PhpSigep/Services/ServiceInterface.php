<?php
namespace MestreMage\SigepWeb\PhpSigep\Services;

use MestreMage\SigepWeb\PhpSigep\Model\AccessData;
use MestreMage\SigepWeb\PhpSigep\Model\VerificaDisponibilidadeServico;
use MestreMage\SigepWeb\PhpSigep\Model\GeraDigitoVerificadorEtiquetas;
use MestreMage\SigepWeb\PhpSigep\Model\RastrearObjeto;
use MestreMage\SigepWeb\PhpSigep\Model\SolicitaEtiquetas;
use MestreMage\SigepWeb\PhpSigep\Model\CalcPrecoPrazo;
use MestreMage\SigepWeb\PhpSigep\Model\PreListaDePostagem;
/**
 * @author: Stavarengo
 */
interface ServiceInterface
{

    /**
     * @param \PhpSigep\Model\VerificaDisponibilidadeServico $params
     *
     * @return Result<\PhpSigep\Model\VerificaDisponibilidadeServicoResposta>
     */
    public function verificaDisponibilidadeServico(VerificaDisponibilidadeServico $params);

    /**
     * @param $cep
     * 
     * @return Result<\PhpSigep\Model\ConsultaCepResposta>
     */
    public function consultaCep($cep);

    /**
     * @param \PhpSigep\Model\SolicitaEtiquetas $params
     *
     * @return \PhpSigep\Model\Etiqueta[]
     */
    public function solicitaEtiquetas(SolicitaEtiquetas $params);

    /**
     * Pede para o WebService do Correios calcular o dígito verificador de uma etiqueta.
     *
     * Se preferir você pode usar o método {@linnk \PhpSigep\Model\Etiqueta::getDv() } para calcular o dígito
     * verificador, visto que esse método é mais rádido pois faz o cálculo local sem precisar se comunicar com o
     * WebService.
     * 
     * @param \PhpSigep\Model\GeraDigitoVerificadorEtiquetas $params
     * 
     * @return string[]
     */
    public function geraDigitoVerificadorEtiquetas(GeraDigitoVerificadorEtiquetas $params);

    /**
     * @param \PhpSigep\Model\PreListaDePostagem $params
     * @param \XMLWriter $xmlDaPreLista
     * @return mixed
     */
    public function fechaPlpVariosServicos(PreListaDePostagem $params);

    /**
     * @param \PhpSigep\Model\CalcPrecoPrazo $params
     * @return \PhpSigep\Model\CalcPrecoPrazoRespostaIterator
     */
    public function calcPrecoPrazo(CalcPrecoPrazo $params);

    /**
     * @todo documentar o retorno
     * 
     * @param \PhpSigep\Model\AccessData $params
     * @return mixed
     */
    public function buscaCliente(AccessData $params);

    /**
     *
     * @param \PhpSigep\Model\RastrearObjeto $params
     * @return \PhpSigep\Services\Result<\PhpSigep\Model\RastrearObjetoResultado[]>
     */
    public function rastrearObjeto(RastrearObjeto $params);

    /**
     * @param $numeroCartaoPostagem
     * @param $login
     * @param $senha
     * @return \PhpSigep\Services\Result<\PhpSigep\Model\verificarStatusCartaoPostagemResposta[]>
     */
    public function verificarStatusCartaoPostagem($numeroCartaoPostagem, $usuario, $senha);
}
