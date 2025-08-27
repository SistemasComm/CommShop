<?php

namespace MestreMage\ShippingQuoteProductPage\Block\Catalog\Product\View;

class CalcularFrete extends \Magento\Catalog\Block\Product\View\AbstractView {

    public function isSalable(){

        if ($this->getProduct()->getData('type_id') == 'virtual' OR $this->getProduct()->getData('type_id') == 'downloadable') {
            return false;
        }
        
        return $this->getProduct()->isSalable();
    }
    public function getId(){
        return $this->getProduct()->getId();
    }
    public function getCountry()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $countrySourceModel = $objectManager->get('Magento\Directory\Model\Config\Source\Country');
        $countries = $countrySourceModel->toOptionArray();
        $html = '';
        foreach ($countries as $country) {
            $html .= '<option value="'.$country['value'].'">'.$country['label'].'</option>';
        }
        return $html;
    }
    public function getconfPanel($valor){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
       return $scopeConfig->getValue($valor, $storeScope);
    }
    public function getScripts(){
       $msgProdConfig = ( $this->getconfPanel('shippingquoteproductpage/loja/txtprodconf') ? $this->getconfPanel('shippingquoteproductpage/loja/txtprodconf') : __("Select product options") );
       $msgBtnsearch = ( $this->getconfPanel('shippingquoteproductpage/loja/txtsearch') ? $this->getconfPanel('shippingquoteproductpage/loja/txtsearch') : __("Shipping Quote") );
       $msgBtnloading = ( $this->getconfPanel('shippingquoteproductpage/loja/txtloading') ? $this->getconfPanel('shippingquoteproductpage/loja/txtloading') : __("Waiting...") );
        $msgErroCep = ( $this->getconfPanel('shippingquoteproductpage/loja/txterrocep') ? $this->getconfPanel('shippingquoteproductpage/loja/txterrocep') : __("Could not simulate freight") );

        return '<script>var country_linha = "'.__('Country'). '";var state_linha = "'.__('State/Province'). '";var msg_prod_confg = "'.__($msgProdConfig). '"; var txt_butom_buscar = "'.__($msgBtnsearch). '"; var txt_butom_carregar = "'.__($msgBtnloading). '";  var msg_cep_confg = "'.__($msgErroCep). '";  </script>';

    }

    public function getLocale(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $getLocale = $objectManager->get('Magento\Framework\Locale\Resolver');
        return $getLocale->getLocale();
    }

}