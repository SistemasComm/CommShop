<?php

namespace Clearsale\Total\Model;

class Request extends \Magento\Framework\Model\AbstractModel
{
    protected $_postData;
    protected $_helper;
    protected $_logger;
    protected $_baseHelper;

    public function __construct(
        \Clearsale\Total\Helper\Data $helper,
        \Clearsale\Base\Helper\Data $baseHelper,
		\Clearsale\Base\Logger\Logger $logger
    ) {
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_baseHelper = $baseHelper;
    }

    public function getPostData($orderCollection)
    {
        try {
            $this->init($orderCollection);

            $this->preparePost($orderCollection);
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
            return false;
        }

        return $this->_postData;
    }

    public function clearPostData()
    {
        $this->_postData = false;
    }

    public function getBillingData()
    {
        $validate = $this->_helper->validate('BILLING');

        $billingData =  [
            'clientID' => (string) $validate->getClientId(),
            'type' => $validate->getType(),
            'primary_document' => $validate->getPrimaryDocument(),
            'secondary_document' => $validate->getSecondaryDocument(),
            'name' => $validate->getName(),
            'birth_date' => $validate->getBirthDate(),
            'email' => $validate->getEmail() ,
            'gender' => $validate->getGender() ,
            'address' => $validate->getAddress(),
            'phones' => $validate->getPhones()
        ];

        return $billingData;
    }

    public function getPaymentsData()
    {
        $validate = $this->_helper->validate('PAYMENTS');

        return $validate->getPaymentData();
    }

    public function getShippingData()
    {
        $validate = $this->_helper->validate('SHIPPING');

        $shippingData =  [
            'clientID' => (string) $validate->getClientId(),
            'type' => $validate->getType(),
            'primary_document' => $validate->getPrimaryDocument(),
            'secondary_document' => $validate->getSecondaryDocument(),
            'name' => $validate->getName(),
            'birth_date' => $validate->getBirthDate(),
            'email' => $validate->getEmail(),
            'gender' => $validate->getGender(),
            'address' => $validate->getAddress(),
            'phones' => $validate->getPhones(),
            'price' => (float) $validate->getShippingPrice()
        ];

        return $shippingData;
    }

    /**
     * Returns data of purchase information
     *
     * @return array
     */
    public function getPurchaseInformation()
    {
        $validate = $this->_helper->validate('PURCHASE_INFO');

        return $validate->getPurchaseInfoData();
    }

    public function getItemsData()
    {
        $validate = $this->_helper->validate('ITEMS');

        return $validate->getItemsArray();
    }

    protected function init($orderCollection)
    {
        $this->clearPostData();

        $this->_helper->clear();
        $this->_helper->initValidate($orderCollection);
    }

    protected function preparePost($orderCollection)
    {
        $this->_postData = [
            'order_number' => $orderCollection->getIncrementId(),
            'session_id'    => $orderCollection->getCsSessionId(),
            'order_date'    => $orderCollection->getCreatedAt(),
            'order_email'   => $orderCollection->getCustomerEmail(),
            'item_value'    => (float) $orderCollection->getSubtotal(),
            'total_value'   => (float) $orderCollection->getGrandTotal(),
            'ip'            => (string) $orderCollection->getRemoteIp(),
            'purchaseInformation' => $this->getPurchaseInformation(),
            'billing'       => $this->getBillingData(),
            'shipping'      => $this->getShippingData(),
            'items'         => $this->getItemsData(),
            'payments'      => $this->getPaymentsData()
        ];
    }
}
