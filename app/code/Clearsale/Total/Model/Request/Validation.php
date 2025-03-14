<?php

namespace Clearsale\Total\Model\Request;

abstract class Validation extends \Magento\Framework\Model\AbstractModel
{
	/**
     * Logging instance
     * @var \Clearsale\Base\Logger\Logger
     */
    protected $_logger;
    protected $_scopeConfig;
	
    public $_inputParams;
    public $_baseHelper;
    public $_error;
    public $_addressValidation;
    public $_customer;
    public $_order;

    /**
     * Validation constructor.
     * @param \Clearsale\Base\Helper\Data $baseHelper
     * @param \Clearsale\Total\Helper\Data $baseHelper
     * @param \Clearsale\Base\Logger\Logger $logger
     * @param AddressValidation $addressValidation
     */
    public function __construct(
        \Clearsale\Base\Helper\Data $baseHelper,
        \Clearsale\Base\Logger\Logger $logger,
        \Clearsale\Total\Model\Request\AddressValidation $addressValidation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_baseHelper = $baseHelper;
		$this->_logger = $logger;
        $this->_addressValidation = $addressValidation;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param $inputParams
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate($inputParams)
    {
        $this->_inputParams = $inputParams;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_customer = $this->_inputParams['customer'];
        $this->_order = $this->_inputParams['order'];

        if ($this->hasCustomer()) {
            $this->_customer = $objectManager->create('Magento\Customer\Model\Customer')->load($this->_customer->getId());
        }

        if (!$this->isValidated()) {
			$this->_logger->info($this->getErrorMessage());
			
            $getStatus = $this->_order->getStatus();
            if($getStatus != $this->_scopeConfig->getValue('clearsale_total/manage_status/review_status'))
            {
                //$this->_order->setStatus('review_clearsale');
                $this->_order->setStatus(
                    $this->_scopeConfig->getValue('clearsale_total/manage_status/review_status')
                );

                $this->_order->addStatusHistoryComment($this->getErrorMessage());
                
            }
            $this->_order->save();
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasCustomer()
    {
        return $this->_inputParams['customer'] != null;
    }

    /**
     * @return mixed
     */
    abstract public function isValidated();

    /**
     * @param $errorMessage
     */
    public function addError($errorMessage)
    {
        $this->_error = $errorMessage;
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->_error;
    }
}
