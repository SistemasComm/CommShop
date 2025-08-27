<?php
namespace Zafarie\TechSpec\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddShowInTechnicalSheetColumn implements SchemaPatchInterface
{
    private $moduleDataSetup;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->addColumn(
            $this->moduleDataSetup->getTable('catalog_eav_attribute'),
            'show_in_technical_sheet',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => '0',
                'comment' => 'Show in Technical Sheet'
            ]
        );
    }

    public static function getDependencies() { return []; }

    public function getAliases() { return []; }
}
 