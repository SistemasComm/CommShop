<?php

namespace Zafarie\EmailCancel\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class OrderSaveAfter implements ObserverInterface
{
    protected $orderSender;
    protected $scopeConfig;

    public function __construct(
        OrderSender $orderSender,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->orderSender = $orderSender;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order->getState() == 'canceled') {
            $templateId = $this->scopeConfig->getValue(
                'sales_email/order/cancel_template',
                ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            );

            $this->orderSender->send($order, true, ['template_id' => $templateId]);
        }
    }
}