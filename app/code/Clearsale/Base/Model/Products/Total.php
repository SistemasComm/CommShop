<?php

namespace Clearsale\Base\Model\Products;

use Clearsale\Base\Model\Api\Request;

/**
 * Class Total
 * @package Clearsale\Base\Model\Products
 */
class Total extends AbstractProduct
{
    const TOTALID = 3;
    const DESCRIPTION = 'TOTAL';

    /**
     * @param $postData
     * @return bool
     */
    public function createOrder($postData)
    {
        try {
            $response = $this->send(
                Request::SEND_ORDER,
                Request::POST,
                $postData
            );
        } catch (\Exception $e) {
            $this->getLogger()->info($e->getMessage());
            $response = false;
        }
        return $response;
    }

    /**
     * @param $postData
     * @return bool
     */
    public function markChargeback($postData)
    {
        try {
            $response = $this->sendChargeback(
                Request::SEND_CHARGEBACK,
                Request::POST,
                $postData
            );
        } catch (\Exception $e) {
            $this->getLogger()->info($e->getMessage());
            $response = false;
        }
        return $response;
    }

    /**
     * @param $orderNumber
     * @return bool
     */
    public function updateOrder($orderNumber)
    {
        try {
            $apiMethod = sprintf(\Clearsale\Base\Model\Api\Request::GET_ORDER, $orderNumber);

            $response = $this->send($apiMethod, \Clearsale\Base\Model\Api\Request::GET);
        } catch (\Exception $e) {
            $this->getLogger()->info($e->getMessage());
            $response = false;
        }

        return $response;
    }

    /**
     * @param $postData
     * @param false $isUpdate
     * @return bool
     */
    public function updateStatus($postData, $isUpdate = false)
    {
        try {
            $apiMethod = sprintf(Request::GET_ORDER, $postData['order']);
            $response = $this->sendUpdateStatus(
                $apiMethod,
                $isUpdate ? Request::PUT : Request::GET,
                $postData
            );
        } catch (\Exception $e) {
            $this->getLogger()->info($e->getMessage());
            $response = false;
        }

        return $response;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return self::TOTALID;
    }

    /**
     * @return string
     */
    public function getProductDescription()
    {
        return self::DESCRIPTION;
    }
}
