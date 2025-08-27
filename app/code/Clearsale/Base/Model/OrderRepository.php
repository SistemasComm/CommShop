<?php


namespace Clearsale\Base\Model;

use Clearsale\Base\Api\OrderRepositoryInterface;
use Clearsale\Base\Api\Data\OrderInterface;
use Clearsale\Base\Model\ResourceModel\Order as OrderResource;
use Clearsale\Base\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class OrderRepository implements OrderRepositoryInterface
{
    protected $_resource;

    protected $_collection;

    protected $_orderFactory;

    public function __construct(
        OrderResource $resource,
                                    OrderCollectionFactory $orderCollectionFactory,
                                    \Clearsale\Base\Model\OrderFactory $orderFactory
    ) {
        $this->_resource = $resource;
        $this->_collection = $orderCollectionFactory;
        $this->_orderFactory = $orderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(OrderInterface $order)
    {
        try {
            $this->_resource->save($order);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the order: %1',
                $exception->getMessage()
            ));
        }

        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($order_number)
    {
        $order = $this->_orderFactory->create();
        $order->load($order_number, 'code');

        if (!$order->getId()) {
            return false;
        }

        return $order;
    }

    /**
    * {@inheritdoc}
    */
    public function delete(OrderInterface $order)
    {
        try {
            $this->_resource->delete($order);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the order: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($order_number)
    {
        return $this->delete($this->getById($order_number));
    }
}
