<?php
namespace Zafarie\TechSpec\Plugin;

use Magento\Eav\Model\Attribute\Data\Provider;

class AddShowInTechnicalSheet
{
    public function afterGetMeta(Provider $subject, $result)
    {
        $result['front_fieldset']['children']['show_in_technical_sheet'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Mostrar na Ficha Técnica'),
                        'componentType' => 'field',
                        'formElement' => 'checkbox',
                        'dataType' => 'boolean',
                        'default' => 0, 
                        'sortOrder' => 90,
                        'notice' => __('Marque "Sim" para exibir na ficha técnica'),
                        'valueMap' => [
                            'true' => 1,
                            'false' => 0
                        ],
                    ]
                ]
            ]
        ];

        return $result;
    }
}
