<?php

namespace Commshop\Adminorder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\Model\Auth\Session;

class SaveAdminUserOnOrder implements ObserverInterface
{
    protected $backendAuthSession;

    public function __construct(Session $backendAuthSession)
    {
        $this->backendAuthSession = $backendAuthSession;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $user = $this->backendAuthSession->getUser();

        if ($user) {
            $order->setCreatedByAdminUserId($user->getId());
            $order->save();
        }
    }
}
