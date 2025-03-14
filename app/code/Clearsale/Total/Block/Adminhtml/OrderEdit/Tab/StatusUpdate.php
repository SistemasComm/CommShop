<?php

namespace Clearsale\Total\Block\Adminhtml\OrderEdit\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Clearsale\Total\Helper\Data;


/**
 * Order custom tab
 *
 */
class StatusUpdate extends Template implements TabInterface
{
    protected $_template = 'tab/view/total_statusupdate.phtml';

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $onclick = "submitClearsaleStatusAndReloadArea($('clearsale_status_update_block').parentNode, '" . $this->getSubmitUrl() . "')";
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Status update'), 'class' => 'action-save action-secondary', 'onclick' => $onclick, 'disabled' => 'disabled']
        );
        $this->setChild('submit_button', $button);
        return parent::_prepareLayout();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('statusupdate/sales_order/updateClearsaleStatus', ['order_id' => $this->getOrderId()]);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrderId()
    {
        return $this->getOrder()->getEntityId();
    }

    /**
     * Retrieve order increment id
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Status update Clearsale');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Status update Clearsale');
    }

    public function getPaymentStatusOption()
    {
        return [
            'PGR' => __('Disapproved Payment'),
            'PGA' => __('Approved Payment')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        if ($this->helper->isEnabled()) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}