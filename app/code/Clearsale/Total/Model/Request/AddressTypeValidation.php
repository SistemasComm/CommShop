<?php

namespace Clearsale\Total\Model\Request;

class AddressTypeValidation extends Validation
{
    const CUSTOMER_SECONDARY_DOCUMENT = 'clearsale_total/settings/customer_secondary_document';
    protected $_personTypeValue;

    /**
     * @return bool
     */
    public function isValidated()
    {
        if (!$this->validatePersonType()) {
            return false;
        }

        if (!$this->validateAddress()) {
            return false;
        }

        if (!$this->validatePhone()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function validatePersonType()
    {
        $this->_personTypeValue = '';

        if (!$this->hasCustomer()) {
            $this->_personTypeValue = $this->_inputParams['order']->getCustomerTaxvat() ? $this->_inputParams['order']->getCustomerTaxvat() : false;

            if (!$this->_personTypeValue) {
                $this->_personTypeValue = $this->_inputParams['address']->getVatid() ? $this->_inputParams['address']->getVatid() : false;
            }
        } else {
            $activeVatId = $this->_baseHelper->isEnabledVatId();
            $customerGroupByVat = $this->_baseHelper->getCustomerGroupVatId();

            $mappedValue = $this->_inputParams['customer']->getCustomAttribute($this->_baseHelper->getCpfCnpjAttribute());
            $mappedValue = $mappedValue ? $mappedValue->getValue() : false;
            $defaultValue = $this->_inputParams['customer']->getTaxvat();
            $taxvat = $mappedValue ? $mappedValue : $defaultValue;

            if ($activeVatId) {
                foreach ($customerGroupByVat as $groupId) {
                    if ($this->_inputParams['customer']->getGroupId() == $groupId) {
                        $addresses = $this->_inputParams['order']->getAddresses();
                        foreach ($addresses as $data) {
                            $vatId = $data->getVatId() ? $data->getVatId() : false;
                            if ($vatId) {
                                $vatId = preg_replace('/[^0-9]/', '', $vatId);
                                $taxvat = $vatId;
                                break;
                            }
                        }
                    }
                }
            }

            $this->_personTypeValue =  $taxvat ? $taxvat : false;
        }

        if (!$this->_personTypeValue) {
            $this->addError(__('Required Taxvat not found for order: %1', $this->_inputParams['order']->getIncrementId()));
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getPhones()
    {
        $phones[0] = $this->getPhone();
        $phones[1] = $this->getCellPhone();

        $return = [];
        foreach ($phones as $key => $value) {
            if ($value) {
                $p = [
                    'type' => $value['type'],
                    "ddi" => "55",
                    'ddd'  => (int)$value[0],
                    'number' => (int)$value[1]
                ];

                array_push($return, $p);
            }
        }

        return $return;
    }

    /**
     * @return bool
     */
    public function validatePhone()
    {
        if ($this->getPhone() || $this->getCellPhone()) {
            return true;
        } else {
            $this->addError(__('Telephone not found or not validated for order: %1', $this->_inputParams['order']->getIncrementId()));
			
            return false;
        }
    }

    /**
     * @return false|string[]
     */
    public function getPhone()
    {
        $phone = $this->_baseHelper->formatPhone($this->_inputParams['address']->getTelephone());

        if ($phone) {
            $phone['type'] = $this->_baseHelper->getPhoneType('Residencial');
            return $phone;
        }

        return false;
    }

    /**
     * @return false|string[]
     */
    public function getCellPhone()
    {
        $phone = $this->_baseHelper->formatPhone($this->_inputParams['address']->getFax());

        if ($phone) {
            $phone['type'] = $this->_baseHelper->getPhoneType('Celular');
            return $phone;
        }

        return false;
    }

    /**
     * @return false|int|string
     */
    public function getType()
    {
        return $this->_baseHelper->getBillingType($this->_personTypeValue);
    }

    /**
     * @return string|string[]|null
     */
    public function getPrimaryDocument()
    {
        return $this->_baseHelper->getPrimaryDocument($this->_personTypeValue);
    }

    /**
     * @return false|mixed
     */
    public function getSecondaryDocument()
    {
        $secondaryDocument = false;

        $customer = $this->_customer;

        if ($this->hasCustomer()) {
            $secondaryDocument = $this->_baseHelper->getParamByCustomerAttr(self::CUSTOMER_SECONDARY_DOCUMENT, $customer);
        }

        if (!$secondaryDocument) {
            return false;
        }
        return $secondaryDocument;
    }

    /**
     * @return false
     */
    public function getShippingPrice()
    {
        $shippingPrice = false;

        if ($this->hasCustomer()) {
            $shippingPrice = $this->_inputParams['order']->getShippingAmount();
        }

        if (!$shippingPrice) {
            return false;
        }

        return $shippingPrice;
    }

    /**
     * @param $param
     * @return mixed
     */
    public function getFieldNameValue($param)
    {
        return $this->_scopeConfig->getValue($param, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return false
     */
    public function getClientId()
    {
        $customerId = false;

        if ($this->hasCustomer()) {
            $customerId = $this->_customer->getId();
        }

        if (!$customerId) {
            return false;
        }
        return $customerId;
    }

    /**
     * @return false|int|string
     */
    public function getGender()
    {
        $customerGender = false;

        if ($this->hasCustomer()) {
            $customerGender = $this->_inputParams['customer']->getGender();
        }

        $result = $customerGender ? $customerGender : false;

        if ($result) {
            return $this->_baseHelper->getGender($result);
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        $customerDob = false;

        if ($this->hasCustomer()) {
            $customerDob = $this->_inputParams['customer']->getDob();
        }

        $orderDob = $this->_inputParams['order']->getCustomerDob();

        $target = $customerDob ? $customerDob : $orderDob;

        return $target;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->_inputParams['address']->getFirstName();
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->_inputParams['address']->getEmail();
    }

    /**
     * @return bool
     */
    public function validateAddress()
    {
        $validateAddress = $this->_addressValidation->validateAddress($this->_inputParams['address'],$this->_baseHelper);

        if (!$validateAddress['flag']) {
            $msg = $validateAddress['msg'];
            //$this->addError(__('Check addresses field for order: %1', $this->_inputParams['order']->getIncrementId()));
            $this->addError(__('CLEARSALE ERROR: Check %1 field(s) address!', $msg));
            return false;
        } else {
            return true;
        }

        
    }

    /**
     * @return array
     */
    public function getAddress()
    {
        return $this->_addressValidation->getAddress();
    }
}
