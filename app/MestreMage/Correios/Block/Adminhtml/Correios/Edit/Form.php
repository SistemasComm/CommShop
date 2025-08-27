<?php
 /**
 * Mestre Magento
 * www.modulomagento.com.br
 *
 * LICENÇA DE USO
 * Este arquivo está sujeito ao EULA (contrato de licença para usuário final).
 * @copyright Copyright (c) Mestre Magento. ( https://www.modulomagento.com.br/ )
 * @license   https://www.modulomagento.com.br/Mestre-Magento-Licenca.txt
 *
 * ##############################################################################
 * #																			#
 * #  Nós programadores nos dedicamos muito, da mesma forma que você, então		#
 * #  valorize sua profissão e não aceite usar esse módulo de forma ilícita.	#
 * #  Denuncie anônimamente a empresa que utilizar esse módulo de forma ilegal.	#
 * #  Estamos abertos a parcerias com desenvolvedores ;)						#
 * #																			#
 * ##############################################################################
 *
 */

namespace MestreMage\Correios\Block\Adminhtml\Correios\Edit;

/**
 * Adminhtml Add New Row Form.
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context,
     * @param \Magento\Framework\Registry $registry,
     * @param \Magento\Framework\Data\FormFactory $formFactory,
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
     * @param \MestreMage\Correios\Model\Status $options,
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \MestreMage\Correios\Model\Status $options,
        array $data = []
    ) {
        $this->_options = $options;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form',
                            'enctype' => 'multipart/form-data',
                            'action' => $this->getData('action'),
                            'method' => 'post'
                        ]
            ]
        );

        $form->setHtmlIdPrefix('wkcorreios_');
        if ($model->getEntityId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Editar Faixa de CEP'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Cadastrar Faixa de CEP'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'servico',
            'select',
            [
                'name' => 'servico',
                'label' => __('Serviço'),
                'id' => 'title',
                'title' => __('Serviço'),
                'values' => $this->_options->getOptionArray(),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
         
        $fieldset->addField(
            'prazo',
            'text',
            [
                'name' => 'prazo',
                'label' => __('Prazo'),
                'id' => 'title',
                'title' => __('Prazo'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
         
        $fieldset->addField(
            'peso_inicial',
            'text',
            [
                'name' => 'peso_inicial',
                'label' => __('Peso Inicial'),
                'id' => 'title',
                'title' => __('Peso Inicial'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
         
        $fieldset->addField(
            'peso_final',
            'text',
            [
                'name' => 'peso_final',
                'label' => __('Peso Final'),
                'id' => 'title',
                'title' => __('Peso Final'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
         
        $fieldset->addField(
            'valor',
            'text',
            [
                'name' => 'valor',
                'label' => __('Valor'),
                'id' => 'title',
                'title' => __('Valor'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
         
        $fieldset->addField(
            'cep_inicial',
            'text',
            [
                'name' => 'cep_inicial',
                'label' => __('Cep Inicial'),
                'id' => 'title',
                'title' => __('Cep Inicial'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
         
        $fieldset->addField(
            'cep_final',
            'text',
            [
                'name' => 'cep_final',
                'label' => __('Cep Final'),
                'id' => 'title',
                'title' => __('Cep Final'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
