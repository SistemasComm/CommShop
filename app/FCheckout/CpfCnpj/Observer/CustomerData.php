<?php

namespace FCheckout\CpfCnpj\Observer;

use \Magento\Framework\App\RequestInterface;
use \Magento\Customer\Model\Session;

class CustomerData implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        RequestInterface $request,
        Session $session
    ) {
        $this->_request = $request;
        $this->_customer = $session->getCustomer();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getCustomer();
        $taxvat = preg_replace("/[^0-9]/", "", $customer->getTaxvat());

        if ($taxvat) {
            if (strlen($taxvat) > 11) {
                $groupId = $this->getCoreConfig("mmcpfcnpj/loja/customer_group_cnpj");
            } else {
                $groupId = $this->getCoreConfig("mmcpfcnpj/loja/customer_group_cpf");
            }

            if(isset($groupId) && $groupId!=""){
                $customer->setGroupId($groupId);
                $customer->save();
            }
        }

    }

    public function getCoreConfig($valor){
        $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }
}
