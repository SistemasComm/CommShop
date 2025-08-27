<?php

namespace FCheckout\CpfCnpj\Observer;


use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Message\ManagerInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use \Magento\Framework\App\Request\Http;
use \FCheckout\CpfCnpj\Helper\Data as HelperData;

class CustomerRegisterBefore implements ObserverInterface
{
    protected $_customerCollectionFactory;
    protected $_toreManagerInterface;
    protected $_ManagerInterface;
    protected $request;
    protected $helperData;
    var $repeat = 0;

    public function __construct(
        CollectionFactory $customerCollectionFactory,
        StoreManagerInterface $StoreManagerInterface,
        ManagerInterface $ManagerInterface,
        Http $request,
        HelperData $helperData
    ) {
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_toreManagerInterface = $StoreManagerInterface;
        $this->_ManagerInterface = $ManagerInterface;
        $this->request = $request;
        $this->helperData = $helperData;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Action\Context')->getRequest();
        if (!$this->repeat) {

            if ((int)$this->getCoreConfig("mmcpfcnpj/loja/valid_inscricao_estadual")) {
                if ($request->getFullActionName() == 'customer_account_createpost') {
                    $inscricao_estadual = $request->getParam('inscricao_estadual');
                    $taxvat = $request->getParam('taxvat');

                    if (empty($taxvat) && !$this->helperData->ValidInscricaoEstadual($inscricao_estadual)) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('Inscrição Estadual Inválida'));
                    }
                } else {
                    $postJson = json_decode(file_get_contents('php://input'));
                    if (isset($postJson->inscricao_estadual)) {
                        $inscricao_estadual = $postJson->inscricao_estadual;
                        $taxvat = (isset($postJson->taxvat) ? $postJson->taxvat : "");
    
                        if (empty($taxvat) && !$this->helperData->ValidInscricaoEstadual($inscricao_estadual)) {
                            throw new \Magento\Framework\Exception\LocalizedException(__('Inscrição Estadual Inválida'));
                        }
                    }
                }
            }


            if ($this->getCoreConfig("mmcpfcnpj/loja/taxvat_unico")) {

                if ($request->getFullActionName() == 'customer_account_createpost') {
                    $customer = $observer->getCustomer();
                    $taxvat = $customer->getData('taxvat');
                    $customerCollection = $this->_customerCollectionFactory->create();
                    $collection = $customerCollection->addAttributeToSelect('taxvat')->load();
                    foreach ($collection as $item) {
                        if ($this->onlyNumber($item->getData('taxvat')) == $this->onlyNumber($taxvat)) {
                            $this->isTaxvatRegister($taxvat);
                            break;
                        }
                    }
                }
            }

            $this->repeat++;
        }
    }

    public function getCoreConfig($valor)
    {
        $scopeConfig = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    public function onlyNumber($str)
    {
        return preg_replace("/[^0-9]/", "", $str);
    }

    public function isTaxvatRegister($taxvat)
    {
        $msgTaxvat = $this->getCoreConfig("mmcpfcnpj/loja/msg_unique_taxvat");
        $msgTaxvat = str_replace('{{taxvat}}', $taxvat, $msgTaxvat);
        $this->_ManagerInterface->addErrorMessage($msgTaxvat);
        $baseUrl = $this->_toreManagerInterface->getStore()->getBaseUrl();
        header('location: ' . $baseUrl . 'customer/account/login/');
        exit();
    }
}
