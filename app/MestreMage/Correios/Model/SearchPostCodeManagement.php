<?php
declare(strict_types=1);

namespace MestreMage\Correios\Model;

class SearchPostCodeManagement implements \MestreMage\Correios\Api\SearchPostCodeManagementInterface
{

    /**
     * {@inheritdoc}
     */
    public function getSearchPostCode($param)
    {

        if(isset($_REQUEST['whatsapp']) && isset($_REQUEST['increment_id'])){
            try {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $order = $objectManager->create('Magento\Sales\Model\Order')->loadByAttribute('increment_id', $_REQUEST['increment_id']);
                $order->getPayment()->setAdditionalInformation('number_whatsapp_send',$_REQUEST['whatsapp'])->save();
                return true;
             }catch(Exception $e) {
                return false;
            }
        }



        $url_correios = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa='.$_REQUEST['contract_cod_adm'].'&sDsSenha='.$_REQUEST['contract_pass_adm'].'&sCepOrigem='.$_REQUEST['con_test_zip_orig'].'&sCepDestino='.$_REQUEST['con_test_zip_dest'].'&nVlPeso=0.3&nCdFormato=1&nVlComprimento=15&nVlAltura=1&nVlLargura=10&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico=04162&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_correios);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        ob_start();
        curl_exec($ch);
        curl_close($ch);
        $content = ob_get_contents();
        ob_end_clean();

        $msg_return = 'Contrato Validado com sucesso!||';
        if ($content) {
            $xml = new \SimpleXMLElement($content);
            foreach ($xml->cServico as $servico) {

                if ((string)$servico->MsgErro != "") {
                    $msg_return = $this->remover_caracter($servico->MsgErro).'||'.$url_correios;
                
                }
            }
        }
        return $msg_return;
    }
    
    function remover_caracter($string) {
        return preg_replace(array("/(ç|Ç)/","/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","c a A e E i I o O u U n N"),$string);

    }
}

