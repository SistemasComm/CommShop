<?php

namespace Clearsale\Base\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveSessionId implements ObserverInterface
{
    protected $_sessionManager;

    public function __construct(\Magento\Framework\Session\SessionManager $sessionManager)
    {
        $this->_sessionManager = $sessionManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();

            $sessionId = $this->_sessionManager->getSessionId();

            $order->setCsSessionId($sessionId);
        } catch (\Exception $e) {
        }
    }
}
