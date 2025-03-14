<?php

namespace Clearsale\Total\Model\Request;

class AddressValidation
{
    protected $_street;
    protected $_number;
    protected $_county;
    protected $_additionalInformation;
    protected $_city;
    protected $_state;
    protected $_zipcode;

    protected $_baseHelper;
    protected $_address;

    /**
     * @param $address
     * @param $helper
     * @return bool
     */
    public function validateAddress($address, $helper)
    {
        $this->_baseHelper = $helper;
        $this->_address = $address;
        $msg = "";

        if (!$this->getStreet()){
            $msg = $msg . ' RUA (Street)';
        }
        if (!$this->getNumber()){
            $msg = $msg . ' NÚMERO (Number)';
        }
        if (!$this->getCounty()){
            $msg = $msg . ' BAIRRO (County)';
        }
        if (!$this->getCity()){
            $msg = $msg . ' CIDADE (City)';
        }
        if (!$this->getState()){
            $msg = $msg . ' ESTADO (State)';
        }
        if (!$this->getZipcode()) {
            $msg = $msg . ' CEP (Zipcode)';
        }

        if (!empty($msg)) {
            return ["flag" => false,"msg" => $msg];
        }

        return ["flag" => true];
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        $this->_street = false;

        $this->_street = $this->_baseHelper->getStreet($this->_address->getStreet());

        return $this->_street;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        $this->_number = 'N/A';

		if($this->_baseHelper->getNumber($this->_address->getStreet()) != "0")
			$this->_number = $this->_baseHelper->getNumber($this->_address->getStreet());
		
        return $this->_number;
    }

    /**
     * @return mixed
     */
    public function getCounty()
    {
        $this->_county = false;

        $this->_county = $this->_baseHelper->getNeighborhood($this->_address->getStreet());

        return $this->_county;
    }

    /**
     * @return string
     */
    public function getAdditionalInformation()
    {
        $this->_additionalInformation = 'N/A';

        if ($this->_baseHelper->getAdditionaInformation($this->_address->getStreet())) {
            $this->_additionalInformation = $this->_baseHelper->getAdditionaInformation($this->_address->getStreet());
        }

        return $this->_additionalInformation;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        $this->_city = false;

        $this->_city = $this->_address->getCity();

        return $this->_city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        $this->_state = false;

        $this->_state = $this->_baseHelper->getStateCode($this->_address->getRegionId());

        return $this->_state;
    }

    /**
     * @return mixed
     */
    public function getZipcode()
    {
        $this->_zipcode = false;

        $this->_zipcode = $this->_address->getPostcode();

        return $this->_zipcode;
    }

    /**
     * @return array
     */
    public function getAddress()
    {
        return [
            'street' => $this->_street,
            'number' => $this->_number,
            'additional_information' => $this->getAdditionalInformation(),
            'county' => $this->_county,
            'city' => $this->_city,
            'state' => $this->_state,
            'country' => 'BR',
            'zipcode' => $this->_zipcode
        ];
    }
}
