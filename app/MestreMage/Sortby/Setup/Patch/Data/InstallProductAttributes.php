<?php


namespace MestreMage\Sortby\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use MestreMage\Sortby\Setup\ProductSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallProductAttributes implements DataPatchInterface
{

    private $productSetupFactory;
    private $moduleDataSetup;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ProductSetupFactory $productSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ProductSetupFactory $productSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->productSetupFactory = $productSetupFactory;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        /** @var ProductSetup $productSetup */
        $productSetup = $this->productSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $productSetup->installEntities();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [
        
        ];
    }
}
