<?php


namespace Clearsale\Base\Api\Data;

interface OrderInterface
{
    const CODE = 'code';
    const REQUEST_ID = 'request_id';
    const STATUS = 'status';
    const SCORE = 'score';
    const UPDATE_DATE = 'update_date';


    /**
     * Get code
     * @return string|null
     */
    public function getCode();

    /**
     * Set code
     * @param string $code
     * @return \Clearsale\Base\Api\Data\OrderInterface
     */
    public function setCode($code);

    /**
    * Get request id
    * @return string|null
    */
    public function getRequestId();

    /**
     * Set request id
     * @param string $requestId
     * @return \Clearsale\Base\Api\Data\OrderInterface
     */
    public function setRequestId($requestId);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Clearsale\Base\Api\Data\OrderInterface
     */
    public function setStatus($status);

    /**
     * Get score
     * @return string|null
     */
    public function getScore();

    /**
     * Set score
     * @param string $score
     * @return \Clearsale\Base\Api\Data\OrderInterface
     */
    public function setScore($score);

    /**
     * Get update date
     * @return string|null
     */
    public function getUpdateDate();

    /**
     * Set update date
     * @param string $updateDate
     * @return \Clearsale\Base\Api\Data\OrderInterface
     */
    public function setUpdateDate($updateDate);
}
