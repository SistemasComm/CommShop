<?php

namespace Commshop\Adminorder\Plugin;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderGridCollection;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Commshop\Adminorder\Model\ResourceModel\Order\Grid\Collection as SalesOrderGridCollection;
use Magento\Framework\App\RequestInterface;

class OrderGridCollectionPlugin
{
    protected $authSession;
    protected $request;

    const ROLE_ID_TO_FILTER = 43; // ID da role que verá apenas os próprios pedidos

    public function __construct(
        AuthSession $authSession,
        RequestInterface $request
    ) {
        $this->authSession = $authSession;
        $this->request = $request;
    }

    public function beforeLoad(SalesOrderGridCollection $collection) {
        $adminUser = $this->authSession->getUser();
        if (!$adminUser) {
            return;
        }
    
        $userId = $adminUser->getId();
        $userRoleData = $adminUser->getRole()->getData();
    
        // Verifica se está na página de listagem dos pedidos no painel
        $moduleName = $this->request->getModuleName();
        $controllerName = $this->request->getControllerName();
        $actionName = $this->request->getActionName();
    
        if ($moduleName == 'sales' && $controllerName == 'order' && $actionName == 'index' && isset($userRoleData['role_id']) && $userRoleData['role_id'] == self::ROLE_ID_TO_FILTER) {
            $collection->addFieldToFilter('created_by_admin_user_id', $userId);
        }
    
        // Verifique se a junção já foi adicionada
        $parts = $collection->getSelect()->getPart(\Zend_Db_Select::FROM);
        if (!isset($parts['admin_user'])) {
            // Adicionando a junção com a tabela admin_user
            $collection->getSelect()->joinLeft(
                ['admin_user' => $collection->getTable('admin_user')],
                'main_table.created_by_admin_user_id = admin_user.user_id',
                [
                    'admin_fullname' => new \Zend_Db_Expr("CONCAT(admin_user.firstname, ' ', admin_user.lastname)"),
                    'admin_cpf' => 'admin_user.CPF'
                ]
            );
        }
    }    
    
    
}
