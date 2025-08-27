<?php

namespace Clearsale\Base\Model\Products;


class Profiler extends AbstractProduct
{
    const PROFILERID = 15; //TODO DESCOBRIR QUE QUE É ISSO
    const DESCRIPTION = 'PROFILER';

    public function createCustomer($postData, $isUpdate = false)
    {
        try {
            $response = $this->send(
                \Clearsale\Base\Model\Api\Request::PROFILER,
                $isUpdate ? \Clearsale\Base\Model\Api\Request::PUT : \Clearsale\Base\Model\Api\Request::POST,
                $postData,
                true);
        } catch (\Exception $e) {
            //$this->getLogger()->error($e->getMessage());
            $this->getLogger()->info($e->getMessage());
            $response = false;
        }
        return $response;
    }

    public function customerLogin($postData)
    {
        try {
            $response = $this->send(
                \Clearsale\Base\Model\Api\Request::PROFILER_LOGIN,
                \Clearsale\Base\Model\Api\Request::POST,
                $postData,
                true);

        } catch (\Exception $e) {
            //$this->getLogger()->error($e->getMessage());
            $this->getLogger()->info($e->getMessage());
            $response = false;
        }

        return $response;
    }

    public function customerLogout($postData)
    {
        try {
            $response = $this->send(
                \Clearsale\Base\Model\Api\Request::PROFILER_LOGOUT,
                \Clearsale\Base\Model\Api\Request::POST,
                $postData,
                true);

        } catch (\Exception $e) {
            //$this->getLogger()->error($e->getMessage());
            $this->getLogger()->info($e->getMessage());
            $response = false;
        }

        return $response;
    }

    public function customerResetPassword($postData)
    {
        try {
            $response = $this->send(
                \Clearsale\Base\Model\Api\Request::PROFILER_RESET_PASSWORD,
                \Clearsale\Base\Model\Api\Request::POST,
                $postData,
                true);

        } catch (\Exception $e) {
            //$this->getLogger()->error($e->getMessage());
            $this->getLogger()->info($e->getMessage());
            $response = false;
        }

        return $response;
    }

    public function getProductId()
    {
        return self::PROFILERID;
    }

    public function getProductDescription()
    {
        return self::DESCRIPTION;
    }
}