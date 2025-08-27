<?php
namespace Vendor\Module\Plugin;

use Magento\Catalog\Block\Product\View;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class ProductView
{
    protected $customerSession;
    protected $orderCollectionFactory;

    public function __construct(
        CustomerSession $customerSession,
        OrderCollectionFactory $orderCollectionFactory
    ) {
        $this->customerSession = $customerSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    public function beforeToHtml(View $subject)
    {
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId && $this->hasExceededPurchaseLimit($customerId)) {
            // Remove o botão "Comprar"
            $subject->getLayout()->unsetElement('product.info.addtocart');
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
}
