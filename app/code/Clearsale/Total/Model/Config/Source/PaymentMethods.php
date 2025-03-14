<?php

namespace Clearsale\Total\Model\Config\Source;

class PaymentMethods implements \Magento\Framework\Option\ArrayInterface
{
    const PAYMENTTYPE_CC_ATTRIBUTE          =   'clearsale_base/payment_mapping/credit_card_map';
    const PAYMENTTYPE_PAYPAL_ATTRIBUTE      =   'clearsale_base/payment_mapping/paypal_map';
    const PAYMENTTYPE_CD_ATTRIBUTE          =   'clearsale_base/payment_mapping/debit_card_map';
    const PAYMENTTYPE_BO_ATTRIBUTE          =   'clearsale_base/payment_mapping/ticket_map';
    const PAYMENTTYPE_TR_ATTRIBUTE          =   'clearsale_base/payment_mapping/bank_transfer_map';
    const PAYMENTTYPE_DP_ATTRIBUTE          =   'clearsale_base/payment_mapping/deposit_map';
    
    protected $_paymentHelper;
    protected $_scopeConfig;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_paymentHelper = $paymentHelper;
        $this->_scopeConfig = $scopeConfig;
    }

    public function toOptionArray()
    {
        $data = $this->_paymentHelper->getPaymentMethods();

        $result = array();

        $payments = $this->formatPaymentDefaults([
            self::PAYMENTTYPE_CC_ATTRIBUTE,
            self::PAYMENTTYPE_PAYPAL_ATTRIBUTE,
            self::PAYMENTTYPE_CD_ATTRIBUTE,
            self::PAYMENTTYPE_BO_ATTRIBUTE,
            self::PAYMENTTYPE_TR_ATTRIBUTE,
            self::PAYMENTTYPE_DP_ATTRIBUTE]
        );


        foreach ($payments as $payment){
            $label = "";
            if (array_key_exists($payment, $data)) {
                $label = $data[$payment]['title'];
            }

            $result [] = ['value' => $payment, 'label' => $label ];
        }

        return $result;
    }

    public function formatPaymentDefaults($defaults)
    {
        $return = [];

        if (is_array($defaults)) {
            foreach ($defaults as $default) {
                $result = $this->_scopeConfig->getValue(
                    $default
                );

                if (strpos((string)$result, ',') !== false) {
                    $result = explode(',', $result);
                }

                if (is_array($result)) {
                    foreach ($result as $key => $value) {
                        array_push($return, $value);
                    }
                    continue;
                }

                array_push($return, $result);
            }
        } else {
            $return[] = $this->_scopeConfig->getValue(
                $defaults
            );
        }

        return $return;
    }
}
