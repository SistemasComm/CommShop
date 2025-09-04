<?php
namespace Zafarie\TechSpec\Block\Adminhtml\Product\Attribute\Edit\Tab;

use Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Front as MagentoFront;

class Front extends MagentoFront
{
    /**
     * Adiciona lógica customizada no campo 'Mostrar na Ficha Técnica'
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = $this->getForm();
        $fieldset = $form->getElement('front_fieldset');

        // Recupera os dados do atributo do formulário
        $attributeData = $this->getRequest()->getParam('attribute_id') 
            ? $this->_coreRegistry->registry('entity_attribute')->getData()
            : [];

        $value = isset($attributeData['show_in_technical_sheet']) 
            ? $attributeData['show_in_technical_sheet'] 
            : 0;

        // Adiciona o campo "Mostrar na Ficha Técnica"
        $fieldset->addField(
            'show_in_technical_sheet',
            'select',
            [
                'name' => 'show_in_technical_sheet',
                'label' => __('Mostrar na Ficha Técnica'),
                'title' => __('Mostrar na Ficha Técnica'),
                'values' => [
                    ['value' => 0, 'label' => __('Não')],
                    ['value' => 1, 'label' => __('Sim')],
                ],
                'note' => __('Marque "Sim" para exibir este atributo na Ficha Técnica do produto.'),
                'value' => $value, // Preenche o valor do banco
            ]
        );

        return $this;
    }
}
