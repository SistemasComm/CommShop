<?php
namespace MestreMage\OneStepCheckout\Model\Source;

use Magento\Backend\App\Action;

class SimpleFooter implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            '1' => __('Não'),
            '2' => __('Sim'),
        ];
    }
}