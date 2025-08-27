<?php

namespace Commshop\Adminorder\Model\ResourceModel\Order\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;
use Magento\Backend\Model\Auth\Session;

class Collection extends OriginalCollection
{
    protected $_authSession;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Session $authSession,
        $mainTable = 'sales_order_grid',
        $resourceModel = \Magento\Sales\Model\ResourceModel\Order::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->_authSession = $authSession;
    }

    protected function _renderFiltersBefore()
    {
        $adminUser = $this->_authSession->getUser();

        if ($adminUser) {
            $userId = $adminUser->getId();
            $userRoleData = $adminUser->getRole()->getData();

            if (isset($userRoleData['role_id']) && ($userRoleData['role_id'] == 43 || $userRoleData['role_id'] == 101)) {
                $this->getSelect()->where('created_by_admin_user_id = ?', $userId);
            }
        }

        parent::_renderFiltersBefore();
    }
}
