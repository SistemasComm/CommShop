<?php

namespace Clearsale\Base\Model\Api;

class Request
{
    const POST                      =   'POST';
    const GET                       =   'GET';
    const PUT                       =   'PUT';

    const SEND_ORDER                =   'v1/orders';
    const SEND_CHARGEBACK           =   'v1/chargeback';
    const AUTHENTICATE              =   'v1/authenticate';
    const GET_ORDER                 =   'v1/orders/%s/status';
    const PROFILER                  =   'v1/accounts';
    const PROFILER_LOGIN            =   'v1/Accounts/Login';
    const PROFILER_LOGOUT           =   'v1/Accounts/Logout';
    const PROFILER_RESET_PASSWORD   =   'v1/Accounts/ResetPassword';

    const HOMOLOG_URL               =   'https://homologacao.clearsale.com.br/api/';
    const PRODUCTION_URL            =   'https://api.clearsale.com.br/';

    const ENVIRONMENT_PATH_CONFIG   =   'clearsale_base/settings/environment_type';

    const AUTH_USER_NAME            =   'clearsale_base/settings/clearsale_product_user';
    const AUTH_USER_PWD             =   'clearsale_base/settings/clearsale_product_password';

    const EXIST_ORDER_KEY           =   'exist-order';

    const DEBUG                     =   'clearsale_base/settings/debug';

    protected $_requestUrl;

    protected $_token;

    protected $_scopeConfig;

    protected $_logger;

    protected $_inputParams;

    protected $_apiResponse;

    protected $_isSuccess = false;

    protected $_errors = [];

    protected $_retryCount = 0;

    protected $_httpMethod;

    protected $_apiMethod;

    protected $_postData;

    protected $_hasExistingOrders = false;

    protected $_responseHeader;

    protected $_responseBody;

    protected $_httpResponseCode;

