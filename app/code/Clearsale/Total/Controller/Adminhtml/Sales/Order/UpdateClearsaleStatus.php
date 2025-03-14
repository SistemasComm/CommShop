<?php

namespace Clearsale\Total\Controller\Adminhtml\Sales\Order;

use Magento\Sales\Controller\Adminhtml\Order;
use Magento\Framework\Exception\LocalizedException;

class UpdateClearsaleStatus extends Order
{
    const UPDATESTATUS_DONE_STATUS = 'OK';
    const UPDATESTATUS_MESSAGE = 'The order had its status in the clearsale updated';

    public function execute()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $data = $this->getRequest()->getPost('statusupdate');

                $postData = [
                    'order' => $order->getIncrementId(),
                    'status' => $data['status']
                ];
                $clearsaletotal = $this->_objectManager->create('Clearsale\Base\Model\Products\Total');

                $updateStatusRequest = $clearsaletotal->updateStatus($postData, true);

                if ($updateStatusRequest) {
                    $responseBody = $clearsaletotal->getApi()->getResponseBody();
                    $status = $responseBody->status;
                    if ($status == self::UPDATESTATUS_DONE_STATUS) {
                        $this->messageManager->addSuccess(__('Order status at clearsale has been successfully updated.'));
                        $orderFactory = $this->_objectManager->create('Magento\Sales\Model\OrderFactory');
                        $order = $orderFactory->create()->load($order->getId());
                        $history = $order->addStatusHistoryComment(self::UPDATESTATUS_MESSAGE);
                        $history->save();
                        $order->save();
                        return $this->resultPageFactory->create();
                    }
                }
                $response = ['error' => true, 'message' => __('We cannot update order status at Clearsale.')];
            } catch (LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            }
        }
        if (is_array($response)) {
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        }
        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}