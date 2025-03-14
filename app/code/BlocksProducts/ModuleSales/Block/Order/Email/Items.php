<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Sales Order Email order items
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace BlocksProducts\ModuleSales\Block\Order\Email;
use Magento\Sales\Block\Order\Email\Items as BaseItems;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Sales Order Email items.
 *
 * @api
 * @since 100.0.2
 */
class Items extends BaseItems {

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param Context $context
     * @param array $data
     * @param OrderRepositoryInterface|null $orderRepository
     */
    public function __construct(
            \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
            Context $context,
            array $data = [],
            ?OrderRepositoryInterface $orderRepository = null
    ) {
       
        $this->searchCriteriaBuilder = $searchCriteriaBuilderFactory->create();
         $this->orderRepository = $orderRepository ?: ObjectManager::getInstance()->get(OrderRepositoryInterface::class);
        parent::__construct($context, $data);
    }

    /**
     * Returns order.
     *
     * Custom email templates are only allowed to use scalar values for variable data.
     * So order is loaded by order_id, that is passed to block from email template.
     * For legacy custom email templates it can pass as an object.
     *
     * @return OrderInterface|null
     * @since 102.1.0
     */
    public function getOrder() {
        $incrementId = $this->getData('order_increment_id'); // Assumes this data is passed
 $searchCriteria = $this->searchCriteriaBuilder->addFilter(
        OrderInterface::INCREMENT_ID,
        $incrementId
    )->create();
 
    $result = $this->orderRepository->getList($searchCriteria);
      if (empty($result->getItems())) {
        throw new \Magento\Framework\Exception\NoSuchEntityException(__('No such order.'));
    }

    $orders = $result->getItems();

    return reset($orders);
    }
}
