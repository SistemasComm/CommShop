<?php
namespace MestreMage\SigepWeb\PhpSigep\Services\Real;

use MestreMage\SigepWeb\PhpSigep\Model\AbstractModel;
use MestreMage\SigepWeb\PhpSigep\Model\BuscaClienteResult;
use MestreMage\SigepWeb\PhpSigep\Services\Exception;
use MestreMage\SigepWeb\PhpSigep\Services\Result;
use MestreMage\SigepWeb\PhpSigep\Model\AccessData;

/**
 * @author: Stavarengo
 */
class BuscaCliente implements RealServiceInterface
{

    /**
     * @param \PhpSigep\Model\AbstractModel|\PhpSigep\Model\SolicitaEtiquetas $params
     *
     * @throws \PhpSigep\Services\Exception
     * @throws InvalidArgument
     * @return BuscaClienteResult
     */
    public function execute(AbstractModel $params)
    {
        if (!$params instanceof AccessData) {
            throw new InvalidArgument();
        }

        $soapArgs = array(
            'idContrato'       => $params->getNumeroContrato(),
            'idCartaoPostagem' => $params->getCartaoPostagem(),
            'usuario'          => $params->getUsuario(),
            'senha'            => $params->getSenha(),
        );

        $result = new Result();

        try {
            if (!$params->getUsuario() || !$params->getSenha() || !$params->getNumeroContrato()
                || !$params->getCartaoPostagem()
            ) {
                throw new Exception('Para usar este serviço você precisa setar o nome de usuário, a senha, o numero ' .
                    'do contrato e o número do cartão de postagem.');
            }

            $r = SoapClientFactory::getSoapClient()->buscaCliente($soapArgs);
            if (!$r || !is_object($r) || !isset($r->return) || ($r instanceof \SoapFault)) {
                if ($r instanceof \SoapFault) {
                    throw $r;
                }
                throw new \Exception('Erro ao consultar os dados do cliente. Retorno: "' . $r . '"');
            }

            $result->setResult(new BuscaClienteResult((array)$r->return));
        } catch (\Exception $e) {
            if ($e instanceof \SoapFault) {
                $result->setIsSoapFault(true);
                $result->setErrorCode($e->getCode());
                $result->setErrorMsg(SoapClientFactory::convertEncoding($e->getMessage()));
            } else {
                $result->setErrorCode($e->getCode());
                $result->setErrorMsg($e->getMessage());
            }
        }

        return $result;
    }
}
