<?php

namespace Drupal\commerce_store_gateways\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\MapDataDefinition;

/**
 * Plugin implementation of the 'commerce_config_override' field type.
 *
 * @FieldType(
 *   id = "commerce_config_override",
 *   label = @Translation("Configuration override"),
 *   description = @Translation("Stores configuration overrides."),
 *   category = @Translation("Commerce"),
 *   default_widget = "",
 *   default_formatter = "",
 * )
 */
class ConfigOverrideItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['config_name'] = DataDefinition::create('string')
      ->setLabel(t('Configuration name'));
    $properties['data'] = MapDataDefinition::create()
      ->setLabel(t('The override data'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'config_name' => [
          'description' => 'The configuration name.',
          'type' => 'varchar_ascii',
          'length' => 255,
          'not null' => TRUE,
        ],
        'data' => [
          'description' => 'The override data.',
          'type' => 'blob',
          'not null' => TRUE,
          'serialize' => TRUE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    return empty($this->config_name) || empty($this->data);
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    if (isset($values)) {
      $values += [
        'data' => [],
      ];
      // Single serialized values on shared tables for base fields are not
      // always unserialized. https://www.drupal.org/node/2788637
      if (is_string($values['data'])) {
        $values['data'] = unserialize($values['data']);
      }
    }

    parent::setValue($values, $notify);
  }

}
