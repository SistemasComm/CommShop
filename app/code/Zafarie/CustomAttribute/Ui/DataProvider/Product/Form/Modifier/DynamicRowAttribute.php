<?php

namespace Zafarie\CustomAttribute\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollection;

use Magento\Framework\Stdlib\ArrayManager;

class DynamicRowAttribute extends AbstractModifier
{
  public const PRODUCT_ATTRIBUTE_CODE = 'dynamic_row_attribute';
  public const FIELD_IS_DELETE = 'is_delete';
  public const FIELD_SORT_ORDER_NAME = 'sort_order';

  /**
   * Dependency Initilization
   *
   * @param LocatorInterface $locator
   * @param AttributeSetCollection $attributeSetCollection
   * @param \Magento\Framework\Serialize\SerializerInterface $serializer
   * @param ArrayManager $arrayManager
   */
  public function __construct(
    private LocatorInterface $locator,
    protected AttributeSetCollection $attributeSetCollection,
    protected \Magento\Framework\Serialize\SerializerInterface $serializer,
    protected ArrayManager $arrayManager,
  ) {
  }

  /**
   * Modify Data
   *
   * @param array $data
   * @return array
   */
  public function modifyData(array $data)
  {
    $fieldCode = self::PRODUCT_ATTRIBUTE_CODE;

    $model = $this->locator->getProduct();
    $modelId = $model->getId();

    $highlightsData = $model->getDynamicRowAttribute();

    if ($highlightsData) {
      $highlightsData = $this->serializer->unserialize($highlightsData, true);
      $path = $modelId . '/' . self::DATA_SOURCE_DEFAULT . '/' . $fieldCode;
      $data = $this->arrayManager->set($path, $data, $highlightsData);
    }
    return $data;
  }

  /**
   * Modify Meta
   *
   * @param array $meta
   * @return array
   */
  public function modifyMeta(array $meta)
  {
    $highlightsPath = $this->arrayManager->findPath(
      self::PRODUCT_ATTRIBUTE_CODE,
      $meta,
      null,
      'children'
    );

    if ($highlightsPath) {
      $meta = $this->arrayManager->merge(
        $highlightsPath,
        $meta,
        $this->initHighlightFieldStructure($meta, $highlightsPath)
      );
      $meta = $this->arrayManager->set(
        $this->arrayManager->slicePath($highlightsPath, 0, -3)
        . '/' . self::PRODUCT_ATTRIBUTE_CODE,
        $meta,
        $this->arrayManager->get($highlightsPath, $meta)
      );
      $meta = $this->arrayManager->remove(
        $this->arrayManager->slicePath($highlightsPath, 0, -2),
        $meta
      );
    }

    return $meta;
  }

  /**
   * Add Attribute Grid Config
   *
   * @param int $sortOrder
   * @return array
   */
  protected function addAttributeGridConfig($sortOrder)
  {
    return [
      'arguments' => [
        'data' => [
          'config' => [
            'addButtonLabel' => __('Add Attribute'),
            'componentType' => DynamicRows::NAME,
            'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows',
            'additionalClasses' => 'admin__field-wide',
            'deleteProperty' => static::FIELD_IS_DELETE,
            'deleteValue' => '1',
            'renderDefaultRecord' => false,
            'sortOrder' => $sortOrder,
          ],
        ],
      ],
      'children' => [
        'record' => [
          'arguments' => [
            'data' => [
              'config' => [
                'componentType' => Container::NAME,
                'component' => 'Magento_Ui/js/dynamic-rows/record',
                'positionProvider' => static::FIELD_SORT_ORDER_NAME,
                'isTemplate' => true,
                'is_collection' => true,
              ],
            ],
          ],
          'children' => [
            'attribute_type' => [
              'arguments' => [
                'data' => [
                  'config' => [
                    'componentType' => Field::NAME,
                    'formElement' => Input::NAME,
                    'dataType' => Text::NAME,
                    'label' => __('Attribute Type'),
                    'enableLabel' => true,
                    'dataScope' => 'attribute_type',
                    'sortOrder' => 40,
                    'validation' => [
                      'required-entry' => true,
                    ],
                  ],
                ],
              ],
            ],
            'attribute_lable' => [
              'arguments' => [
                'data' => [
                  'config' => [
                    'componentType' => Field::NAME,
                    'formElement' => Input::NAME,
                    'dataType' => Text::NAME,
                    'label' => __('Attribute'),
                    'enableLabel' => true,
                    'dataScope' => 'attribute_lable',
                    'sortOrder' => 40,
                    'validation' => [
                      'required-entry' => true,
                    ],
                  ],
                ],
              ],
            ],
            'actionDelete' => [
              'arguments' => [
                'data' => [
                  'config' => [
                    'componentType' => 'actionDelete',
                    'dataType' => Text::NAME,
                    'label' => '',
                    'sortOrder' => 50,
                  ],
                ],
              ],
            ],
          ]
        ]
      ]
    ];
  }

  /**
   * Get attraction highlights dynamic rows structure
   *
   * @param array $meta
   * @param string $highlightsPath
   * @return array
   */
  protected function initHighlightFieldStructure($meta, $highlightsPath)
  {
    return [
      'arguments' => [
        'data' => [
          'config' => [
            'componentType' => 'dynamicRows',
            'label' => __('Custom Dynamic Rows'),
            'renderDefaultRecord' => false,
            'recordTemplate' => 'record',
            'dataScope' => '',
            'dndConfig' => [
              'enabled' => false,
            ],
            'disabled' => false,
            'sortOrder' =>
              $this->arrayManager->get($highlightsPath . '/arguments/data/config/sortOrder', $meta),
          ],
        ],
      ],
      'children' => [
        'record' => [
          'arguments' => [
            'data' => [
              'config' => [
                'componentType' => Container::NAME,
                'isTemplate' => true,
                'is_collection' => true,
                'component' => 'Magento_Ui/js/dynamic-rows/record',
                'dataScope' => '',
              ],
            ],
          ],
          'children' => [
            'title' => [
              'arguments' => [
                'data' => [
                  'config' => [
                    'formElement' => Input::NAME,
                    'componentType' => Field::NAME,
                    'dataType' => Text::NAME,
                    'label' => __('Title'),
                    'dataScope' => 'title',
                    'require' => '1',
                  ],
                ],
              ],
            ],

            'value' => [
              'arguments' => [
                'data' => [
                  'config' => [
                    'formElement' => Input::NAME,
                    'componentType' => Field::NAME,
                    'dataType' => Text::NAME,
                    'label' => __('Value'),
                    'dataScope' => 'value',
                    'require' => '1',
                  ],
                ],
              ],
            ],
            'actionDelete' => [
              'arguments' => [
                'data' => [
                  'config' => [
                    'componentType' => 'actionDelete',
                    'dataType' => Text::NAME,
                    'label' => '',
                  ],
                ],
              ],
            ],
          ],
        ],
      ],
    ];
  }
}