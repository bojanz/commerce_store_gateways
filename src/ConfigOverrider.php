<?php

namespace Drupal\commerce_store_gateways;

use Drupal\commerce_store\CurrentStoreInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;

/**
 * Overrides payment gateway configuration with current store data.
 */
class ConfigOverrider implements ConfigFactoryOverrideInterface {

  /**
   * The current store.
   *
   * @var \Drupal\commerce_store\CurrentStoreInterface
   */
  protected $currentStore;

  /**
   * Constructs a new ConfigOverrider object.
   *
   * @param \Drupal\commerce_store\CurrentStoreInterface $current_store
   *   The current store.
   */
  public function __construct(CurrentStoreInterface $current_store) {
    $this->currentStore = $current_store;
  }

  /**
   * {@inheritdoc}
   */
  public function loadOverrides($names) {
    if (!$this->applies($names)) {
      return [];
    }
    $store = $this->currentStore->getStore();
    if (!$store->hasField('payment_gateways') || $store->get('payment_gateways')->isEmpty()) {
      return [];
    }

    $overrides = [];
    foreach ($store->get('payment_gateways')->getValue() as $item) {
      $overrides[$item['config_name']] = $item['data'];
    }

    return $overrides;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheSuffix() {
    return 'commerce_store_gateways';
  }

  /**
   * {@inheritdoc}
   */
  public function createConfigObject($name, $collection = StorageInterface::DEFAULT_COLLECTION) {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata($name) {
    if (!$this->applies([$name])) {
      return new CacheableMetadata();
    }

    // @todo Add the store metadata here.
    return new CacheableMetadata();
  }

  /**
   * Gets whether the config overrider applies.
   *
   * The config overrider applies only if one of the requested configuration
   * objects is a payment gateway.
   *
   * @param $names
   *   The configuration names.
   *
   * @return bool
   */
  protected function applies($names) {
    foreach ($names as $name) {
      if (strpos($name, 'commerce_payment.commerce_payment_gateway.') !== FALSE) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
