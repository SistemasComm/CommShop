<?php

namespace Clearsale\Total\Controller\Adminhtml\Sales\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Controller\Adminhtml\Order;

class SendChargebackFeedback extends Order
{
    private $order;
    const CHARGEBACK_COMMENT = 'Pedido com Chargeback';
    const CHARGEBACK_DONE_STATUS = 'Chargeback done';

    public function execute()
    {
        $this->order = $this->_initOrder();
        $data = $this->getRequest()->getPost('chargeback');

        if (!$this->order) {
            throw new LocalizedException(__('Order not found.'));
        }
        if (!$data['has_chargeback']) {
            throw new LocalizedException(__('Please select a chargeback.'));
        }
        $postData = [
            'orders' => [$this->order->getIncrementId()],
            'message' => $data['comment']
        ];
        $clearsaleTotalModel = $this->_objectManager->create('Clearsale\Base\Model\Products\Total');

        try {
            $clearsaleTotalModel->markChargeback($postData);
            $responseBody = $clearsaleTotalModel->getApi()->getResponseBody()[0];
            if ($responseBody->status != self::CHARGEBACK_DONE_STATUS) {
                $this->chargebackInvalid();
            }
            $this->chargebackDone();
        } catch (LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function chargebackDone()
    {
        $this->messageManager->addSuccess(__('Chargeback feedback has successfully submitted.'));
        $orderFactory = $this->_objectManager->create('Magento\Sales\Model\OrderFactory');
        $orderObj = $orderFactory->create()->load($this->order->getId());
        $history = $orderObj->addStatusHistoryComment(self::CHARGEBACK_COMMENT);
        $history->save();
        $orderObj->save();
        return $this->resultPageFactory->create();
    }

    public function chargebackInvalid()
    {
        $response = ['error' => true, 'message' => __('We cannot send the chargeback feedback.')];
        if (is_array($response)) {
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        }
        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