    protected $_existingOrders;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Clearsale\Base\Logger\Logger $logger
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_logger = $logger;
    }

    public function sendApiRequest($product, $apiMethod, $httpMethod)
    {
        try {
            if ($this->canSendRequest($product, $apiMethod)) {
                $this->setInputParams($apiMethod, $httpMethod, $product->getPostData());

                $this->makeRequest();

                if (!$this->_isSuccess && $this->canRetry()) {
                    $this->_retryCount++;
                    $this->requestToken($product);
                    $this->sendApiRequest($product, $apiMethod, $httpMethod);
                }
            }
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }

        $this->resetRetry();

        return $this->_isSuccess;
    }

    protected function makeRequest()
    {
        $this->resetRequestStatus();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
        curl_setopt($curl, CURLOPT_URL, $this->_requestUrl);

        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Expect:"]);

        $header = $this->getHeader();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $data = $this->object_to_array($this->getPostData());
        if (is_array($data)) {
            array_walk_recursive($data, function (&$item, $key) {
                if($item){
                    if (!mb_detect_encoding($item, 'utf-8', true)) {
                        $item = utf8_encode($item);
                    }
                }
            });
        }

        if ($this->isPost()) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        if ($this->isPut()) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
		
		$this->_logger->info($response . " >>> Request makeRequest");

        $this->setApiResponse($response);

        if ($this->debugEnabled()) {
            $this->logRequest();
        }

        $this->checkResponse($curl);

        curl_close($curl);

        return $curl;
    }

    protected function object_to_array($obj)
    {
        //only process if it's an object or array being passed to the function
        if(is_object($obj) || is_array($obj)) {
            $ret = (array) $obj;
            foreach($ret as &$item) {
                //recursively process EACH element regardless of type
                $item =$this->object_to_array($item);
            }
            return $ret;
        }
        //otherwise (i.e. for scalar values) return without modification
        else {
            return $obj;
        }
    }

    protected function resetRetry()
    {
        $this->_retryCount = 0;
    }

    protected function logRequest()
    {
        $request = 'REQUEST = METHOD: ' . $this->_apiMethod . ' POST ' .
            json_encode($this->getPostData()) . 'HEADER ' . print_r($this->getHeader(), true);

        $this->_logger->info($request);
    }

    protected function resetRequestStatus()
    {
        $this->_isSuccess = false;
        $this->_httpResponseCode = 0;
    }

    protected function getPostData()
    {
        if ($this->_apiMethod == self::AUTHENTICATE) {
            return [	'name' => $this->_scopeConfig->getValue(
                self::AUTH_USER_NAME,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
                'password' => $this->_scopeConfig->getValue(
                    self::AUTH_USER_PWD,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            ];
        } elseif ($this->_postData) {
            return $this->_postData;
        }

        return false;
    }

    protected function debugEnabled()
    {
		return true;
    }

    protected function checkResponse($curl)
    {
        $this->formatResponse();

        if ($this->debugEnabled()) {
            $this->logResponse();
        }

        if (curl_errno($curl) != 0) {
            $this->_logger->info(curl_errno($curl));
            $this->_logger->info('Curl Error.');
        }

        $this->_httpResponseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($this->_httpResponseCode == '200') {
            $this->checkCode200();
        }

        if ($this->_httpResponseCode == '400') {
            $this->checkExistingOrders();
        }
    }

    protected function checkCode200()
    {
        $http_code = $this->_responseHeader['http_code'];

        if ($http_code == 'HTTP/1.1 100 Continue') {
            $this->resetSuccess();
            return;
        }

        $this->_isSuccess = true;
    }

    protected function logResponse()
    {
        $response = print_r(
            ['RESPONSE'    => [   'header' => print_r($this->_responseHeader, true),
            'body'	 => print_r($this->_responseBody, true)]
        ],
            true
        );
        $this->_logger->info($response);
    }

    protected function checkExistingOrders()
    {
        if ($this->_responseBody) {
            $modelState = $this->_responseBody->ModelState;
            $existing_orders = 'existing-orders';
            $existingOrders = isset($modelState->{$existing_orders}) ? $modelState->{$existing_orders} : null;

            if ($existingOrders) {
                $existOrderArray[self::EXIST_ORDER_KEY] = [];

                foreach ($existingOrders as $order) {
                    array_push($existOrderArray[self::EXIST_ORDER_KEY], $order);
                }

                $this->_existingOrders = $existOrderArray;
                $this->_hasExistingOrders = true;
            }
        }
    }

    public function getExistingOrders()
    {
        return $this->_existingOrders;
    }

    public function hasExistingOrders()
    {
        return $this->_hasExistingOrders;
    }

    public function getResponseHeader()
    {
        return $this->_responseHeader;
    }

    public function getResponseBody()
    {
        return $this->_responseBody;
    }

    protected function setInputParams($apiMethod, $httpMethod, $postData)
    {
        $this->_httpMethod = $httpMethod;
        $this->_apiMethod = $apiMethod;

        $this->_postData = $postData;
    }

    public function getInputParams()
    {
        return $this->_inputParams;
    }

    protected function canRetry()
    {
        $validMethod = null;
		
		if(isset($this->_inputParams['apiMethod']))
			$validMethod = $this->_inputParams['apiMethod'] != self::AUTHENTICATE;

        if (($this->_httpResponseCode == '401' || $this->_httpResponseCode == '403')
            && $validMethod && $this->_retryCount < 1) {
            return true;
        }

        if ($this->_httpResponseCode == '500' || $this->_httpResponseCode == '400') {
            $this->addError('Request Problem');
        }

        if (!$validMethod) {
            $this->addError("Can't authenticate code: " .
                $this->_httpResponseCode . ", Method: " . $this->_httpMethod);
        }

        return false;
    }

    public function getResponseCode()
    {
        return $this->_httpResponseCode;
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

    protected function setApiResponse($response)
    {
        $this->_apiResponse = $response;
    }

    public function getApiResponse()
    {
        return $this->_apiResponse;
    }

    protected function isPost()
    {
        return $this->_httpMethod == self::POST;
    }

    protected function isPut()
    {
        return $this->_httpMethod == self::PUT;
    }

    protected function getHeader()
    {
        if ($this->_httpMethod == self::GET) {
            $header = ["Accept: application/json"];
        } else {
            $header = ['Content-Type: application/json'];
        }

        if ($this->_apiMethod != self::AUTHENTICATE) {
            array_push($header, "Authorization: Bearer {$this->_token}");
        }

        return $header;
    }

    protected function setRequestUrl($apiMethod)
    {
        if (!$apiMethod) {
            $this->_logger->info('Missing Api Method.');
        }

        $baseUrl = $this->getApiBaseUrl();

        $this->_requestUrl = $baseUrl . $apiMethod;
    }

    protected function canSendRequest($product, $apiMethod)
    {
        $this->_token = $this->getToken($product);

        $this->setRequestUrl($apiMethod);

        return true;
    }

    public function getRequestUrl($apiMethod)
    {
        return $this->_requestUrl;
    }

    public function getToken($product)
    {
        try {
            $token = $product->getToken();

            if (!$token) {
                $token = $this->requestToken($product);
            }
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }

        return $token->getToken();
    }

    protected function requestToken($product)
    {
        $this->_httpMethod = self::POST;
        $this->_apiMethod = self::AUTHENTICATE;

        $this->setRequestUrl($this->_apiMethod);

        $this->makeRequest();

        if ($this->_isSuccess) {
            $this->resetSuccess();
            $token = $product->saveToken();

            return $token;
        }
    }

    protected function resetSuccess()
    {
        $this->_isSuccess = false;
    }

    public function formatResponse()
    {
        $apiResponse = $this->getApiResponse();

        if (strpos($apiResponse, "\r\n\r\n") > 0) {
            list($header, $body) = explode("\r\n\r\n", $apiResponse);
        } else {
            $body = $apiResponse;
        }

        $responseHeader = $this->getHeaderAsArray($apiResponse);

        $body = json_decode($body);

        $this->_responseHeader = $responseHeader;
        $this->_responseBody = $body;
    }

    protected function getHeaderAsArray($response)
    {
        $headers = [];

        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                list($key, $value) = explode(': ', $line);

                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    protected function getApiBaseUrl()
    {
        $configValue = $this->_scopeConfig->getValue(
            self::ENVIRONMENT_PATH_CONFIG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        switch ($configValue) {
            case 0:
                return self::HOMOLOG_URL;

            case 1:
                return self::PRODUCTION_URL;

            default:
                return false;
        }
    }
}
