<?php
/**
 * @category   Commcenter
 * @package    Commcenter_Preregistro
 * @author     yurirneves@gmail.com
 */

namespace Commcenter\Preregistro\Block\Adminhtml\Items\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        \Magento\Framework\Registry $registry, 
        \Magento\Framework\Data\FormFactory $formFactory,  
        array $data = []
    ) 
    {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_commcenter_preregistro_items');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);
        
        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        // Add the 'name' field
        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Nome'), 'title' => __('Nome'), 'required' => true]
        );

        // Add the 'email' field
        $fieldset->addField(
            'email',
            'text',
            ['name' => 'email', 'label' => __('Email'), 'title' => __('Email'), 'required' => true]
        );

        // Add the 'cpf' field
        $fieldset->addField(
            'cpf',
            'text',
            ['name' => 'cpf', 'label' => __('CPF'), 'title' => __('CPF'), 'required' => true]
        );

        // Add the 'phone' field
        $fieldset->addField(
            'phone',
            'text',
            ['name' => 'phone', 'label' => __('Telefone'), 'title' => __('Telefone'), 'required' => true]
        );

        // Add the 'terms' field as a checkbox
        $fieldset->addField(
            'terms',
            'checkbox',
            [
                'name' => 'terms',
                'label' => __('Termos Aceitos'),
                'title' => __('Termos Aceitos'),
                'required' => true,
                'value' => 1,  // Value when checked
                'checked' => $model->getData('terms') ? 'checked' : '',
                'after_element_html' => '<small>' . __('Marque para aceitar os termos.') . '</small>'
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
