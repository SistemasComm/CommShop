<?php


namespace Clearsale\Base\Model;

use Clearsale\Base\Api\Data\OrderInterface;

class Order extends \Magento\Framework\Model\AbstractModel implements OrderInterface
{
    protected function _construct()
    {
        $this->_init('Clearsale\Base\Model\ResourceModel\Order');
    }

    /**
    * Get code
    * @return string
    */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Get status
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Get request_id
     * @return string
     */
    public function getRequestId()
    {
        return $this->getData(self::REQUEST_ID);
    }

    /**
     * Get score
     * @return string
     */
    public function getScore()
    {
        return $this->getData(self::SCORE);
    }

    /**
     * Get update_date
     * @return string
     */
    public function getUpdateDate()
    {
        return $this->getData(self::UPDATE_DATE);
    }

    /**
     * Set code
     * @param string $code
     * @return \Clearsale\Base\Api\Data\OrderInterface
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Set status
     * @param string $status
     * @return \Clearsale\Base\Api\Data\OrderInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set request_id
     * @param string $requestId
     * @return \Clearsale\Base\Api\Data\OrderInterface
     */
    public function setRequestId($requestId)
    {
        return $this->setData(self::REQUEST_ID, $requestId);
    }

    /**
     * Set score
     * @param string $score
     * @return \Clearsale\Base\Api\Data\OrderInterface
     */
    public function setScore($score)
    {
        return $this->setData(self::SCORE, $score);
    }

    /**
     * Set update_date
     * @param string $updateDate
     * @return \Clearsale\Base\Api\Data\OrderInterface
     */
    public function setUpdateDate($updateDate)
    {
        return $this->setData(self::UPDATE_DATE, $updateDate);
    }
}
