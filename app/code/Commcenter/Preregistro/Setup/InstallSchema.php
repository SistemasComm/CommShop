<?php
namespace Commcenter\Preregistro\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * InstallSchema class for creating the table.
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Creating the table commcenter_preregistro
        $table = $installer->getConnection()->newTable(
            $installer->getTable('commcenter_preregistro')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Nome'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email'
        )->addColumn(
            'cpf',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'CPF'
        )->addColumn(
            'phone',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Telefone'
        )->addColumn(
            'terms',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Termos Aceitos'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Criado em'
        )->setComment(
            'Tabela de Registros'
        );

        // Execute the creation of the table
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
