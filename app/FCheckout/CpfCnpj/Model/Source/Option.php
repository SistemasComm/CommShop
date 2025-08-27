<?php
/**
 *
 * @author      Mestre Magento
 * @copyright   2018 FCheckout (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */
namespace FCheckout\CpfCnpj\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\AbstractMethod;

class Option implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => __('No')
            ],
            [
                'value' =>  '1',
                'label' =>__('Optional')
            ],
            [
                'value' =>  '2',
                'label' =>__('Required')
            ]
        ];
    }
}
