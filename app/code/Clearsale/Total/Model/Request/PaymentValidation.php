<?php

namespace Clearsale\Total\Model\Request;

class PaymentValidation extends Validation
{
    protected $_paymentData = [];

    /**
     * @return bool
     */
    public function isValidated()
    {
        return $this->isValidMethod();
    }

    /**
     * @return bool
     */
    protected function isValidMethod()
    {
        $this->clearPayments();
        $method = $this->_baseHelper->getPaymentType($this->_inputParams['payment']);
        $order = $this->_inputParams['order'];
        $payment = $order->getPayment();

        if (!$method) {
            return false;
        }

        $return = [
            'type' => (int) $method,
            'value' => (float) $order->getGrandTotal()
        ];

        if ($method == 1) {
            $payment = $this->_inputParams['order']->getPayment();
            $additionalInformation = $this->_inputParams['order']->getPayment()->getAdditionalInformation();

            $return['card']['type'] = 4;
            if (!empty($payment->getCcType())) {
                if ($index = array_search($payment->getCcType(), \Clearsale\Base\Helper\Data::CARDTYPE_ENUM)) {
                    $return['card']['type'] = (int) $index;
                }

                if ($index = array_search($payment->getCcType(), \Clearsale\Base\Helper\Data::CARDTYPE_SHORT_ENUM)) {
                    $return['card']['type'] = (int) $index;
                }
            }

            if (!empty($payment->getCcOwner())) {
                $return['card']['ownerName'] = $payment->getCcOwner();
            }

            if (!empty($payment->getCcExpMonth()) && !empty($payment->getCcExpYear())) {
                $return['card']['validityDate'] = $payment->getCcExpMonth() . '/' . $payment->getCcExpYear();
            }

            if (!empty($additionalInformation['cc_installments'])) {
                $return['installments'] = (int) $additionalInformation['cc_installments'];
            }

            if (!empty($payment->getCcInstallments())) {
                $return['installments'] = (int) $payment->getCcInstallments();
            }

            if (empty($additionalInformation['cc_first6'])) {
                $return['card']['bin'] = '000000';
            }

            if (!empty($payment->getCcNumber())) {
                $return['card']['bin'] = substr($payment->getCcNumber(), 0, 6);
            }

            if (!empty($additionalInformation['cc_first6'])) {
                $return['card']['bin'] = $additionalInformation['cc_first6'];
            }

            if (!empty($payment->getCcLast4())) {
                $return['card']['end'] = (string) $payment->getCcLast4();
            }

            if (!isset($return['card']['end']) && !empty($payment->getCcNumber())) {
                $return['card']['end'] = (string) substr($payment->getCcNumber(), -4);
            }

            if (!empty($additionalInformation['cc_last4'])) {
                $return['card']['end'] = $additionalInformation['cc_last4'];
            }

            if (!empty($payment->getNsu())) {
                $return['card']['nsu'] = (string) $payment->getNsu();
            } else {
                $return['card']['nsu'] = "";
            }

            if (!empty($additionalInformation['nsu'])) {
                $return['card']['nsu'] = (string) $additionalInformation['nsu'];
            } else {
                $return['card']['nsu'] = "";
            }

            if (!empty($additionalInformation['transaction_identifier'])) {
                $return['card']['nsu'] = (string) $additionalInformation['transaction_identifier'];
            } else {
                $return['card']['nsu'] = "";
            }
        }

        $this->addPaymentData($return);

        return true;
    }

    /**
     *
     */
    protected function clearPayments()
    {
        $this->_paymentData = [];
    }

    /**
     * @return array
     */
    public function getPaymentData()
    {
        return $this->_paymentData;
    }

    /**
     * @param $method
     */
    protected function addPaymentData($method)
    {
        array_push($this->_paymentData, $method);
    }
}
