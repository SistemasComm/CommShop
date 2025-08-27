<?php

namespace Clearsale\Base\Model\Products;

use \Magento\Framework\Exception\NoSuchEntityException;

abstract class AbstractProduct
{
    protected $_code;

    protected $_api;

    protected $_helper;

    protected $_postData = array();

    protected $_tokenRepository;

    protected $_tokenFactory;

    protected $_orderRepository;

    protected $_orderFactory;

    protected $_logger;

    protected $_errors = array();

    protected $_success = array();

    public $_hasExistingOrders = false;

    public function __construct(
        \Clearsale\Base\Helper\Data $helper,
        \Clearsale\Base\Model\Api\Request $request,
        \Clearsale\Base\Model\TokenRepository $tokenRepository,
        \Clearsale\Base\Model\TokenFactory $tokenFactory,
        \Clearsale\Base\Model\OrderFactory $orderFactory,
        \Clearsale\Base\Model\OrderRepository $orderRepository,
		\Clearsale\Base\Logger\Logger $logger
    ) {
        $this->_helper = $helper;
        $this->_api = $request;
        $this->_tokenRepository = $tokenRepository;
        $this->_logger = $logger;
        $this->_tokenFactory = $tokenFactory;
        $this->_orderFactory = $orderFactory;
        $this->_orderRepository = $orderRepository;
    }

    public function getHelper()
    {
        return $this->_helper;
    }

    public function getCode()
    {
        return $this->_code;
    }

    abstract public function getProductId();

	protected function makePostData($postData, $isProfiler = false, $isChargeback = false, $isUpdated = false)
	{
	    if($isProfiler){
	        $post = $this->getAutoPost($postData);
	        $this->addToPost($post, $isProfiler);
	        return;
        }

        if ($isChargeback) {
            $post = $this->getChargebackPost($postData);
            $this->addToPost($post, true);
            return;
        }

        if ($isUpdated) {
            $post = $this->getUpdateStatusPost($postData);
            $this->addToPost($post, true);
            return;
        }

		if (is_array($postData))
		{
			foreach ($postData as $post)
			{
				$post = $this->getPost($post);
				$this->addToPost($post);
			}
		}
		else
		{
			$post = $this->getPost($postData);
			$this->addToPost($post);
		}
	}

	protected function getAutoPost($post){
	    if(!$post || !is_array($post)){
	        return new \stdClass();;
        }
        $object = new \stdClass();
        foreach ($post as $key => $value) {
            if(is_array($value)){
                foreach($value as $levelValue){
                    $object->{$key}[] = $this->getAutoPost($levelValue);
                }
                continue;
            }
            $object->{$key} = $value;
        }
        return $object;
    }

    protected function addToPost($post, $isProfiler = false)
    {
        if($isProfiler){
            $this->_postData = $post;
            return;
        }
        array_push($this->_postData, $post);
    }

    protected function getPost($post)
    {
        $requestBodyObj = new \stdClass();

        $requestBodyObj->code = $post['order_number'];
        $requestBodyObj->sessionID = $post['session_id'];
        $requestBodyObj->date = $post['order_date'];
        $requestBodyObj->email = $post['order_email'];
        $requestBodyObj->itemValue = $post['item_value'];
        $requestBodyObj->totalValue = $post['total_value'];
        $requestBodyObj->ip = $post['ip'];
        $requestBodyObj->status = 0;
        $requestBodyObj->origin = "Ecommerce Magento 2";
        $requestBodyObj->country = "Brasil";
        $requestBodyObj->product = $this->getProductId();
        $requestBodyObj->billing = $this->getBillingData($post['billing']);
        $requestBodyObj->shipping = $this->getShippingData($post['shipping']);
        $requestBodyObj->payments = $post['payments'];
        if (isset($post['purchaseInformation'])) {
            $requestBodyObj->purchaseInformation = $this->getPurchaseInfoData($post['purchaseInformation']);
        }
        if (isset($post['tickets'])){
            $requestBodyObj->tickets = $post['tickets'];
        }
        if (isset($post['items'])){
            $requestBodyObj->items = $this->getItemsData($post['items']);
        }
        return $requestBodyObj;
    }

    protected function getChargebackPost($post)
    {
        $requestBodyObj = new \stdClass();

        $requestBodyObj->orders = $post['orders'];
        if (isset($post['message'])){
            $requestBodyObj->message = $post['message'];
        }
        return $requestBodyObj;
    }

    protected function getUpdateStatusPost($post)
    {
        $requestBodyObj = new \stdClass();
        $requestBodyObj->status = $post['status'];
        return $requestBodyObj;
    }

