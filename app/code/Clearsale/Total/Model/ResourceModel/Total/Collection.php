<?php


namespace Clearsale\Total\Model\ResourceModel\Total;

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
            'Clearsale\Total\Model\Total',
            'Magento\Sales\Model\ResourceModel\Order'
        );
    }

    public function filterOrder($params)
    {
        $this->sales_order_table = "main_table";
        $this->sales_order_payment_table = $this->getTable("sales_order_payment");
        $this->addFieldToFilter('main_table.status', ['in' => $params['status']]);

        $orderNumber = $params['order_number'];
        if ($orderNumber) {
            $this->addFieldToFilter('main_table.increment_id', ['eq' => $orderNumber]);
        }

        $this->getSelect()
            ->join(
                array('payment' =>$this->sales_order_payment_table),
                $this->sales_order_table . '.entity_id= payment.parent_id',
                array('payment_method' => 'payment.method',
                    'order_id' => $this->sales_order_table.'.entity_id'
                )
            );

        $this->addFieldToFilter('method', ['in' => $params['payments']]);
    }
}
