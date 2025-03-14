<?php
namespace Vendor\Module\Plugin;

use Magento\Checkout\Model\GuestPaymentInformationManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Customer\Api\GroupRepositoryInterface;

class GuestPaymentInformationManagementPlugin
{
    protected $customerSession;
    protected $orderCollectionFactory;
    protected $groupRepository;

    public function __construct(
        CustomerSession $customerSession,
        OrderCollectionFactory $orderCollectionFactory,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->customerSession = $customerSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->groupRepository = $groupRepository;
    }

    public function beforeSavePaymentInformationAndPlaceOrder(
        GuestPaymentInformationManagement $subject,
        $cartId,
        $email,
        $paymentMethod,
        $billingAddress = null
    ) {
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId && $this->isCollaborator($customerId) && $this->hasExceededPurchaseLimit($customerId)) {
            $firstOrderDate = $this->getFirstOrderDate($customerId);
            $nextPurchaseDate = date('d/m/Y', strtotime('+1 year', strtotime($firstOrderDate)));
            throw new LocalizedException(__('Você já fez duas compras no último ano. Você só poderá fazer uma nova compra em %1.', $nextPurchaseDate));
        }
    }

    protected function hasExceededPurchaseLimit($customerId)
    {
        $oneYearAgo = date('Y-m-d H:i:s', strtotime('-1 year'));
        $orders = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('created_at', ['gteq' => $oneYearAgo]);

        return $orders->getSize() >= 2;
    }

    protected function getFirstOrderDate($customerId)
    {
        $oneYearAgo = date('Y-m-d H:i:s', strtotime('-1 year'));
        $orders = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('created_at', ['gteq' => $oneYearAgo])
            ->setOrder('created_at', 'ASC');

        return $orders->getFirstItem()->getCreatedAt();
    }

    protected function isCollaborator($customerId)
    {
        $customer = $this->customerSession->getCustomer();
        $groupId = $customer->getGroupId();
        $group = $this->groupRepository->getById($groupId);
        
        return $group->getCode() === 'Colaboradores';
    }
}