    protected function getBillingData($billing)
    {
        $requestBillingObj = new \stdClass();
        $requestBillingObj->type = $billing['type'];
        $requestBillingObj->primaryDocument = $this->_helper->getPrimaryDocument($billing['primary_document']);
        $requestBillingObj->name = $billing['name'];
        if (isset($billing['birth_date']) && $billing['birth_date'] != null){
            $requestBillingObj->birthDate = $billing['birth_date'];
        }
        $requestBillingObj->email = $billing['email'];
        if (isset($billing['gender']) && $billing['gender'] != null){
            $requestBillingObj->gender = $this->_helper->getGender($billing['gender']);
        }
        $requestBillingObj->clientID = $billing['clientID'];
        if (isset($billing['secondary_document']) && $billing['secondary_document'] != null){
            $requestBillingObj->secondaryDocument = $billing['secondary_document'];
        }
        $requestBillingObj->address = $this->getAddress($billing['address']);
        $requestBillingObj->phones = $this->getPhones($billing['phones']);
        if (isset($billing['mother_name'])){
            $requestBillingObj->motherName = $billing['mother_name'];
        }

        return $requestBillingObj;
    }

    protected function getPurchaseInfoData($purchaseInfo)
    {
        $requestPurchaseInfoObj = new \stdClass();
        $requestPurchaseInfoObj->purchaseLogged = $purchaseInfo['purchaseLogged'];
        $requestPurchaseInfoObj->email = $purchaseInfo['email'];

        if ($purchaseInfo['purchaseLogged'] === true) {
            $requestPurchaseInfoObj->lastDateChangePassword = $purchaseInfo['lastDateChangePassword'];
            $requestPurchaseInfoObj->lastDateInsertedAddress = $purchaseInfo['lastDateInsertedAddress'];
            $requestPurchaseInfoObj->login = $purchaseInfo['login'];
        }

        return $requestPurchaseInfoObj;
    }

    protected function getAddress($address)
    {
        $requestAddressObj = new \stdClass();
        $requestAddressObj->street = $address['street'];
        $requestAddressObj->number = $address['number'];
        if (isset($address['additional_information'])) {
            $requestAddressObj->additionalInformation = $address['additional_information'];
        }
        $requestAddressObj->county = $address['county'];
        $requestAddressObj->city = $address['city'];
        $requestAddressObj->state = $address['state'];
        $requestAddressObj->country = $address['country'];
        $requestAddressObj->zipcode = $address['zipcode'];

        return $requestAddressObj;
    }

    protected function getPhones($phones)
    {
        $phonesArray = array();

        foreach ($phones as $phone) {
            $requestPhoneObj = new \stdClass();
            $requestPhoneObj->type = $phone['type'];
            $requestPhoneObj->ddi = 55;
            $requestPhoneObj->ddd = $phone['ddd'];
            $requestPhoneObj->number = $phone['number'];

            array_push($phonesArray, $requestPhoneObj);
        }

        return $phonesArray;
    }

    protected function getShippingData($shipping)
    {
        $requestShippingObj = new \stdClass();
        $requestShippingObj->type = $shipping['type'];
        $requestShippingObj->primaryDocument = $this->_helper->getPrimaryDocument($shipping['primary_document']);
        $requestShippingObj->name = $shipping['name'];
        if (isset($shipping['birth_date']) && $shipping['birth_date'] != null){
            $requestShippingObj->birthDate = $shipping['birth_date'];
        }
        $requestShippingObj->email = $shipping['email'];
        $requestShippingObj->clientID = $shipping['clientID'];
        if (isset($shipping['secondary_document']) && $shipping['secondary_document'] != null){
            $requestShippingObj->secondaryDocument = $shipping['secondary_document'];
        }
        if (isset($shipping['gender']) && $shipping['gender'] != null){
            $requestShippingObj->gender = $this->_helper->getGender($shipping['gender']);
        }
        $requestShippingObj->address = $this->getAddress($shipping['address']);
        $requestShippingObj->phones = $this->getPhones($shipping['phones']);
        $requestShippingObj->price = (float) $shipping['price'];

        return $requestShippingObj;
    }

    protected function getItemsData($items)
    {
        $itemsArray = array();

        foreach ($items as $item) {
            $requestItemsObj = new \stdClass();
            $requestItemsObj->code = $item['code'];
            $requestItemsObj->name = $item['name'];
            $requestItemsObj->value = $item['value'];
            $requestItemsObj->amount = $item['amount'];

            array_push($itemsArray, $requestItemsObj);
        }

        return $itemsArray;
    }

    protected function send($apiMethod, $httpMethod, $postData = false, $isProfiler = false)
    {
        $this->clearPostData();

        if ($postData)
        {
            $this->makePostData($postData, $isProfiler);
        }

        $response = $this->_api->sendApiRequest($this, $apiMethod, $httpMethod);

        try
        {
            if ($response && !$isProfiler) {
                $this->saveOrder();
            }
        }
        catch(\Exception $e)
        {
			$this->_logger->info($e->getMessage());
        }

        return $response;
    }

