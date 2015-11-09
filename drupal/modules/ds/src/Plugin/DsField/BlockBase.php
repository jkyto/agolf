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

    // Create the wanted block class
    $id = $this->blockPluginId();
    /** @var $block BlockPluginInterface */
    $block = $manager->createInstance($id);

    // Get render array.
    // @todo check label/subject.
    $block_elements = $block->build();

    return $block_elements;
  }

  /**
   * Returns the plugin ID of the block.
   */
  protected function blockPluginId() {
    return '';
  }

}
