<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsFieldTemplate\Minimal.
 */

namespace Drupal\ds\Plugin\DsFieldTemplate;

use Drupal\ds\Ds;

/**
 * Plugin for the minimal field template.
 *
 * @DsFieldTemplate(
 *   id = "minimal",
 *   title = @Translation("Minimal"),
 *   theme = "ds_field_minimal",
 * )
 */
class Minimal extends DsFieldTemplateBase {

  /**
   * {@inheritdoc}
   */
  public function alterForm(&$form) {
    // Field classes.
    $config = $this->getConfiguration();
    $field_classes = Ds::getClasses('field');
    if (!empty($field_classes)) {
      $form['classes'] = array(
        '#type' => 'select',
        '#multiple' => TRUE,
        '#options' => $field_classes,
        '#title' => t('Choose additional CSS classes for the field'),
        '#default_value' => $config['classes'],
        '#prefix' => '<div class="field-classes">',
        '#suffix' => '</div>',
      );
    }
    else {
      $form['classes'] = array(
        '#type' => 'value',
        '#value' => array(''),
      );
    }
    parent::alterForm($form);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $config = parent::defaultConfiguration();
    $config['classes'] = array();
    return $config;
  }

  /**
   * {@inheritdoc}
   */
  public function massageRenderValues(&$field_settings, $values) {
    if (isset($values['classes'])) {
      $classes = is_array($values['classes']) ? implode(' ', $values['classes']) : $values['classes'];
      if (!empty($classes)) {
        $field_settings['classes'] = $classes;
      }
    }

    // Token replacement.
    if ($entity = $this->getEntity()) {
      // Tokens
      $apply_to = array(
        'classes',
      );

      foreach ($apply_to as $identifier) {
        $field_settings[$identifier] = \Drupal::token()->replace(
          $field_settings[$identifier],
          array($entity->getEntityTypeId() => $entity),
          array('clear' => TRUE)
        );
      }
    }
    parent::massageRenderValues($field_settings, $values);
  }

}
