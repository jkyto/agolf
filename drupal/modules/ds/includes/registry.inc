<?php
/**
 * @file
 * Registry file for Display Suite.
 */

use Drupal\ds\Ds;

/**
 * Implements hook_theme().
 */
function _ds_theme() {
  $theme_functions = array();

  // Field templates
  if (\Drupal::config('ds.settings')->get('field_template')) {
    $field_layouts = \Drupal::service('plugin.manager.ds.field.layout')->getDefinitions();
    foreach ($field_layouts as $key => $plugin) {
      if ($key != 'default') {
        $theme_functions['field__' . $plugin['theme']] = array(
          'render element' => 'elements',
          'function' => $plugin['theme'],
          'base hook' => 'field',
          'path' => drupal_get_path('module', $plugin['provider']),
        );

        if (!empty($plugin['path'])) {
          $theme_functions['field__' . $plugin['theme']]['file'] = $plugin['path'];
        }
      }
    }
  }

  return $theme_functions;
}

/**
 * Implements hook_module_implements_alter().
 */
function _ds_module_implements_alter(&$implementations, $hook) {

  // node_entity_view_display_alter() disables all labels on all fields
  // when the view mode is 'search_index'. If you set display modes for
  // this view mode by hand, then the hook isn't needed. Since this
  // may be called dozens of times on some pages, it's worth disabling it.
  if ($hook == 'entity_view_display_alter') {
    unset($implementations['node']);
  }

  // Because it's possible to turn on/off field templates,
  // we'll unset hooks here so ds_preprocess_field() doesn't run.
  $ft_hooks = array(
    'ds_field_settings_alter',
    'form_ds_classes_form_alter',
    'form_field_ui_field_edit_form_alter',
  );
  if (!\Drupal::config('ds.settings')->get('field_template') && in_array($hook, $ft_hooks)) {
    unset($implementations['ds']);
  }
}

/**
 * Implements hook_theme_registry_alter().
 */
function _ds_theme_registry_alter(&$theme_registry) {
  $layouts = Ds::getLayouts();
  foreach ($layouts as $info) {
    $key = $info['theme'];
    // Only add preprocess functions if entity exposes theme function.
    if (isset($theme_registry[$key])) {
      $theme_registry[$key]['preprocess functions'][] = 'ds_preprocess_ds_layout';
    }
  }

  // Remove ds_preprocess_field in case field templates is not enabled.
  if (!\Drupal::config('ds.settings')->get('field_template')) {
    $key = array_search('ds_preprocess_field', $theme_registry['field']['preprocess functions']);
    if (!empty($key)) {
      unset($theme_registry['field']['preprocess functions'][$key]);
    }
  }
}
