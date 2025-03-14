<?php

namespace Clearsale\Total\Model;

use Magento\Framework\DB\Transaction;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Clearsale\Base\Helper\Data as BaseHelper;
use Clearsale\Base\Model\Products\Total as TotalBase;
use Clearsale\Total\Helper\Data;
use Clearsale\Total\Model\RequestFactory;
use Clearsale\Total\Model\ResourceModel\Total\CollectionFactory;
use Psr\Log\LoggerInterface;

class Total extends AbstractModel
{
    protected $_orderCollectionFactory;
    protected $_baseTotal;
    protected $_collectionFactory;
    protected $_requestFactory;
    protected $_logger;
    protected $_helper;
    protected $invoiceService;
    protected $order;
    protected $orderFactory;
    protected $transaction;
    protected $invoiceSender;
    protected $isSuccessful = false;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        TotalBase $baseTotal,
        RequestFactory $requestFactory,
		\Clearsale\Base\Logger\Logger $logger,
        Data $helper,
        CollectionFactory $collectionFactory,
        InvoiceService $invoiceService,
        Order $order,
        OrderFactory $orderFactory,
        Transaction $transaction,
        InvoiceSender $invoiceSender
    ) {

        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_baseTotal = $baseTotal;
        $this->_requestFactory = $requestFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->invoiceService = $invoiceService;
        $this->order = $order;
        $this->orderFactory = $orderFactory;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
    }

    public function sendOrders($orderId = false)
    {
        if (!$this->_helper->isEnabled()) {
            return false;
        }

        try {
            $orders = $this->prepareOrders($orderId);
            $postData = $this->getPostData($orders);

            if ($this->createOrder($postData)) {
                $this->updateOrderStatus();
                $this->isSuccessful = true;
            } else {
                $this->checkForUpdates();
            }
        } catch (\Exception $e) {
			$this->_logger->info($e->getMessage());
        }

        return $this->isSuccessful;
    }

    protected function createOrder($postData)
    {
        if (!$postData) {
            return false;
        }

        return $this->_baseTotal->createOrder($postData);
    }

    protected function checkForUpdates()
    {
        if ($this->_baseTotal->getApi()->hasExistingOrders()) {
            $existingOrders = $this->_baseTotal->getApi()->getExistingOrders();
            $this->updateExistingOrders($existingOrders);
        }
    }
    protected function prepareOrders($orderId)
    {
        return $this->initSearch($orderId);
    }

    protected function updateExistingOrders($orders)
    {
        foreach ($orders['exist-order'] as $order) {
            if ($this->updateOrder($order)) {
                $this->updateOrderStatus();
            }
        }
    }

    public function updateOrder($orderId)
    {
        return $this->_baseTotal->updateOrder($orderId);
    }

    protected function getPostData($orders)
    {
        $postData = array();

        if (count($orders->getData()) > 0) {
            foreach ($orders as $order) {
                $request = $this->_requestFactory->create();
                $data = $request->getPostData($order);

                if ($data) {
                    array_push($postData, $data);
                }
            }
        }

        return $postData;
    }

    protected function initSearch($orderId = false)
    {
        $statusAllow = explode(",", $this->_helper->getFilterParams($orderId)['status']);
        $methodsAllow = explode(",", $this->_helper->getFilterParams($orderId)['payments']);
        $collection = $this->_orderCollectionFactory->create()
            ->join(
                ['payment' => 'sales_order_payment'],
                'payment.parent_id = main_table.entity_id',
                ['payment_method' => 'payment.method']
            )
            ->addFieldToFilter('payment.method', ['in' => [$methodsAllow]])
            ->setPageSize($this->_helper->getMassOrderQuantity());

		if($this->_helper->getMinimalDate()){
			$date = $this->_helper->getMinimalDate();
			$collection->addFieldToFilter('created_at', ['gt' => $date]);
		}
        if ($orderId) {
            $collection->addFieldToFilter(
                'increment_id',
                ['eq' => $orderId]
            );

		
        }else{
			$collection->addFieldToFilter('main_table.status', ['in' => [$statusAllow]]);
		}

        return $collection;
    }

    protected function updateOrderStatus()
    {
        $success = $this->_baseTotal->getSuccess();

        foreach ($success as $value) {
            try {
                $orderStatus = $this->_baseTotal->getHelper()->getOrderStatus($value['status']);

                if ($orderStatus == BaseHelper::APPROVED_STATUS) {
                    $this->approveOrder($value['order_number'], $orderStatus);
                }

                $this->setOrderStatus($value['order_number'], $orderStatus);
                $this->isSuccessful = true;
                $this->_logger->info('Order ' . $value['order_number'] . ' created.');
            } catch (\Exception $e) {
				$this->_logger->info($e->getMessage());
            }
        }
    }

    protected function setOrderStatus($orderNumber, $orderStatus)
    {
        $orderId = $this->getOrderEntityId($orderNumber);

        if ($orderId && $orderStatus) {
            $this->_helper->setOrderStatus($orderId, $orderStatus);
        } else {
			$this->_logger->info(__("Can't update order status for order: " . $orderNumber));
        }
    }

    protected function getOrderEntityId($orderNumber)
    {
        $orderCollection = $this->initSearch($orderNumber);

        if ($orderCollection) {
            foreach ($orderCollection as $order) {
                return $order->getEntityid();
            }
        }

        return false;
    }

    protected function approveOrder($incrementId, $status)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);
        $order->setState(Order::STATE_PROCESSING);

        if ($this->_helper->generateInvoiceAfterApproved()) {
            $this->generateInvoice($order);
        }

    }

    protected function putOrderInReview($incrementId)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);
        $order->setState(Order::STATE_PAYMENT_REVIEW);
        $history = $order->addStatusToHistory(__('Payment in review by Clearsale'));
        $history->save();
        $order->save();
    }

    private function generateInvoice($order)
    {
        if (!$order->canInvoice()) {
            return false;
        }

        $invoice = $this->invoiceService->prepareInvoice($order);
        if ($invoice->isEmpty()) {
			$this->_logger->info('Failed to generate invoice');
        }

        if ($invoice->getTotalQty() == 0) {
			$this->_logger->info('No items found to invoice');
        }

        $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
        $invoice->addComment(__('Clearsale: Automatic invoice'));
        $invoice->register();

        $order->setCustomerNoteNotify(true);
        $order->setIsInProcess(true);

        $transactionSave = $this->transaction->addObject($invoice)->addObject($order);
        $transactionSave->save();

        $order->save();
        $order->setDataChanges(false);

        // Send invoice email
        try {
            $this->invoiceSender->send($invoice);
        } catch (\Exception $e) {
			$this->_logger->info('Failed to send the invoice email: ' . $e->getMessage());
        }

    }
}
