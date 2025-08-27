<?php
namespace Zafarie\TechSpec\Plugin;

use Magento\Eav\Model\ResourceModel\Attribute;

class SaveShowInTechnicalSheet
{
    /**
     * Antes de salvar o atributo, injeta o valor do campo "Mostrar na Ficha Técnica".
     *
     * @param Attribute $subject
     * @param \Magento\Framework\DataObject $attribute
     * @return void
     */
    public function beforeSave(Attribute $subject, $attribute)
    {
        $data = $attribute->getData();
        
        if (isset($data['show_in_technical_sheet'])) {
            $attribute->setData('show_in_technical_sheet', $data['show_in_technical_sheet']);
        }
    } 
}
 