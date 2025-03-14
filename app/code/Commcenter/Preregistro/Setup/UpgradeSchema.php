<?php
namespace Commcenter\Preregistro\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * UpgradeSchema class to modify the table
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Verifica se a tabela já foi criada e se a coluna 'operadora' não existe
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            // Adiciona a coluna 'operadora' à tabela existente
            $installer->getConnection()->addColumn(
                $installer->getTable('commcenter_preregistro'),
                'operadora',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Operadora'
                ]
            );
        }

        $installer->endSetup();
    }
}
