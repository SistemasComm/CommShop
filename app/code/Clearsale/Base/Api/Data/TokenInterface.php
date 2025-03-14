<?php


namespace Clearsale\Base\Api\Data;

interface TokenInterface
{
    const PRODUCT_ID = 'product_id';
    const TOKEN = 'token';
    const EXPIRATION_DATE = 'expiration_date';
    const PRODUCT_DESCRIPTION = 'product_description';


    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Clearsale\Base\Api\Data\TokenInterface
     */
    public function setProductId($productId);

    /**
    * Get product_description
    * @return string|null
    */
    public function getProductDescription();

    /**
     * Set product_description
     * @param string $productDescription
     * @return \Clearsale\Base\Api\Data\TokenInterface
     */
    public function setProductDescription($productDescription);

    /**
     * Get token
     * @return string|null
     */
    public function getToken();

    /**
     * Set token
     * @param string $token
     * @return \Clearsale\Base\Api\Data\TokenInterface
     */
    public function setToken($token);

    /**
     * Get expiration_date
     * @return string|null
     */
    public function getExpirationDate();

    /**
     * Set expiration_date
     * @param string $expirationDate
     * @return \Clearsale\Base\Api\Data\TokenInterface
     */
    public function setExpirationDate($expirationDate);
}
