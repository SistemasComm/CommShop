<?php

namespace MestreMage\CpfCnpj\Observer;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use MestreMage\CpfCnpj\Observer\CustomerRegisterBefore;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;

class SalesOrderPlaceBefore implements ObserverInterface
{

    public function __construct(
        CollectionFactory $customerCollectionFactory,
        CustomerRegisterBefore $customerRegisterBefore,
        Session $session
    ) {
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->customerRegisterBefore = $customerRegisterBefore;
        $this->session = $session;
    }

    public function execute(Observer $observer)
    {
        try {
            $postJson = json_decode(file_get_contents('php://input'));
            if ($this->customerRegisterBefore->getCoreConfig("mmcpfcnpj/loja/taxvat_unico")) {
                if (!$this->session->isLoggedIn()) {
                    $msgTaxvat = $this->customerRegisterBefore->getCoreConfig("mmcpfcnpj/loja/msg_unique_taxvat");

                    if (isset($postJson->billingAddress->vatId)) {
                        $taxvat = $postJson->billingAddress->vatId;
                        $msgTaxvat = str_replace('{{taxvat}}', $taxvat, $msgTaxvat);
                        $customerCollection = $this->_customerCollectionFactory->create();
                        $collection = $customerCollection->addAttributeToSelect('taxvat')->load();
                        foreach ($collection as $item) {
                            if ($this->customerRegisterBefore->onlyNumber($item->getData('taxvat')) == $this->customerRegisterBefore->onlyNumber($taxvat)) {
                                throw new \Magento\Framework\Exception\StateException(__($msgTaxvat));
                                break;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(__($e->getMessage()));
        }
    }
}
