<?php

namespace Commshop\Admincpf\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getTable('admin_user');

        $setup->getConnection()->addColumn(
            $table,
            'cpf',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 15,
                'nullable' => false,
                'comment' => 'CPF'
            ]
        );

        $setup->endSetup();
    }
}
