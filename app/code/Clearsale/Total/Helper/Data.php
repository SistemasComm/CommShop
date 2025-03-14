<?php

namespace Clearsale\Total\Helper;

use Magento\Store\Model\ScopeInterface;
use Clearsale\Base\Helper\Data as BaseHelper;

class Data
{
    const FILTER_STATUS_PATH            =   'clearsale_total/manage_status/filter_status';
    const ANALYZE_STATUS_PATH           =   'clearsale_total/manage_status/analyze_status';
    const APPROVED_STATUS_PATH          =   'clearsale_total/manage_status/approved_status';
    const MANUAL_APPROVED_STATUS_PATH   =   'clearsale_total/manage_status/manual_approved_status';
    const REPROVED_STATUS_PATH          =   'clearsale_total/manage_status/reproved_status';
    const REVIEW_STATUS_PATH			=   'clearsale_total/manage_status/review_status';
    const ENABLEDPATH                   =   'clearsale_total/settings/enabled';
    const PAYMENTSPATH                  =   'clearsale_total/payment_methods/allowed_payment_methods';
    const MASS_ORDER_QTY_PATH           =   'clearsale_total/settings/mass_order_qty';
    const MINIMAL_DATE			        =   'clearsale_total/settings/minimal_date';
    const CANCEL_IF_REPROVED            =   'clearsale_total/manage_status/cancel_if_reproved';
    const GENERATE_INVOICE              =   'clearsale_total/manage_status/generate_invoice';

    public $_customerModel;

    public $_orderModel;

    public $_inputParams;

    protected $_customerRepository;

    protected $_orderInterface;

    protected $_addressTypeValidation;

    protected $_itemsValidation;

    protected $_billingAddress;

    protected $_shippingAddress;

    protected $_items;

    protected $_paymentValidation;

    protected $_purchaseInfoValidation;

    protected $_scopeConfig;

    protected $_params;

    public function __construct(
        \Clearsale\Total\Model\Request\AddressTypeValidation $addressTypeValidation,
        \Clearsale\Total\Model\Request\ItemsValidation $itemsValidation,
        \Clearsale\Total\Model\Request\PaymentValidation $paymentValidation,
        \Clearsale\Total\Model\Request\PurchaseInfoValidation $purchaseInfoValidation,
        \Magento\Sales\Api\OrderRepositoryInterface $orderInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->_orderInterface = $orderInterface;
        $this->_customerRepository = $customerRepository;
        $this->_addressTypeValidation = $addressTypeValidation;
        $this->_itemsValidation = $itemsValidation;
        $this->_paymentValidation = $paymentValidation;
        $this->_purchaseInfoValidation = $purchaseInfoValidation;
        $this->_scopeConfig = $scopeConfig;
    }

    public function initValidate($orderColletion)
    {
        $this->init($orderColletion);
    }

    public function validate($validateType)
    {
        switch ($validateType) {
            case 'BILLING':
                if ($this->_addressTypeValidation->validate($this->getParamsForType($validateType))) {
                    return $this->_addressTypeValidation;
                }
                break;
            case 'SHIPPING':
                if ($this->_addressTypeValidation->validate($this->getParamsForType($validateType))) {
                    return $this->_addressTypeValidation;
                }
                break;
            case 'ITEMS':
                if ($this->_itemsValidation->validate($this->_inputParams)) {
                    return $this->_itemsValidation;
                }
                break;
            case 'PAYMENTS':
                if ($this->_paymentValidation->validate($this->_inputParams)) {
                    return $this->_paymentValidation;
                }
                break;
            case 'PURCHASE_INFO':
                if ($this->_purchaseInfoValidation->validate($this->_inputParams)) {
                    return $this->_purchaseInfoValidation;
                }
                break;
            default:
                return false;
        }
    }

    public function clear()
    {
        $this->_inputParams = false;
    }

    protected function getParamsForType($type)
    {
        $this->_inputParams['address'] = $type == 'SHIPPING' ? $this->_shippingAddress : $this->_billingAddress;

        return $this->_inputParams;
    }

