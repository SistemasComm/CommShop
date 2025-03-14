<?php
namespace Clearsale\Total\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

            /**
             * Install order statuses from config
             */
            $data = [];
            $statuses = [
                'aprovacao_clearsale'  => __('Em aprovacao ClearSale'),
                'aprovado_clearsale' => __('Aprovado ClearSale'),
                'reprovado_clearsale' => __('Reprovado ClearSale')
            ];

            foreach ($statuses as $code => $info) {
                $data[] = ['status' => $code, 'label' => $info];
            }
            //create status
            $setup->getConnection()->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $data);

            $data = [];

            //prepare data for associate status to state
            foreach ($statuses as $code => $label) {
                $item = [
                    'label' => __($label),
                    'statuses' => [$code => ['default' => '1'], $code => []],
                    'visible_on_front' => true];
                $states[$code] = $item;
            }

            foreach ($states as $code => $info) {
                if (isset($info['statuses'])) {
                    foreach ($info['statuses'] as $status => $statusInfo) {
                        $data[] = [
                            'status' => $status,
                            'state' => 'processing',
                            'is_default' => is_array($statusInfo) && isset($statusInfo['default']) ? 1 : 0,
                        ];

                        $data[] = [
                            'status' => $status,
                            'state' => 'new',
                            'is_default' => is_array($statusInfo) && isset($statusInfo['default']) ? 1 : 0,
                        ];
                    }
                }
            }

            //Insert row for associate
            $setup->getConnection()->insertArray(
                $setup->getTable('sales_order_status_state'),
                ['status', 'state', 'is_default'],
                $data
            );

        $setup->endSetup();
    }
}
