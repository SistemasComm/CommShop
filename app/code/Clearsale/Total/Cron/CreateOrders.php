<?php


namespace Clearsale\Total\Cron;

class CreateOrders
{
    protected $_totalModel;

    public function __construct(\Clearsale\Total\Model\Total $total)
    {
        $this->_totalModel = $total;
    }

    public function execute()
    {
        $this->_totalModel->sendOrders();
    }
}
