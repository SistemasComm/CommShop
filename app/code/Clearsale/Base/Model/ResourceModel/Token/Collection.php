<?php


namespace Clearsale\Base\Model\ResourceModel\Token;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Clearsale\Base\Model\Token',
            'Clearsale\Base\Model\ResourceModel\Token'
        );
    }
}
