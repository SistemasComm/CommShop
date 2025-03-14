<?php

namespace MestreMage\Correios\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Option\ArrayInterface;

class AtributeProduct implements ArrayInterface
{
    protected $_attributeFactory;
    public function __construct(Attribute $attributeFactory) {
        $this->_attributeFactory = $attributeFactory;
    }

    public function toOptionArray()
    {
        $attributeInfo = $this->_attributeFactory->getCollection();
        $return = [];
        foreach($attributeInfo as $attributes){
                if($attributes->getData('entity_type_id') == 4){
                    $return[] = [
                        'value' => $attributes->getAttributeCode(),
                        'label' => $attributes->getFrontendLabel() 
                    ];
                }

            }

        return $return;
    }
}