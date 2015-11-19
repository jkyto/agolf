<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\DsFieldBase.
 */

namespace Drupal\ds\Plugin\DsField;

use Drupal\Component\Plugin\PluginBase as ComponentPluginBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base class for all the ds plugins.
 */
abstract class DsFieldBase extends ComponentPluginBase implements DsFieldInterface {

  /**
   * Constructs a Display Suite field plugin.
   */
  public function __construct($configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configuration += $this->defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm($form, FormStateInterface $form_state) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary($settings) {
    return array();
  }

  /**
   *
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
   return array();
  }

   /**
   * {@inheritdoc}
    */
  public function getConfiguration() {
    return $this->configuration;
  }

   /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration + $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function formatters() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function isAllowed() {
    $definition = $this->getPluginDefinition();
    if (!isset($definition['ui_limit'])) {
      return TRUE;
    }

    $limits = $definition['ui_limit'];
    foreach ($limits as $limit) {
      if (strpos($limit, '|') !== FALSE) {
        list($bundle_limit, $view_mode_limit) = explode('|', $limit);

        if (($bundle_limit == $this->bundle() || $bundle_limit == '*') && ($view_mode_limit == $this->viewMode() || $view_mode_limit == '*')) {
          return TRUE;
        }
      }
    }

    // When the current bundle view_mode combination is not allowed we shouldn't
    // show the field.
    return FALSE;
  }

  /**
   * Gets the current entity.
   */
  public function entity() {
    return $this->configuration['entity'];
  }

  /**
   * Gets the current entity type.
   */
  public function getEntityTypeId() {
    if (isset($this->configuration['entity_type'])) {
      return $this->configuration['entity_type'];
    }
    elseif ($entity = $this->entity()) {
      /** @var $entity EntityInterface */
      return $entity->getEntityTypeId();
    }
    else {
      return '';
    }
  }

  /**
   * Gets the current bundle.
   */
  public function bundle() {
    return $this->configuration['bundle'];
  }

  /**
   * Gets the view mode
   */
  public function viewMode() {
    return $this->configuration['view_mode'];
  }

  /**
   * Gets the field configuration
   */
  public function getFieldConfiguration() {
    return $this->configuration['field'];
  }

  /**
   * Gets the field name
   */
  public function getName() {
    return $this->configuration['field_name'];
  }

  /**
   * @inheritdoc
   */
  public function calculateDependencies() {
    // By default there are no dependencies
    return array();
  }

}
