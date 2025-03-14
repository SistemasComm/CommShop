<?php

namespace Clearsale\Base\Model\Config\Source;

class CustomerAttribute implements \Magento\Framework\Option\ArrayInterface
{
    protected $_attributeCollection;

    public function __construct(\Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollection)
    {
        $this->_attributeCollection = $attributeCollection;
    }

    public function toOptionArray()
    {
        $collection = $this->_attributeCollection->create();
        $collection->setAttributeSetFilter(1);
        $collection->getSelect()->order('frontend_label');

        $result = array();


        foreach ($collection as $child) {
            $result [] = ['value' => $child->getAttributeCode(), 'label' => $child->getFrontendLabel()];
        }

        return $result;
    }
}