    protected function init($orderColletion)
    {
        if (!$this->_inputParams) {
            $this->_orderModel = $this->_orderInterface->get($orderColletion->getEntityId());

            $this->loadCustomer($orderColletion->getCustomerId());

            $this->_billingAddress = $this->_orderModel->getBillingAddress();
            $this->_shippingAddress = $this->_orderModel->getShippingAddress();

            $this->_items = $this->_orderModel->getAllItems();

            $this->_inputParams = ['order' => $this->_orderModel,
                'customer' => $this->_customerModel,
                'items'				=> $this->_items,
                'payment'	=> $orderColletion['payment_method'],
            ];
        }
    }

    public function getMassOrderQuantity()
    {
        $qty = 1;
        $configQty = $this->_scopeConfig->getValue(
            self::MASS_ORDER_QTY_PATH,
            ScopeInterface::SCOPE_STORE
        );
        if ($configQty) {
            if ($configQty > $qty) {
                $qty = $configQty;
            }
        }

        return $qty;
    }
	
	public function getMinimalDate()
    {
        $configDate = $this->_scopeConfig->getValue(
            self::MINIMAL_DATE,
            ScopeInterface::SCOPE_STORE
        );
        if ($configDate) {
			$configDateTime = $configDate." 00:00:00";
			return $configDateTime;
        }

        return false;
    }

    public function getStatusCode($status)
    {
        switch ($status) {
            case BaseHelper::ANALYZE_STATUS:
                return $this->_scopeConfig->getValue(
                    self::ANALYZE_STATUS_PATH,
                    ScopeInterface::SCOPE_STORE
                );

            case BaseHelper::APPROVED_STATUS:
                return $this->_scopeConfig->getValue(
                    self::APPROVED_STATUS_PATH,
                    ScopeInterface::SCOPE_STORE
                );

            case BaseHelper::MANUAL_APPROVED_STATUS:
                return $this->_scopeConfig->getValue(
                    self::MANUAL_APPROVED_STATUS_PATH,
                    ScopeInterface::SCOPE_STORE
                );

            case BaseHelper::REPROVED_STATUS:
                return $this->_scopeConfig->getValue(
                    self::REPROVED_STATUS_PATH,
                    ScopeInterface::SCOPE_STORE
                );
			
			case BaseHelper::REVIEW_STATUS:
                return $this->_scopeConfig->getValue(
                    self::REVIEW_STATUS_PATH,
                    ScopeInterface::SCOPE_STORE
                );
            default:
                return false;
        }
    }

    public function isEnabled()
    {
        return 1 == $this->_scopeConfig
                ->getValue(self::ENABLEDPATH, ScopeInterface::SCOPE_STORE);
    }

    public function getFilterParams($orderId)
    {
        if ($this->_params) {
            if ($orderId) {
                $this->_params['order_number'] = $orderId;
            }

            return $this->_params;
        }
		$status = $this->_scopeConfig->getValue(self::FILTER_STATUS_PATH,ScopeInterface::SCOPE_STORE);

        $this->_params = ['status' => $status,

            'payments' => $this->_scopeConfig
                ->getValue(
                    self::PAYMENTSPATH,
                    ScopeInterface::SCOPE_STORE
                ),
            'order_number' => $orderId ? $orderId : 0
        ];

        return $this->_params;
    }

    public function setOrderStatus($orderNumber, $status)
    {
        if ($this->isReproved($status) && $this->cancelIfReproved()) {
            $this->cancelOrder($orderNumber);
            return true;
        }

        $status = $this->getStatusCode($status);

        $order = $this->_orderInterface->get($orderNumber);
        $order->setStatus($status);
        $order->save();
    }

    protected function loadCustomer($customerId)
    {
        try {
            $this->_customerModel = $this->_customerRepository->getById($customerId);
        } catch (\Exception $e) {
            $this->_customerModel = null;
        }
    }

    protected function isReproved($status)
    {
        return $status == BaseHelper::REPROVED_STATUS;
    }

    protected function cancelOrder($orderNumber)
    {
        $order = $this->_orderInterface->get($orderNumber);
        $history = $order->addStatusHistoryComment(__('Payment denied by Clearsale'));
        $history->save();
        $order->cancel()->save();
    }

    protected function cancelIfReproved()
    {
        return $this->_scopeConfig->getValue(
            self::CANCEL_IF_REPROVED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function generateInvoiceAfterApproved()
    {
        return $this->_scopeConfig->getValue(
            self::GENERATE_INVOICE,
            ScopeInterface::SCOPE_STORE
        );
    }

}
