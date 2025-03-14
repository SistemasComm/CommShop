<?php
namespace Clearsale\Base\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;

class InstallData implements InstallDataInterface
{
    protected $_salesSetupFactory;
 
    public function __construct(
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
    ) {
        $this->_salesSetupFactory = $salesSetupFactory;
    }
 

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
 
        $installer->startSetup();
 
        $salesSetup = $this->_salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $installer]);
 
        $salesSetup->addAttribute(Order::ENTITY, 'cs_session_id', [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> 255,
            'visible' => false,
            'nullable' => true
        ]);
 
        $installer->endSetup();
    }
}
