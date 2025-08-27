<?php
namespace MestreMage\Sortby\Plugin\Catalog\Block;

class Toolbar
{

    /**
    * Plugin
    *
    * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
    * @param \Closure $proceed
    * @param \Magento\Framework\Data\Collection $collection
    * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
    */
    public function aroundSetCollection(
    \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
    \Closure $proceed,
    $collection
    ) {
    $currentOrder = $subject->getCurrentOrder();
    $result = $proceed($collection);

        if($this->getCoreConfig('filter_by/configuracao/habilitado')) {
            if ($currentOrder) {
                if ($currentOrder == 'high_to_low') {
                    $subject->getCollection()->setOrder('price', 'desc');
                } elseif ($currentOrder == 'low_to_high') {
                    $subject->getCollection()->setOrder('price', 'asc');
                } elseif ($currentOrder == 'high_to_new') {
                    $subject->getCollection()->setOrder('updated_at', 'DESC');
                } elseif ($currentOrder == 'discount_summary') {
                    $subject->getCollection()->setOrder('special_price', 'desc');
                } elseif ($currentOrder == 'sort_by_best_seller') {

                    $subject->getCollection()->getSelect()->joinLeft(
                        'sales_order_item',
                        'e.entity_id = sales_order_item.product_id',
                        array('qty_ordered' => 'SUM(sales_order_item.qty_ordered)'))
                        ->group('e.entity_id')
                        ->order('qty_ordered asc');

                } elseif ($currentOrder == 'ratings_summary') {
                    // em breve
                }
            }
        }
        return $result;
    }

public function getCoreConfig($valor){
    $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
    return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
}


}