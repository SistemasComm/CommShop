<?php


namespace Clearsale\Base\Api;

interface OrderRepositoryInterface
{


    /**
     * Save order
     * @param \Clearsale\Base\Api\Data\OrderInterface $order
     * @return \Clearsale\Base\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Clearsale\Base\Api\Data\OrderInterface $order);

    /**
     * Retrieve order
     * @param string $order_number
     * @return \Clearsale\Base\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($order_number);

    /**
     * Delete order
     * @param \Clearsale\Base\Api\Data\OrderInterface $order
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Clearsale\Base\Api\Data\OrderInterface $order);

    /**
     * Delete order by Order Number
     * @param string $order_number
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($order_number);
}
