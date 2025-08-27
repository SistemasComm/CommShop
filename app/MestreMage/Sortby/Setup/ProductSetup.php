<?php


namespace MestreMage\Sortby\Setup;

use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product;

class ProductSetup extends EavSetup
{

    public function getDefaultEntities()
    {
        return [
            'catalog_product' => [
                'entity_type_id' => CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID,
                'entity_model' => Product::class,
                'attribute_model' => Attribute::class,
                'table' => 'catalog_product_entity',
                'additional_attribute_table' => 'catalog_eav_attribute',
                'entity_attribute_collection' =>
                    Collection::class,
                'attributes' => [

                    'high_to_low' => [
                        'type' => 'int',
                        'label' => 'Maior Preço',
                        'input' => 'static',
                        'source' => '',
                        'frontend' => '',
                        'required' => false,
                        'note' => '',
                        'class' => '',
                        'backend' => '',
                        'sort_order' => '30',
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'default' => null,
                        'visible' => false,
                        'user_defined' => true,
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'unique' => false,
                        'apply_to' => '',
                        'group' => 'General',
                        'used_for_sort_by' => true,
                        'is_used_in_grid' => false,
                        'is_visible_in_grid' => false,
                        'is_filterable_in_grid' => false,
                        'option' => array('values' => array(""))
                    ],

                    'low_to_high' => [
                        'type' => 'int',
                        'label' => 'Menor Preço',
                        'input' => 'static',
                        'source' => '',
                        'frontend' => '',
                        'required' => false,
                        'note' => '',
                        'class' => '',
                        'backend' => '',
                        'sort_order' => '30',
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'default' => null,
                        'visible' => false,
                        'user_defined' => true,
                        'searchable' => true,
                        'filterable' => true,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'unique' => false,
                        'apply_to' => '',
                        'group' => 'General',
                        'used_for_sort_by' => true,
                        'is_used_in_grid' => false,
                        'is_visible_in_grid' => false,
                        'is_filterable_in_grid' => false,
                        'option' => array('values' => array(""))
                    ],

                    'high_to_new' => [
                        'type' => 'int',
                        'label' => 'Mais novos',
                        'input' => 'static',
                        'source' => '',
                        'frontend' => '',
                        'required' => false,
                        'note' => '',
                        'class' => '',
                        'backend' => '',
                        'sort_order' => '30',
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'default' => null,
                        'visible' => false,
                        'user_defined' => true,
                        'searchable' => true,
                        'filterable' => true,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'unique' => false,
                        'apply_to' => '',
                        'group' => 'General',
                        'used_for_sort_by' => true,
                        'is_used_in_grid' => false,
                        'is_visible_in_grid' => false,
                        'is_filterable_in_grid' => false,
                        'option' => array('values' => array(""))
                    ],

                    'discount_summary' => [
                        'type' => 'int',
                        'label' => 'Maior Desconto',
                        'input' => 'static',
                        'source' => '',
                        'frontend' => '',
                        'required' => false,
                        'note' => '',
                        'class' => '',
                        'backend' => '',
                        'sort_order' => '30',
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'default' => null,
                        'visible' => false,
                        'user_defined' => true,
                        'searchable' => true,
                        'filterable' => true,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'unique' => false,
                        'apply_to' => '',
                        'group' => 'General',
                        'used_for_sort_by' => true,
                        'is_used_in_grid' => false,
                        'is_visible_in_grid' => false,
                        'is_filterable_in_grid' => false,
                        'option' => array('values' => array(""))
                    ],

                    'sort_by_best_seller' => [
                        'type' => 'int',
                        'label' => 'Mais Vendidos',
                        'input' => 'static',
                        'source' => '',
                        'frontend' => '',
                        'required' => false,
                        'note' => '',
                        'class' => '',
                        'backend' => '',
                        'sort_order' => '30',
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'default' => null,
                        'visible' => false,
                        'user_defined' => true,
                        'searchable' => true,
                        'filterable' => true,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'unique' => false,
                        'apply_to' => '',
                        'group' => 'General',
                        'used_for_sort_by' => true,
                        'is_used_in_grid' => false,
                        'is_visible_in_grid' => false,
                        'is_filterable_in_grid' => false,
                        'option' => array('values' => array(""))
                    ],

                    'ratings_summary' => [
                        'type' => 'int',
                        'label' => 'Melhor Avaliados',
                        'input' => 'static',
                        'source' => '',
                        'frontend' => '',
                        'required' => false,
                        'note' => '',
                        'class' => '',
                        'backend' => '',
                        'sort_order' => '30',
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'default' => null,
                        'visible' => false,
                        'user_defined' => true,
                        'searchable' => true,
                        'filterable' => true,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'unique' => false,
                        'apply_to' => '',
                        'group' => 'General',
                        'used_for_sort_by' => true,
                        'is_used_in_grid' => false,
                        'is_visible_in_grid' => false,
                        'is_filterable_in_grid' => false,
                        'option' => array('values' => array(""))
                    ],

                ]
            ]
        ];
    }
}
