<?php

namespace Drupal\Tests\commerce_store_gateways\Kernel;

use Drupal\commerce_payment\Entity\PaymentGateway;
use Drupal\Tests\commerce\Kernel\CommerceKernelTestBase;

/**
 * Tests overriding payment gateway configuration from store data.
 *
 * @group commerce_store_gateways
 */
class ConfigOverrideTest extends CommerceKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'entity_reference_revisions',
    'path',
    'profile',
    'state_machine',
    'commerce_order',
    'commerce_payment',
    'commerce_payment_example',
    'commerce_store_gateways',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $field_definition = commerce_store_gateways_get_field_definition('online');
    /** @var \Drupal\commerce\ConfigurableFieldManagerInterface $configurable_field_manager */
    $configurable_field_manager = \Drupal::service('commerce.configurable_field_manager');
    $configurable_field_manager->createField($field_definition);

    // Reload the entity to get access to the new field.
    $this->store = $this->reloadEntity($this->store);
  }

  /**
   * Tests the override process.
   */
  public function testOverride() {
    /** @var \Drupal\commerce_payment\Entity\PaymentGateway $gateway */
    $gateway = PaymentGateway::create([
      'id' => 'onsite_test',
      'label' => 'On-site test',
      'plugin' => 'example_onsite',
      'configuration' => [
        'api_key' => '2342fewfsfs',
        'payment_method_types' => ['credit_card'],
      ],
    ]);
    $gateway->save();

    $this->store->set('payment_gateways', [
      [
        'config_name' => 'commerce_payment.commerce_payment_gateway.onsite_test',
        'data' => [
          'configuration' => [
            'api_key' => 'real_api_key',
            'payment_method_types' => ['credit_card'],
          ],
        ],
      ]
    ]);
    $this->store->save();
    $this->store = $this->reloadEntity($this->store);

    $gateway = $this->reloadEntity($gateway);
    $configuration = $gateway->getPluginConfiguration();
    $this->assertEquals('real_api_key', $configuration['api_key']);
  }

}
