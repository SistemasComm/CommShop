<?php

declare(strict_types=1);

namespace MestreMage\Correios\Model;

class SearchPostCodeManagement implements \MestreMage\Correios\Api\SearchPostCodeManagementInterface
{

    private $correios;
    public function __construct(\MestreMage\Correios\Model\Carrier\Correios $correios)
    {
        $this->correios = $correios;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchPostCode($param)
    {

        if (isset($_REQUEST['whatsapp']) && isset($_REQUEST['increment_id'])) {
            try {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $order = $objectManager->create('Magento\Sales\Model\Order')->loadByAttribute('increment_id', $_REQUEST['increment_id']);
                $order->getPayment()->setAdditionalInformation('number_whatsapp_send', $_REQUEST['whatsapp'])->save();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        $con_test_zip_orig = '';
        $con_test_zip_dest = '';

        if (isset($_REQUEST['con_test_zip_orig'])) {
            $con_test_zip_orig = $_REQUEST['con_test_zip_orig'];
        }
        if (isset($_REQUEST['con_test_zip_dest'])) {
            $con_test_zip_dest = $_REQUEST['con_test_zip_dest'];
        }

        $urls[] = "https://api.correios.com.br/prazo/v1/nacional/04162?cepOrigem=$con_test_zip_orig&cepDestino=$con_test_zip_dest";
        $urls[] = "https://api.correios.com.br/preco/v1/nacional/04162?comprimentos=16&largura=16&altura=16&cepDestino=$con_test_zip_orig&cepOrigem=$con_test_zip_dest&psObjeto=3000";

        $shippingQuotes =  $this->correios->getOnlineShippingQuotes('04162', $con_test_zip_dest, $urls);

        if (isset($shippingQuotes['servico'])) {
            return 'Contrato Validado com sucesso!||';
        }

        return 'Erro: verifique o usuario e a senha ||' . json_encode($shippingQuotes);
    }

    function remover_caracter($string)
    {
        return preg_replace(array("/(ç|Ç)/", "/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "c a A e E i I o O u U n N"), $string);
    }
}
