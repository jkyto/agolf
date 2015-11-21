<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\BlockBase.
 */

namespace Drupal\ds\Plugin\DsField;

use Drupal\Core\Block\BlockPluginInterface;

/**
 * The base plugin to create DS block fields.
 */
abstract class BlockBase extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $manager = \Drupal::service('plugin.manager.block');

    // Create an instance of the block.
    /** @var $block BlockPluginInterface */
    $block_id = $this->blockPluginId();
    $block = $manager->createInstance($block_id);

    // Apply block config.
    $block_config = $this->blockConfig();
    $block->setConfiguration($block_config);

    // Get render array.
    $block_elements = $block->build();

    return $block_elements;
  }

  /**
   * Returns the plugin ID of the block.
   */
  protected function blockPluginId() {
    return '';
  }

  /**
   * Returns the config of the block.
   */
  protected function blockConfig() {
    return array();
  }
}
