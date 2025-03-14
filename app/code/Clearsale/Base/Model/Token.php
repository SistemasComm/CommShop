<?php


namespace Clearsale\Base\Model;

use Clearsale\Base\Api\Data\TokenInterface;

class Token extends \Magento\Framework\Model\AbstractModel implements TokenInterface
{
    protected function _construct()
    {
        $this->_init('Clearsale\Base\Model\ResourceModel\Token');
    }

    /**
     * Get token
     * @return string
     */
    public function getToken()
    {
        return $this->getData(self::TOKEN);
    }

    /**
     * Get expiration_date
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->getData(self::EXPIRATION_DATE);
    }

    /**
     * Get product_id
     * @return string
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Get product_description
     * @return string
     */
    public function getProductDescription()
    {
        return $this->getData(self::PRODUCT_DESCRIPTION);
    }

    /**
     * Set token
     * @param string $token
     * @return \Clearsale\Base\Api\Data\TokenInterface
     */
    public function setToken($token)
    {
        return $this->setData(self::TOKEN, $token);
    }

    /**
     * Set expirtion_date
     * @param string $expirationDate
     * @return \Clearsale\Base\Api\Data\TokenInterface
     */
    public function setExpirationDate($expirationDate)
    {
        return $this->setData(self::EXPIRATION_DATE, $expirationDate);
    }

    /**
     * Set product_id
     * @param string $productId
     * @return \Clearsale\Base\Api\Data\TokenInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Set product_description
     * @param string $productDescription
     * @return \Clearsale\Base\Api\Data\TokenInterface
     */
    public function setProductDescription($productDescription)
    {
        return $this->setData(self::PRODUCT_DESCRIPTION, $productDescription);
    }
}
