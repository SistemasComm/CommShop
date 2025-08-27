<?php

namespace Clearsale\Base\Model\Products;

use Clearsale\Base\Model\Api\Request;

class Tickets extends AbstractProduct
{
    const TICKETID = 12;
    const DESCRIPTION = 'TICKETS';

    public function createOrder($postData)
    {
        try {
            $response = $this->send(\Clearsale\Base\Model\Api\Request::SEND_ORDER,
                                    \Clearsale\Base\Model\Api\Request::POST, $postData);
        } catch (\Exception $e) {
            $this->getLogger()->info($e->getMessage());
            $response = false;
        }
        return $response;
    }

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

    public function markChargeback($postData)
    {
        try {
            $response = $this->sendChargeback(Request::SEND_CHARGEBACK,
                Request::POST, $postData);
        } catch (\Exception $e) {
            $this->getLogger()->info($e->getMessage());
            $response = false;
        }
        return $response;
    }

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

    public function getProductId()
    {
        return self::TICKETID;
    }

    public function getProductDescription()
    {
        return self::DESCRIPTION;
    }
}