    protected function sendUpdateStatus($apiMethod, $httpMethod, $postData)
    {
        $isChargeback = false;

        $this->clearPostData();

        if ($postData)
        {
            $this->makePostData($postData, false, false, true);
        }

        $response = $this->_api->sendApiRequest($this, $apiMethod, $httpMethod);

        return $response;
    }

    protected function sendChargeback($apiMethod, $httpMethod, $postData)
    {
        $isChargeback = true;

        $this->clearPostData();

        if ($postData)
        {
            $this->makePostData($postData, false, $isChargeback);
        }

        $response = $this->_api->sendApiRequest($this, $apiMethod, $httpMethod);

        try
        {
            if ($response) {
                if (!$isChargeback) {
                    $this->saveOrder();
                }
            }
        }
        catch(\Exception $e)
        {
			$this->_logger->info($e->getMessage());
        }

        return $response;
    }

    public function clearPostData()
    {
        $this->_postData = array();
    }

    public function saveToken()
    {
        $tokenF = $this->_tokenRepository->getById($this->getProductId());

        if (!$tokenF) {
            $tokenF = $this->_tokenFactory->create();
        }

        $tokenF->setProductId($this->getProductId());
        $tokenF->setProductDescription($this->getProductDescription());

        $tokenF->setToken($this->_api->getResponseBody()->Token);
        $tokenF->setExpirationDate($this->_api->getResponseBody()->ExpirationDate);

        $repo = $this->_tokenRepository->save($tokenF);

        return $repo;
    }

    public function saveOrder()
    {
        $header = $this->_api->getResponseHeader();

        if (array_key_exists('Request-ID', $header)) {
            $requestId = $header['Request-ID'];
        } else {
            $requestId = false;
        }

        if (isset($this->_api->getResponseBody()->orders)) {
            $this->savePostOrder($requestId, $this->_api->getResponseBody());
        } else {
            $this->saveGetOrder($requestId, $this->_api->getResponseBody());
        }
    }

    protected function savePostOrder($requestId, $body)
    {
        foreach ($body->orders as $order) {
            try {
                $orderF = $this->_orderRepository->getById($order->code);

                if (!$orderF) {
                    $orderF = $this->_orderFactory->create();
                }

                $orderF->setCode($order->code);

                if ($requestId) {
                    $orderF->setRequestId($requestId);
                }

                $orderF->setStatus($order->status);
                $orderF->setScore($order->score);
                $orderF->setUpdateDate($this->_helper->getNowDateTime());

                $orderSave = $this->_orderRepository->save($orderF);

                $this->addSuccess(array('order_number' => $order->code, 'status' => $order->status));
            } catch (\Exception $e) {
                $this->addError($order->code);
            }
        }
    }

    public function getApi()
    {
        return $this->_api;
    }


    protected function saveGetOrder($requestId, $body)
    {
        try {
            $orderF = $this->_orderRepository->getById($body->code);

            if (!$orderF) {
                $orderF = $this->_orderFactory->create();
            }

            $orderF->setCode($body->code);

            if ($requestId) {
                $orderF->setRequestId($requestId);
            }

            $orderF->setStatus($body->status);
            $orderF->setScore($body->score);
            $orderF->setUpdateDate($this->_helper->getNowDateTime());

            $orderSave = $this->_orderRepository->save($orderF);

            $this->addSuccess(array('order_number' => $body->code, 'status' => $body->status));
        } catch (\Exception $e) {
            $this->addError($body->code);
			$this->_logger->info($e->getMessage());
        }
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    protected function addError($message)
    {
        if ($message) {
            array_push($this->_errors, $message);
        }
    }

    public function getSuccess()
    {
        return $this->_success;
    }

    protected function addSuccess($message)
    {
        if ($message) {
            array_push($this->_success, $message);
        }
    }

    public function isValidToken()
    {
        if (!$this->_tokenFactory) {
            return false;
        }

        if ($this->_tokenFactory->getExpirationDate() < $this->_helper->getNowDateTime()) {
            return false;
        }

        return true;
    }
    public function getToken()
    {
        try {
			$token = $this->_tokenRepository->getById($this->getProductId());
			if($token && ($token->getExpirationDate() < $this->_helper->getNowDateTime())){
				return false;
			}
        } catch (NoSuchEntityException $e) {
			$this->_logger->info($e->getMessage() . " >>> AbstractProduct getToken");
            return false;
        }

        return $token;
    }

    public function getLogger()
    {
        return $this->_logger;
    }

    public function setPostData($postData)
    {
        $this->_postData = $postData;
    }

    public function getPostData()
    {
        return $this->_postData;
    }
}
