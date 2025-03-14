<?php
namespace MestreMage\ShippingQuoteProductPage\Model;

use MestreMage\ShippingQuoteProductPage\Api\ShippingQuoteProductPageInterface;
use MestreMage\Core\Model\ModulesManagement;

class ShippingQuoteProductPage implements ShippingQuoteProductPageInterface
{
    public function shippingquoteproductpage()
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $quote = $objectManager->create('Magento\Quote\Model\Quote');
            $cart = $objectManager->create('Magento\Checkout\Model\Cart');
            $product = $objectManager->create('Magento\Catalog\Model\Product');
            $totalsCollector = $objectManager->create('Magento\Quote\Model\Quote\TotalsCollector');
            $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
            $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
            $pais =  $scopeConfig->getValue('general/country/default', $storeScope);
            $productRepository = $objectManager->get('\Magento\Catalog\Api\ProductRepositoryInterface');
            $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
            $request = $request->getParams();
    
            if(!isset($request['tipobusca'])){
                $data[0]['title'] = 'Erro';
                $data[0]['price'] = 'não foi possivel encontrar metodos de entrega';
                return json_encode($data);
            }
    
            if ($request['tipobusca'] == 1) {
                $selPais = '';
                if(!$selPais){
                    $selPais = $pais;
                }
                $params = new \Magento\Framework\DataObject();
                $idProdutoConsulta =  $request['product'];
                $qtyMin = 1;
                if(isset($request['super_group'])) {
                    foreach ($request['super_group'] as $key => $item) {
                        if($qtyMin == 1){
                            $idProdutoConsulta = $key;
                        }
                        if ($item >= $qtyMin) {
                            $qtyMin = $item;
                            $idProdutoConsulta = $key;
                        }
                    }
                }
                if(!empty($request['selected_configurable_option'])){
                    $idProdutoConsulta = $request['selected_configurable_option'];
                }
                $_product = $productRepository->getById($idProdutoConsulta);
                $options = array();
                foreach ($_product->getOptions() as $o)
                {
                    foreach ((array)$o->getValues() as $value)
                    {
                        $options[$value['option_id']] = $value['option_type_id'];
                    }
                }
                $params['options'] = $options;
                if(isset($request['qty'])){
                    $params['qty'] = (int)$request['qty'];
                }else{
                    $params['qty'] = $qtyMin;
                }
				
				$cart->addProduct($product->load($idProdutoConsulta),$params);
                $quote->addProduct($_product,$params);
                $quote->collectTotals();
                $shipping = $quote->getShippingAddress();
                $shipping->setCountryId($selPais);
                if(!empty($request['state'])){
                    $shipping->setRegionId($request['state']);
                }
                if(!empty($request['city'])){
                    $shipping->setCity($request['city']);
                }
                $shipping->setPostcode($request['cep']);
                $shipping->setCollectShippingRates(true);
                $totalsCollector->collectAddressTotals($quote, $shipping);
                $rates = $shipping->collectShippingRates()->getAllShippingRates();
                if(!ModulesManagement::testModule($scopeConfig->getValue(\base64_decode('c2hpcHBpbmdxdW90ZXByb2R1Y3RwYWdlL2dlcmFsL2FjdGl2ZV9oYXNo'),  $storeScope),'MestreMage_ShippingQuoteProductPage')){ $rates = []; }
                $data = array();
                if (count($rates)) {
                    foreach ($rates as $k => $rate) {
                        $data[$k]['title'] = $rate->getMethodTitle();
                        $data[$k]['price'] = $priceHelper->currency($rate->getPrice(), true, false);
                    }
                    return json_encode($data);
                } else {
                    if($scopeConfig->getValue('shippingquoteproductpage/geral/log', $storeScope)) {
                        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/MestreMage_buscarCep.log');
                        $logger = new \Zend_Log();
                        $logger->addWriter($writer);
                        $logger->info('it was not possible to pick up the return methods');
                    }
                    $data[0]['title'] = 'Erro';
                    $data[0]['price'] = 'não foi possivel encontrar metodos de entrega';
                    return json_encode($data);
                }
            }elseif($request['tipobusca'] == 2){
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $regionFactory = $objectManager->create('Magento\Directory\Model\ResourceModel\Region\CollectionFactory')->create();
                $regionFactory->addFieldToFilter('country_id', array('eq' => $request['country']));
                $regions = $regionFactory->getData();
                $html = '';
                foreach($regions as $item){
                    $html .= '<option value="'.$item['region_id'].'">'.$item['default_name'].'</option>';
                }
                if( count($regions)) {
                    return ' <label class="label" for="state"><span>' . __('State/Province') . '</span></label>
                <div class="control">
                    <select id="state">
                        <option value="">'.__('--Please Select--').'</option>
                    ' . $html . '
                    </select>
                </div>';
                }else{
                    return ' <label class="label" for="state"><span>' . __('State/Province') . '</span></label>
                <div class="control">
                     <input type="text" id="state" />
                </div>';
                }
            }
        } catch (\Exception $e) {
            $data[0]['title'] = 'Erro';
            $data[0]['price'] = $e->getMessage();
            return json_encode($data);
        }
    
    }
}