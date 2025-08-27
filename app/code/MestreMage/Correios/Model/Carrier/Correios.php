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

namespace MestreMage\Correios\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Config;
use Magento\Shipping\Model\Rate\ResultFactory;
use MestreMage\Core\Model\ModulesManagement;
use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Backend\Model\Session\Quote;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\State;
use Magento\Catalog\Model\ProductRepository;
use Psr\Log\LoggerInterface;
use MestreMage\Correios\Api\Client;
use Magento\Backend\Model\Session\Quote as BackendSessionQuote;
use PhpParser\Node\Stmt\TryCatch;
use Magento\Framework\App\CacheInterface;

class Correios extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    protected $_code = 'correios';
    protected $_isFixed = true;
    protected $_rateResultFactory;
    protected $_rateMethodFactory;
    protected $methods;
    protected $productRepository;
    protected $quote;
    protected $appState;
    protected $_session;
    protected $obligatoryLogin = [4162, 40436, 40444, 81019, 4669];
    protected $_delivery_day;
    protected $_no_stock_cart = 0;
    protected $_volumeWeight = 0;
    protected $_volumeHeight = 0;
    protected $_packageValue = 0;
    protected $_midSize = 0;
    protected $_packageWeight = 0;
    protected $mini_envio = 04227;
    protected $_splitUp = 0;
    protected $_addAument = 0;
    protected $dimessionInvalidSku = '';
    protected $backendSessionQuote;
    protected $cache;

    public function __construct(
        Session $session,
        State $appState,
        Quote $quote,
        ProductRepository $productRepository,
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        BackendSessionQuote $backendSessionQuote,
        CacheInterface $cache,
        array $data = []
    ) {
        $this->_session = $session;
        $this->appState = $appState;
        $this->quote = $quote;
        $this->productRepository = $productRepository;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->backendSessionQuote = $backendSessionQuote;
        $this->cache = $cache;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function getTokenApi()
    {
        $urlTokenApi = 'https://api.correios.com.br/token/v1/autentica/cartaopostagem';
        if ($this->cache->load('token_correio')) {
            return $this->cache->load('token_correio');
        }

        $login = (string)$this->getConfig('modulo_correios_section/settings_contract/cod_adm');
        $password = (string)$this->getConfig('modulo_correios_section/settings_contract/pass_adm');
        $card_post = (string)$this->getConfig('modulo_correios_section/settings_contract/card_post');

        $basicAuth = base64_encode("$login:$password");

        $bodyFild = ['numero' => $card_post];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlTokenApi,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($bodyFild),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic $basicAuth",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response);
        if (isset($response->token)) {
            $this->cache->save($response->token, 'token_correio', [], 60);
            return $response->token;
        }
    }

    public function getAllowedMethods()
    {
        return [$this->getCarrierCode() => __($this->getConfigData('name'))];
    }

    public function isTrackingAvailable()
    {
        return true;
    }

    protected function _getTracking($code)
    {
        return [
            'url' => 'https://www2.correios.com.br/sistemas/rastreamento/?objetos=' . $code
        ];
    }

    public function getTrackingInfo($number)
    {
        $aux = $this->_getTracking($number);
        $tracking = $this->_rateMethodFactory->create();

        $tracking->setCarrier($this->_code);
        $tracking->setCarrierTitle("Correios");
        $tracking->setTracking($number);
        if ($aux != false) {
            $tracking->addData($aux);
        }
        return $tracking;
    }

    public function collectRates(RateRequest $request)
    {


        try {
            $this->dimessionInvalidSku = '';
            $dest_postcode = $request->getData('dest_postcode');
            $dest_postcode = isset($dest_postcode) ? $dest_postcode : "";
            $dest_postcode = preg_replace('/[^0-9]/', '', $dest_postcode);
            if (!$this->isActive() || strlen(($dest_postcode ? $dest_postcode : "")) < 8) {
                return false;
            }

            $free_shipping = $request->getFreeShipping();
            $result = $this->_rateResultFactory->create();
            $modalidade_frete = explode(',', $this->getConfig('carriers/correios/posting_methods'));

            if (is_int($request->getPackageWeight())) {
                $this->_packageWeight = $request->getPackageWeight();
            } else {
                $this->_packageWeight = $this->fixWeight($request->getPackageWeight());
            }

            $this->getCubicWeight($request);

            if ($this->_addAument && !(int)$this->getConfig('carriers/correios/view_method_with_additional_value')) {
                return false;
            }

            if ($this->dimessionInvalidSku) {
                $this->logMessage("Produto(s) SKU " . $this->dimessionInvalidSku . "excede o limite máximo das dimensões permitidas.");
                return false;
            }

            if (!ModulesManagement::testModule($this->getConfig(\base64_decode('Y2FycmllcnMvY29ycmVpb3MvYWN0aXZlX2hhc2g=')), 'MestreMage_Correios')) return false;
            $correiosMethodReturn = [];
            $servicoCodigo = [];
	    foreach ($modalidade_frete as $correiosMethod) {

		  //  if($correiosMethod == '40010')
		//	    $correiosMethod  = '04014';

                    // if($correiosMethod == '41106')
                       //     $correiosMethod = '04510';

                $correiosMethod = $this->getServiceToPopulate($correiosMethod, $this->formatZip($dest_postcode));



		     //if($correiosMethod["servico_codigo"] == '40010')
                       //     $correiosMethod["servico_codigo"]  = '04014';

                     //if($correiosMethod["servico_codigo"] == '41106')
                     //       $correiosMethod["servico_codigo"] = '04510';




                if (isset($correiosMethod["valor"]) && !empty($correiosMethod["valor"])) {
                    $correiosMethodReturn[] = $correiosMethod;
                    $servicoCodigo[] = $correiosMethod["servico_codigo"];
                }
            }

            $freeMethod = '';
            $posting_freemethod = $this->getConfig('carriers/correios/posting_freemethod');
            $posting_freemethod_2 = $this->getConfig('carriers/correios/posting_freemethod_2');
            if (\in_array($posting_freemethod, $servicoCodigo)) {
                $freeMethod = $posting_freemethod;
            } elseif (\in_array($posting_freemethod_2, $servicoCodigo)) {
                $freeMethod = $posting_freemethod_2;
            }


            foreach ($correiosMethodReturn as $correiosMethod) {
                $method = $this->_rateMethodFactory->create();
                $method->setCarrier('correios');
                $method->setCarrierTitle($this->getConfig('carriers/correios/name'));
                $method->setMethod('correios_mm_' . $correiosMethod['servico_codigo']);

                if ($free_shipping == true && ($correiosMethod["servico_codigo"] == $freeMethod)) {
                    $free_shipping = false;
                    $freeShippingMessage = $this->getConfig('carriers/correios/freeshipping_message');
                    if ($freeShippingMessage != "") {
                        $method->setMethodTitle("[" . $freeShippingMessage . "] " . $correiosMethod['servico']);
                    } else {
                        $method->setMethodTitle($correiosMethod['servico']);
                    }
                    $amount = 0;
                } else {
                    $amount = $correiosMethod['valor'];
                    $method->setMethodTitle($correiosMethod['servico']);
                }
                $method->setPrice($amount);
                $method->setCost($amount);
                $result->append($method);
            }

            return $result;
        } catch (\Exception $ex) {
            $this->logMessage("Error in consult: " . $ex->getMessage());
        }
    }

    public function getCubicWeight($quote)
    {
        $this->getDeliveryDays($quote);
        $cubicWeight = 0;
        $items = [];
        $maxH = 90;
        $minH = 2;
        $maxW = 90;
        $minW = 16;
        $maxD = 90;
        $minD = 11;
        $sumMax = 160;
        $packageWeight = ((int)$this->getConfig("carriers/correios/max_packge_weight") ? (int)$this->getConfig("carriers/correios/max_packge_weight") : 50);
        $coefficient = 6000;
        $dimension_value = (int)$this->getConfig("carriers/correios/dimension_for_additional_value");

        if ($this->appState->getAreaCode() === "adminhtml" && $this->quote->isSessionExists()) {
            $quote = $this->backendSessionQuote->getQuote();
            $items = $quote->getAllItems();
        } else {
            $items = $quote->getAllItems();
        }

        $attr_height = $this->getConfig("carriers/correios/attr_height");
        $attr_width = $this->getConfig("carriers/correios/attr_width");
        $attr_depth = $this->getConfig("carriers/correios/attr_depth");

        $max_height = (int)$this->getConfig("carriers/correios/max_height");
        $max_width = (int)$this->getConfig("carriers/correios/max_width");
        $max_depth = (int)$this->getConfig("carriers/correios/max_depth");

        foreach ($items as $item) {
            $productItem = $item->getProduct();
            $product = $this->productRepository->getById($productItem->getId());
            $width = $product->getData($attr_width) ? (int)$product->getData($attr_width) : (int)$this->getConfig("carriers/correios/default_width");
            $height = $product->getData($attr_height) ? (int)$product->getData($attr_height) : (int)$this->getConfig("carriers/correios/default_height");
            $depth = $product->getData($attr_depth) ? (int)$product->getData($attr_depth) : (int)$this->getConfig("carriers/correios/default_depth");

            if ($depth > $max_depth || $width > $max_width || $height > $max_height) {
                $this->dimessionInvalidSku .= $productItem->getSku() . ', ';
            }

            if ($width > $dimension_value || $height > $dimension_value || $depth > $dimension_value) {
                $this->_addAument++;
            }

            if ($width < $minW) $width = $minW;
            if ($depth < $minD) $depth = $minD;
            if ($height < $minH) $height = $minH;

            $this->_volumeHeight += ((int)$product->getData($attr_height)  * (int)$item->getQty());
            $this->_volumeWeight += (($width * $depth * $height) * (int)$item->getQty()) / $coefficient;
        }

        $this->_packageValue = $quote->getData('package_value');

        if ((int)$this->getConfig("carriers/correios/split_package")) {
            if ($this->_packageWeight > $packageWeight) {
                $this->_splitUp++;
                $this->_volumeWeight /= 2;
                $this->_packageWeight /= 2;
                $this->_packageValue /= 2;
            }
        } else {
            if ($this->_packageWeight > $packageWeight) {
                $this->logMessage("o peso " . $this->_packageWeight . " excedeu o limite estipulado de " . $packageWeight);
                return;
            }
        }
        if ($this->_packageWeight < 0.3) {
            $this->_packageWeight = 0.3;
        }

        $volumeTotal    = $this->_volumeWeight * $coefficient;
        $pow            = round(pow((int) $volumeTotal, (1 / 3)));
        $this->_midSize = max($pow, $minW);
        return true;
    }


    public function getDeliveryDays($quote)
    {
        if ($this->appState->getAreaCode() === "adminhtml" && $this->quote->isSessionExists()) {
            $quote = $this->backendSessionQuote->getQuote();
            $items = $quote->getAllItems();
        } else {
            $items = $quote->getAllItems();
        }

        $deliverydays = [0];
        foreach ($items as $item) {
            $productItem = $item->getProduct();
            $product = $this->productRepository->getById($productItem->getId());
            $stock = (array)$product->getData('quantity_and_stock_status');
            $this->logMessage(json_encode($stock));
            if ($stock['is_in_stock']) {
                $deliverydays[] = (int)$this->getConfig("carriers/correios/add_deliverydays");
                $deliverydays[] = (int)$product->getData('add_deliverydays');
            } else {
                $this->_no_stock_cart = 1;
                $deliverydays[] = (int)$this->getConfig("carriers/correios/add_deliverydays_ns");
                $deliverydays[] = (int)$product->getData('add_deliverydays_ns');
            }
        }

        $this->_delivery_day = max($deliverydays);
        return true;
    }

    public function fixWeight($weight)
    {
        $result = $weight;
        if (((string)$this->getConfig("carriers/correios/weight_type") === 'gr')) {
            $result = number_format($weight / 1000, 2, '.', '');
        }
        return $result;
    }

    public function getServiceToPopulate($service, $finalPostcode)
    {
        $urlNewApi = 'https://api.correios.com.br';

        $packageWeightMiniEnvio = $this->_packageWeight;
        if ($service == $this->mini_envio) {
            $packageWeightMiniEnvio = $this->_volumeWeight;
        }

        $origPostcode = (string)$this->getConfig("shipping/origin/postcode");
        $declaredValue = (bool)$this->getConfig('carriers/correios/declared_value');

        $urls = [];
        $url_d = '';
        if ($declaredValue) {
            $declared_value_default = $this->getConfig("carriers/correios/declared_value_default");
            $sub_total = $this->_packageValue;
            if ($sub_total < $declared_value_default) {
                $sub_total = $declared_value_default;
            }

            $url_d = "$url_d&vlDeclarado=$sub_total&servicosAdicionais=019";
        }

        $sizePackage = $this->_midSize;

        $urls[] = "$urlNewApi/preco/v1/nacional/$service?cepDestino=$finalPostcode&cepOrigem=$origPostcode&psObjeto=$packageWeightMiniEnvio&comprimento=$sizePackage&largura=$sizePackage&altura=$sizePackage" . $url_d;
        $urls[] = "$urlNewApi/prazo/v1/nacional/$service?cepOrigem=$finalPostcode&cepDestino=$origPostcode";

        $this->logMessage(json_encode($urls));

        $consulting_methods_type = $this->getConfig("carriers/correios/consulting_methods_type");
        if ($consulting_methods_type == 4) {
            $shippingQuotes = $this->getOfflineShippingQuotes($service, $finalPostcode, $urls);

            if (!isset($shippingQuotes[0]) || !count($shippingQuotes[0])) {
                $shippingQuotes = $this->getOnlineShippingQuotes($service, $finalPostcode, $urls);
            }
        } else {
            if ($consulting_methods_type == 3) {
                $shippingQuotes = $this->getOfflineShippingQuotes($service, $finalPostcode, $urls);
            } else {
                $shippingQuotes = $this->getOnlineShippingQuotes($service, $finalPostcode, $urls);
            }
        }

        if (count($shippingQuotes) > 0) {
            if (isset($shippingQuotes[0])) {
                return $shippingQuotes[0];
            }
            return $shippingQuotes;
        } else {
            return false;
        }
    }

    public function formatZip($zipcode)
    {
        $new = trim($zipcode);
        $new = preg_replace('/[^0-9\s]/', '', $new);
        if (!preg_match("/^[0-9]{7,8}$/", $new)) {
            return false;
        } elseif (preg_match("/^[0-9]{7}$/", $new)) {
            $new = "0" . $new;
        }
        return $new;
    }

    public function getMethodsData()
    {
        if (!$this->methods) {
            $methods = $this->getConfig('modulo_correios_section/settings/methods');
            $this->methods = json_decode($methods, true);
        }
        return $this->methods;
    }

    public function getMethodName($methodCode)
    {
        $methodCode = (int)$methodCode;
        $methods = $this->getMethodsData();
        foreach ($methods as $method) {
            if ($methodCode === (int)$method['code']) {
                return $method['name'];
            }
        }

        return __("Undefined");
    }

    public function getOfflineShippingQuotes($service, $finalPostcode, $urlsArray)
    {
        $ambiente = (int)$this->getConfig("carriers/correios/ambiente");
        $typeHandlingFee = $this->getConfig("carriers/correios/type_handling_fee");
        $debug_mode = '';
        $deliveryMessage = (string)$this->getConfig("carriers/correios/deliverydays_message");
        if ($this->_no_stock_cart) {
            $deliveryMessage = (string)$this->getConfig("carriers/correios/deliverydays_message_ns");
        }

        if ($deliveryMessage === "") {
            $deliveryMessage = "%s - Em média %d dia(s)";
        }
        $showDeliveryDays = (bool)$this->getConfig("carriers/correios/show_deliverydays");
        $handlingFee = 0;
        if ($this->getConfig("carriers/correios/handling_fee") != "") {
            if (is_numeric($this->getConfig("carriers/correios/handling_fee"))) {
                $handlingFee = $this->getConfig("carriers/correios/handling_fee");
            }
        }

        $additionalValue = 0;
        if ($this->_addAument) {
            if (is_numeric($this->getConfig("carriers/correios/additional_value"))) {
                $additionalValue = $this->getConfig("carriers/correios/additional_value");
            }
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cotacaoOfflineObj = $objectManager->get('MestreMage\Correios\Model\ResourceModel\Correios\Collection')
            ->addFieldToFilter('cep_inicial', ["lteq" => $finalPostcode])
            ->addFieldToFilter('cep_final', ["gteq" => $finalPostcode])
            ->addFieldToFilter('peso_inicial', ["lteq" => $this->_packageWeight])
            ->addFieldToFilter('peso_final', ["gteq" => $this->_packageWeight]);

        if ($ambiente) {
            $debug_mode = '  debug: ';
            $debug_mode .= '| Tipo_Consulta:Offline';
            $debug_mode .= '| Peso:' . $this->_packageWeight;
            $debug_mode .= '| Largura:' . $this->_midSize;
            $debug_mode .= '| Comprimeto:' . $this->_midSize;
            $debug_mode .= '| Altura:' . ($this->_volumeHeight ? $this->_volumeHeight : $this->_midSize);
            $debug_mode .= '| Taxa de Manuseio:' . $handlingFee;
            $debug_mode .= '| Valor Adicional:' . $additionalValue;
            $debug_mode .= '| Cod_Serviço:' . $service;
            $debug_mode .= '| CEP_Origem:' . (string)$this->getConfig("shipping/origin/postcode");
            $debug_mode .= '| CEP_Destino:' . $finalPostcode;
        }

        $ratingsCollection = [];
        foreach ($cotacaoOfflineObj as $cotacaoOffline) {
            if ($cotacaoOffline['servico'] == $service) {
                $data = [];
                if (!$showDeliveryDays) {
                    $data['servico'] = $this->getMethodName($cotacaoOffline['servico']) . $debug_mode;
                } else {
                    $data['servico'] = sprintf(
                        $deliveryMessage,
                        $this->getMethodName($cotacaoOffline['servico']),
                        intval($cotacaoOffline['prazo'] + $this->_delivery_day)
                    ) . $debug_mode;
                }
                $finalValue = $cotacaoOffline['valor'];
                if ($this->_splitUp) {
                    $finalValue *= 2;
                }

                $finalValue = str_replace(",", ".", $finalValue);

                if ($typeHandlingFee == '1') {
                    $handlingFee = ($finalValue / 100 * $handlingFee);
                }

                $data['valor'] = $finalValue + $handlingFee + $additionalValue;
                $data['prazo'] = $cotacaoOffline['prazo'] + $this->_delivery_day;
                $data['servico_codigo'] = (string)$cotacaoOffline['servico'];
                array_push($ratingsCollection, $data);
            }
        }

        return $ratingsCollection;
    }

    public function getOnlineShippingQuotes($service, $finalPostcode, $urlsArray)
    {
        $ambiente = (int)$this->getConfig("carriers/correios/ambiente");
        $typeHandlingFee = $this->getConfig("carriers/correios/type_handling_fee");
        $debug_mode = '';
        $deliveryMessage = (string)$this->getConfig("carriers/correios/deliverydays_message");
        if ($this->_no_stock_cart) {
            $deliveryMessage = (string)$this->getConfig("carriers/correios/deliverydays_message_ns");
        }

        if ($deliveryMessage === "") {
            $deliveryMessage = "%s - Em média %d dia(s)";
        }
        $showDeliveryDays = (bool)$this->getConfig("carriers/correios/show_deliverydays");
        $timeOutCur = (int)$this->getConfig("carriers/correios/time_out_cur");

        $handlingFee = 0;
        if ($this->getConfig("carriers/correios/handling_fee") != "") {
            if (is_numeric($this->getConfig("carriers/correios/handling_fee"))) {
                $handlingFee = $this->getConfig("carriers/correios/handling_fee");
            }
        }

        $additionalValue = 0;
        if ($this->_addAument) {
            if (is_numeric($this->getConfig("carriers/correios/additional_value"))) {
                $additionalValue = $this->getConfig("carriers/correios/additional_value");
            }
        }

        $newServiceApi = [];
        $tokenApi = $this->getTokenApi();

        foreach ($urlsArray as $url_d) {
            try {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url_d,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => $timeOutCur,
                    CURLOPT_CONNECTTIMEOUT => $timeOutCur,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'accept: application/json',
                        "Authorization: Bearer $tokenApi"
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                $response = json_decode($response, true);
                if (isset($response['coProduto'])) {
                    $newServiceApi = array_merge($newServiceApi, $response);
                } else {

                    if (isset($response['msgs']) && !empty($response['msgs'])) {
                        $this->logMessage("service: $service" . json_encode($response));
                    }

                    if ($this->getConfig("carriers/correios/consulting_methods_type") == 2) {
                        return $this->getOfflineShippingQuotes($service, $finalPostcode, $urlsArray); // ofline
                    }
                }
            } catch (\Exception $e) {
                $this->logMessage("Error in consult: " . $e->getMessage());
                continue;
            }
        }

        if ($ambiente) {
            $debug_mode = '  debug: ';
            $debug_mode .= '| Tipo_Consulta:Online';
            $debug_mode .= '| Peso:' . $this->_packageWeight;
            $debug_mode .= '| Largura:' . $this->_midSize;
            $debug_mode .= '| Comprimeto:' . $this->_midSize;
            $debug_mode .= '| Altura:' . ($this->_volumeHeight ? $this->_volumeHeight : $this->_midSize);
            $debug_mode .= '| Taxa de Manuseio:' . $handlingFee;
            $debug_mode .= '| Valor Adicional:' . $additionalValue;
            $debug_mode .= '| Cod_Serviço:' . $service;
            $debug_mode .= '| CEP_Origem:' . (string)$this->getConfig("shipping/origin/postcode");
            $debug_mode .= '| CEP_Destino:' . $finalPostcode;
        }

        try {

            if (isset($newServiceApi['pcFinal'])) {

                $this->logMessage("xxdd: " .  json_encode($newServiceApi));

                if ((float)$newServiceApi['pcFinal'] > 0) {

                    $data = [];
                    if (!$showDeliveryDays) {
                        $data['servico'] = $this->getMethodName($newServiceApi['coProduto']) . $debug_mode;
                    } else {
                        $data['servico'] = sprintf(
                            $deliveryMessage,
                            $this->getMethodName($newServiceApi['coProduto']),
                            intval($newServiceApi['prazoEntrega'] + $this->_delivery_day)
                        ) . $debug_mode;
                    }

                    $finalValue = $newServiceApi['pcFinal'];
                    if ($this->_splitUp) {
                        $finalValue *= 2;
                    }

                    $finalValue = str_replace(",", ".", $finalValue);

                    if ($typeHandlingFee == '1') {
                        $handlingFee = ($finalValue / 100 * $handlingFee);
                    }

                    $data['valor'] = $finalValue + $handlingFee + $additionalValue;
                    $data['prazo'] = $newServiceApi['prazoEntrega'] + $this->_delivery_day;
                    $data['servico_codigo'] = (string)$newServiceApi['coProduto'];

                    return $data;
                }
            }
        } catch (\Exception $ex) {
            $this->logMessage("Error in consult: " . $ex->getMessage());
        }

        return [];
    }

    function get_tag($txt, $tag)
    {
        $offset = 0;
        $start_tag = "<" . $tag;
        $end_tag = "</" . $tag . ">";
        $arr = array();
        do {
            $pos = strpos($txt, $start_tag, $offset);
            if ($pos) {
                $str_pos = strpos($txt, ">", $pos) + 1;
                $end_pos = strpos($txt, $end_tag, $str_pos);
                $len = $end_pos - $str_pos;
                $f_text = substr($txt, $str_pos, $len);

                $arr[] = $f_text;
                $offset = $end_pos;
            }
        } while ($pos);
        return $arr;
    }

    public static function getTrackingInfoByCode($obj)
    {
        if ($obj) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://correios-v2.contrateumdev.com.br/api/rastreio',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                "code": "' . $obj . '",
                "type": "LS"
              }',
                CURLOPT_HTTPHEADER => array(
                    'chave: $1y$10$8IAZn7HKq7QJWbh37N3GOOeRVY',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response);

            $novo_array = array();

            if (isset($response->objeto[0]->evento)) {
                $response = $response->objeto[0]->evento;
                foreach ($response as $row) {
                    $novo_array['success'] = true;
                    $novo_array[] = ["date" => $row->data, "hour" => $row->hora, "location" => $row->unidade->local, "action" => utf8_encode($row->unidade->tipounidade), "message" => utf8_encode($row->descricao)];
                }

                return (object)$novo_array;
            } else {
                $novo_array = array();
                $novo_array['success'] = false;
                $novo_array['msg'] = "Objeto não encontrado";
                return $novo_array;
            }
        }
    }

    public function getConfig($string)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($string, $storeScope);
    }

    public function logMessage($msg)
    {
        if ($this->getConfig('carriers/correios/enabled_log')) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/correios.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info($msg);
        }
    }
}
