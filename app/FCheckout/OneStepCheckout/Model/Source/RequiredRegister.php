<?php
namespace FCheckout\OneStepCheckout\Model\Source;

use Magento\Backend\App\Action;

class RequiredRegister implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            '0' => __('Não Obrigatório'),
            '1' => __('Obrigatório')
        ];
    }
}