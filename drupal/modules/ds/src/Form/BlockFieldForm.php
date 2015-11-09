<?php

/**
 * @file
 * Contains \Drupal\ds\Form\BlockFieldForm.
 */

namespace Drupal\ds\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ds\Form\FieldFormBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

/**
 * Configure block fields.
 */
class BlockFieldForm extends FieldFormBase implements ContainerInjectionInterface {

  /**
   * The type of the dynamic ds field
   */
  const TYPE = 'block';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ds_field_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $field_key = '') {
    $form = parent::buildForm($form, $form_state, $field_key);
    $field = $this->field;

    $manager = \Drupal::service('plugin.manager.block');

    $blocks = array();
    foreach ($manager->getDefinitions() as $plugin_id => $plugin_definition) {
      $blocks[$plugin_id] = $plugin_definition['admin_label'];
    }
    asort($blocks);

    $form['block_identity']['block'] = array(
      '#type' => 'select',
      '#options' => $blocks,
      '#title' => t('Block'),
      '#required' => TRUE,
      '#default_value' => isset($field['properties']['block']) ? $field['properties']['block'] : '',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getProperties(FormStateInterface $form_state) {
    return array(
      'block' => $form_state->getValue('block'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return BlockFieldForm::TYPE;
  }

  /**
   * {@inheritdoc}
   */
  public function getTypeLabel() {
    return 'Block field';
  }

}
