<?php

namespace Commshop\Adminorder\Plugin\Model\Order;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as SalesOrderGridCollection;
use Magento\Backend\Model\Auth\Session as AuthSession;

class SalesOrderGridDataProvider
{
    protected $authSession;

    public function __construct(AuthSession $authSession)
    {
        $this->authSession = $authSession;
    }

    public function aroundGetSearchResult($subject, $proceed)
    {
        /** @var SalesOrderGridCollection $result */
        $result = $proceed();

        if (!$result instanceof SalesOrderGridCollection) {
            return $result;
        }

        $adminUser = $this->authSession->getUser();

        if (!$adminUser) {
            return $result;
        }

        $userId = $adminUser->getId();
        $userRoleData = $adminUser->getRole()->getData();

        if (isset($userRoleData['role_id']) && ($userRoleData['role_id'] == 43 || $userRoleData['role_id'] == 101)) {
          $result->addFieldToFilter('created_by_admin_user_id', $userId);
        }

        return $result;
    }
}
