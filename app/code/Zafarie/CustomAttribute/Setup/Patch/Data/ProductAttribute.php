<?php

namespace Zafarie\CustomAttribute\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Zafarie\CustomAttribute\Ui\DataProvider\Product\Form\Modifier\DynamicRowAttribute;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class ProductAttribute implements DataPatchInterface
{
  /**
   * Dependency Initilization
   *
   * @param ModuleDataSetupInterface $moduleDataSetup
   * @param CategorySetupFactory $categorySetupFactory
   * @param EavSetupFactory $eavSetupFactory
   */
  public function __construct(
    private ModuleDataSetupInterface $moduleDataSetup,
    private CategorySetupFactory $categorySetupFactory,
    private EavSetupFactory $eavSetupFactory
  ) {
  }

  /**
   * @inheritdoc
   */
  public function apply()
  {
    $eavSetup = $this->eavSetupFactory->create();
    $eavSetup->addAttribute(
      \Magento\Catalog\Model\Product::ENTITY,
      DynamicRowAttribute::PRODUCT_ATTRIBUTE_CODE,
      [
        'label' => 'Custom Dynamic Rows',
        'type'  => 'text',
        'default'  => '',
        'input' => 'text',
        'required' => false,
        'sort_order' => 1,
        'user_defined' => true,
        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        'used_in_product_listing' => true,
        'visible_on_front' => true,
        'visible' => true
      ]
    );
    $eavSetup->addAttributeToGroup(
      \Magento\Catalog\Model\Product::ENTITY,
      'Default',
      'General', // group
      DynamicRowAttribute::PRODUCT_ATTRIBUTE_CODE,
      1000 // sort order
    );
  }

  /**
   * @inheritdoc
   */
  public static function getDependencies()
  {
    return [];
  }

  /**
   * @inheritdoc
   */
  public function getAliases()
  {
    return [];
  }
}