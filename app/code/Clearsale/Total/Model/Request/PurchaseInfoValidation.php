<?php

namespace Clearsale\Total\Model\Request;

class PurchaseInfoValidation extends Validation
{
    protected $_purchaseInfoData = [];

    /**
     * @return bool
     */
    public function isValidated()
    {
        if (!$this->validateProductIdentification()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function validateProductIdentification()
    {
        $purchaseInfoData = [
            'purchaseLogged' => (bool) $this->isGuest($this->_inputParams['order']),
        ];

        if ($this->hasCustomer()) {
            $purchaseInfoData += [
                'lastDateChangePassword' => $this->_customer['updated_at'] ? $this->_customer['updated_at'] : '2021-01-01 00:00:00',
                'lastDateInsertedAddress' => $this->_customer['updated_at'] ? $this->_customer['updated_at'] : '2021-01-01 00:00:00',
                'email' => (string) $this->_customer->getEmail(),
                'login' => (string) $this->_customer->getEmail()
            ];
        } else {
            $purchaseInfoData += [
                'email' => (string) $this->_inputParams['order']['customer_email']
            ];
        }

        $this->_purchaseInfoData = $purchaseInfoData;

        return true;
    }

    /**
     * @return array
     */
    public function getPurchaseInfoData()
    {
        return $this->_purchaseInfoData;
    }

    /**
     * @param $order
     * @return bool
     */
    protected function isGuest($order)
    {
        if ($order['customer_is_guest'] === '0') {
            return true;
        }
        return false;
    }
}
