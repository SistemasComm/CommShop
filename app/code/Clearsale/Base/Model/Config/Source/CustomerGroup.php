<?php

namespace Clearsale\Base\Model\Config\Source;

/**
 * Class CustomerGroup
 * @package Clearsale\Base\Model\Config\Source
 */
class CustomerGroup implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Customer Group
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $_customerGroup;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
     * @param array $data
     */
    public function __construct(
            \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
        ) {
        $this->_customerGroup = $customerGroup;
    }
    /**
     * Get customer groups
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getCustomerGroups();
    }

    /**
     * @return array
     */
    public function getCustomerGroups()
    {
        $customerGroups = $this->_customerGroup->toOptionArray();
        //array_unshift($customerGroups, ['value'=>'', 'label'=>'Nenhum']);
        return $customerGroups;
    }
}
