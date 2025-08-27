<?php

namespace MestreMage\ShippingQuoteProductPage\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function getLog($log){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
   if($scopeConfig->getValue('shippingquoteproductpage/geral/log', $storeScope)) {
       $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/MestreMage_buscarCep.log');
       $logger = new \Zend_Log();
       $logger->addWriter($writer);
       $logger->info($log);
   }
    }


}
