<?php

namespace Clearsale\Base\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const STREET_ATTRIBUTE                  =   'clearsale_base/address_mapping/address_street_map';
    const NUMBER_ATTRIBUTE                  =   'clearsale_base/address_mapping/address_number_map';
    const ADDITIONALINFORMATION_ATTRIBUTE   =   'clearsale_base/address_mapping/address_complement_map';
    const NEIGHBORHOOD_ATTRIBUTE            =   'clearsale_base/address_mapping/address_neighborhood_map';

    const CPF_CNPJ_ATTRIBUTE                 =  'clearsale_base/settings/clearsale_cpf_cnpj';
    const CUSTOMER_GROUP_VAT_ID              =  'clearsale_base/settings/clearsale_customer_group';
    const ACTIVE_VAT_ID_CUSTOMER_GROUP       =  'clearsale_base/settings/clearsale_active_vatid';

    const PAYMENTTYPE_CC_ATTRIBUTE          =   'clearsale_base/payment_mapping/credit_card_map';
    const PAYMENTTYPE_PAYPAL_ATTRIBUTE      =   'clearsale_base/payment_mapping/paypal_map';
    const PAYMENTTYPE_CD_ATTRIBUTE          =   'clearsale_base/payment_mapping/debit_card_map';
    const PAYMENTTYPE_BO_ATTRIBUTE          =   'clearsale_base/payment_mapping/ticket_map';
    const PAYMENTTYPE_TR_ATTRIBUTE          =   'clearsale_base/payment_mapping/bank_transfer_map';
    const PAYMENTTYPE_DP_ATTRIBUTE          =   'clearsale_base/payment_mapping/deposit_map';

    const PHONETYPE_ENUM                    = [ '0'     =>  'NI',
                                                '1'     =>  'Residencial',
                                                '2'     =>  'Comercial',
                                                '6'     =>  'Celular'];

    const BILLINGTYPE_ENUM                  = [ '1'     =>  'Pessoa Fisica',
                                                '2'     =>  'Pessoa Juridica'];

    const GENDER_ENUM                       = [ 'M'     =>  'Masculino',
                                                'F'     =>  'Feminino'];

    const CARDTYPE_ENUM                     = [ '1'     =>  'Diners',
                                                '2'     =>  'Mastercard',
                                                '3'     =>  'Visa',
                                                '4'     =>  'Outros',
                                                '5'     =>  'American Express',
                                                '6'     =>  'Hipercard',
                                                '7'     =>  'Aura',
                                                '10'    =>  'Cartão Elo',
                                                '50'    =>  'LeaderCard',
                                                '100'   =>  'Fortbrasil',
                                                '101'   =>  'Sorocred',
                                                '102'   =>  'A Vista',
                                                '103'   =>  'Cartão Mais',
                                                '105'   =>  'Cartão C&A'
    ];

    const CARDTYPE_SHORT_ENUM = [
        '1' => 'DN',
        '2' => 'MC',
        '3' => 'VI',
        '5' => 'AE',
        '6' => 'HI',
        '7' => 'AU',
        '10' => 'EL'
    ];

    const PRODUCTANALYZESTATUS_ENUM         = [ 'AMA'   =>  'Análise Manual',
                                                'NVO'   =>  'Novo'];

    const PRODUCTAPPROVEDSTATUS_ENUM        = [ 'APA'   =>  'Aprovação Automática',
                                                'APP'   =>  'Aprovação Por Política'];

    const MANUALAPPROVEDSTATUS_ENUM         = [ 'APM'   =>  'Aprovação Manual'];

    const PRODUCTREPROVEDSTATUS_ENUM        = [ 'SUS'   =>  'Suspensão Manual',
                                                'RPM'   =>  'Reprovação Sem Suspeita',
                                                'RPA'   =>  'Reprovação Automática',
                                                'RPP'   =>  'Reprovação Por Política',
                                                'CAN'   =>  'Cancelado Pelo Cliente',
                                                'FRD'   =>  'Fraude Confirmada'];

    const PRODUCTLIST_ENUM                  = [	3       =>  'TOTAL',
                                                10      =>  'Real Time Decision',
                                                12      =>  'Tickets'];

    const PAYMENTSTATUS_ENUM                = [ 'PGA'   =>  'Pedido Aprovado',
                                                'PGR'   =>  'Pedido Reprovado'];

    const PAYMENTTYPE_ENUM                  = [ '1'     =>  'Cartao Credito',
                                                '2'     =>  'Boleto',
                                                '14'    =>  'Outros',
                                                '4011'  =>  'Cartao Debito',
                                                '4011'  =>  'Transferencia'];

    const ANALYZE_STATUS                    =   'ANALYZE';
    const APPROVED_STATUS                   =   'APPROVED';
    const MANUAL_APPROVED_STATUS            =   'MANUAL_APPROVED';
    const REPROVED_STATUS                   =   'REPROVED';
    const REVIEW_STATUS	                    =   'REVIEW';

    protected $_scopeConfig;
    protected $_region;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Directory\Model\Region $region
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Directory\Model\Region $region
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_timezone  	= $timezone;
        $this->_region = $region;
    }

    /**
     * @param $param
     * @return false|int|string
     */
    public function getBillingType($param)
    {
        $param = preg_replace('/[^0-9]/', '', $param);

        if (strlen($param) == 14) {
            return array_search('Pessoa Juridica', self::BILLINGTYPE_ENUM);
        }

        return array_search('Pessoa Fisica', self::BILLINGTYPE_ENUM);
    }

    /**
     * @return mixed
     */
    public function getCpfCnpjAttribute()
    {
        return $this->_scopeConfig->getValue(
            self::CPF_CNPJ_ATTRIBUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function isEnabledVatId()
    {
        return (bool)$this->_scopeConfig->getValue(
            self::ACTIVE_VAT_ID_CUSTOMER_GROUP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * @return array|false|mixed|string[]
     */
    public function getCustomerGroupVatId()
    {
        $result = $this->_scopeConfig->getValue(
            self::CUSTOMER_GROUP_VAT_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($result){
            if (strpos($result, ',') !== false) {
                $result = explode(',', $result);
            } else {
                return $result = [$result];
            }

            return $result;
        }
        
        return "";
    }

    /**
     * @param $gender
     * @return false|int|string
     */
    public function getGender($gender)
    {
        switch ($gender) {
            case 1:
                return array_search('Masculino', self::GENDER_ENUM);

            case 2:
                return array_search('Feminino', self::GENDER_ENUM);
            default:
                return 'M';
        }
    }

    /**
     * @param $param
     * @return string|string[]|null
     */
    public function getPrimaryDocument($param)
    {
        $param = preg_replace('/[^0-9]/', '', $param);
        return $param;
    }

    /**
     * @param $param
     * @param $customer
     * @return mixed
     */
    public function getParamByCustomerAttr($param, $customer)
    {
        $field = $this->getFieldNameValue($param);
        return $customer->getData($field);
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
     * @param $param
     * @return string
     */
    public function getDocumentType($param)
    {
        $type = $this->getBillingType($param);

        if ($type == 1) {
            return 'CPF';
        }
        return 'CNPJ';
    }

    /**
     * @param $type
     * @return false|int|string
     */
    public function getPhoneType($type)
    {
        switch ($type) {
            case 'Residencial':
                return array_search('Residencial', self::PHONETYPE_ENUM);

            case 'Comercial':
                return array_search('Comercial', self::PHONETYPE_ENUM);

            case 'Celular':
                return array_search('Celular', self::PHONETYPE_ENUM);

            default:
                return array_search('NI', self::PHONETYPE_ENUM);
        }
    }

    /**
     * @param $phone
     * @return false|string[]
     */
    public function formatPhone($phone)
    {
        if ($phone && strlen($phone) >= 10) {
            $phone = preg_replace('/[^0-9]/', '', $phone);

            $new = substr_replace($phone, '-', 2, 0);
            $formated = explode('-', $new);

            return $formated;
        }

        return false;
    }

    /**
     * @param $streetLines
     * @return null
     */
    public function getStreet($streetLines)
    {
        $line = $this->_scopeConfig->getValue(
            self::STREET_ATTRIBUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (isset($streetLines[$line])) {
            return $streetLines[$line];
        }

        return null;
    }

    /**
     * @param $regionId
     * @return string
     */
    public function getStateCode($regionId)
    {
        $region = $this->_region->load($regionId);

        return $region->getCode();
    }

    /**
     * @param $streetLines
     * @return false
     */
    public function getNumber($streetLines)
    {
        $line = $this->_scopeConfig->getValue(
            self::NUMBER_ATTRIBUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (isset($streetLines[$line])) {
            return $streetLines[$line];
        }

        return false;
    }

    /**
     * @param $streetLines
     * @return false
     */
    public function getAdditionaInformation($streetLines)
    {
        $line = $this->_scopeConfig->getValue(
            self::ADDITIONALINFORMATION_ATTRIBUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (isset($streetLines[$line])) {
            return $streetLines[$line];
        }

        return false;
    }

    /**
     * @param $streetLines
     * @return false
     */
    public function getNeighborhood($streetLines)
    {
        $line = $this->_scopeConfig->getValue(
            self::NEIGHBORHOOD_ATTRIBUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (isset($streetLines[$line])) {
            return $streetLines[$line];
        }

        return false;
    }

    /**
     * @param $type
     * @return false|int|string
     */
    public function getPaymentType($type)
    {
        $defaults = $this->initPaymentMapping();

        switch ($type) {
            case in_array($type, $defaults['Cartao Credito']):
                return $this->searchPaymentType('Cartao Credito');

            case in_array($type, $defaults['Boleto']):
                return $this->searchPaymentType('Boleto');

            case in_array($type, $defaults['Cartao Debito']):
                return $this->searchPaymentType('Cartao Debito');

            case in_array($type, $defaults['Transferencia']):
                return $this->searchPaymentType('Transferencia');

            default:
                return $this->searchPaymentType('Outros');
        }
    }

    /**
     * @param $search
     * @return false|int|string
     */
    protected function searchPaymentType($search)
    {
        return array_search($search, self::PAYMENTTYPE_ENUM);
    }

    /**
     * @return array
     */
    protected function initPaymentMapping()
    {
        return [	'Cartao Credito' 	=> $this->formatPaymentDefaults([  self::PAYMENTTYPE_CC_ATTRIBUTE,
                                                                                    self::PAYMENTTYPE_PAYPAL_ATTRIBUTE]),

                        'Boleto'			=> $this->formatPaymentDefaults(self::PAYMENTTYPE_BO_ATTRIBUTE),

                        'Cartao Debito'		=> $this->formatPaymentDefaults(self::PAYMENTTYPE_CD_ATTRIBUTE),

                        'Transferencia'		=> $this->formatPaymentDefaults(self::PAYMENTTYPE_TR_ATTRIBUTE)
                    ];
    }

    /**
     * @param $defaults
     * @return array
     */
    protected function formatPaymentDefaults($defaults)
    {
        $return = [];

        if (is_array($defaults)) {
            foreach ($defaults as $default) {
                $result = $this->_scopeConfig->getValue(
                    $default,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
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
                $defaults,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return $return;
    }

    /**
     * @param $status
     * @return false|string
     */
    public function getOrderStatus($status)
    {
        switch ($status) {
            case array_key_exists($status, self::PRODUCTANALYZESTATUS_ENUM):
                return self::ANALYZE_STATUS;

            case array_key_exists($status, self::PRODUCTAPPROVEDSTATUS_ENUM):
                return self::APPROVED_STATUS;

            case array_key_exists($status, self::MANUALAPPROVEDSTATUS_ENUM):
                return self::MANUAL_APPROVED_STATUS;

            case array_key_exists($status, self::PRODUCTREPROVEDSTATUS_ENUM):
                return self::REPROVED_STATUS;

            default:
                return false;
        }
    }

    /**
     * @return string|string[]
     * @throws \Exception
     */
    public function getNowDateTime()
    {
        $currentDateTimeUTC = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $localizedDateTimeISO = $this->_timezone->date(
            new \DateTime($currentDateTimeUTC)
        )->format(
            \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT
        );

        $now = str_replace(' ', 'T', $localizedDateTimeISO);
        return $now;
    }

    /**
     * @param $created
     * @return \DateTime
     * @throws \Exception
     */
    public function getOrderDateTime($created)
    {
        return $this->_timezone->date(new \DateTime($created));
    }

    /**
     * @param $orderTimeZone
     * @return string
     * @throws \Exception
     */
    public function orderTimeZone($orderTimeZone)
    {
        $orderTimeZone = $this->getOrderDateTime($orderTimeZone);
        return $orderTimeZone->format('Y-m-d H:i:s');
    }
}
