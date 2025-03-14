<?php

namespace MestreMage\Sortby\Plugin\Catalog\Model;

class Config
{
    public function afterGetAttributeUsedForSortByArray(
    \Magento\Catalog\Model\Config $catalogConfig,
    $options
    ) {

        $all_filtros = ['high_to_low','low_to_high','high_to_new','discount_summary','sort_by_best_seller','ratings_summary','price'];
        foreach($all_filtros as $item){
            if($this->getCoreConfig('filter_by/configuracao/habilitado')) {

                if(!$this->getCoreConfig('filter_by/filter_config/'.$item)){
                    if(isset( $options[$item])){
                        unset( $options[$item]);
                    }
                }
            }else{
                if($item != 'price'){
                    if(isset( $options[$item])){
                        unset( $options[$item]);
                    }
                }
            }
        }
        return $options;
    }
    public function getCoreConfig($valor){
        $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }
}