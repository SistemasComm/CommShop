<?php


namespace Clearsale\Base\Api;

interface TokenRepositoryInterface
{


    /**
     * Save token
     * @param \Clearsale\Base\Api\Data\TokenInterface $token
     * @return \Clearsale\Base\Api\Data\TokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Clearsale\Base\Api\Data\TokenInterface $token);

    /**
     * Retrieve token
     * @param string $productId
     * @return \Clearsale\Base\Api\Data\TokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($productId);

    /**
     * Delete token
     * @param \Clearsale\Base\Api\Data\TokenInterface $token
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Clearsale\Base\Api\Data\TokenInterface $token);

    /**
     * Delete token by ID
     * @param string $productId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($productId);
}
