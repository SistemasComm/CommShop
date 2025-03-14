<?php

namespace MestreMage\SigepWeb\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use MestreMage\Core\Model\ModulesManagement;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class PrintTags
 * @todo refactor - parent class deprecated
 * @package MestreMage\SigepWeb\Controller\Adminhtml\Order
 */
class PrintPlp extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction {
    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct( Context $context, Filter $filter, CollectionFactory $collectionFactory )
    {
        parent::__construct( $context, $filter );
        $this->collectionFactory = $collectionFactory;
    }


    /**
     * Delete selected orders
     *
     * @param AbstractCollection $collection
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function massAction( AbstractCollection $collection )
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        if(!ModulesManagement::testModule($scopeConfig->getValue(\base64_decode('c2VjdGlvbl9tb2R1bGVfc2lnZXB3ZWIvc2lnZXB3ZWIvYWN0aXZlX2hhc2g='), 
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE),'MestreMage_SigepWeb')) throw new \Magento\Framework\Exception\LocalizedException(__(\base64_decode('T3DDp8OjbyBpbmRpc3Bvbml2ZWw=')));
        $orederId = '';
        foreach ( $collection->getItems() as $order ) {
            $orederId .= $order->getEntityId().',';
        }
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        return $this->resultRedirectFactory->create()->setPath($storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'mestremage/pdf?param=print_plp&order_ids='.$orederId.'&_='.time());

    }

}
