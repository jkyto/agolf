<?php
/**
 * @files
 * Holds all the the field layout templates.
 */

/**
 * Reset all HTML for the field.
 */
function theme_ds_field_reset($variables) {
  $output = '';

  // Render the label.
  if (!$variables['label_hidden']) {
    $output .= '<div class="field-label-' . $variables['element']['#label_display'] . '">' . $variables['label'];
    if (\Drupal::config('ds.settings')->get('ft-show-colon')) {
      $output .= ':';
    }
    $output .= '</div>';
  }

  // Render the items.
  foreach ($variables['items'] as $item) {
    $output .= drupal_render($item['content']);
  }

  return $output;
}

/**
 * Provide minimal HTML for the field.
 */
function theme_ds_field_minimal($variables) {
  $output = '';
  $config = isset($variables['ds-config']['settings']) ? $variables['ds-config']['settings'] : array();
  $classes = isset($config['classes']) ? ' ' . implode(' ', $config['classes']) : '';

  // Add a simple wrapper.
  $output .= '<div class="field field-name-' . strtr($variables['element']['#field_name'], '_', '-') . $classes . '">';

  // Render the label.
  if (!$variables['label_hidden']) {
    $output .= '<div class="field-label-' . $variables['element']['#label_display'] . '">' . $variables['label'];
    if (!empty($config['lb-col'])) {
      $output .= ':';
    }
    $output .= '</div>';
  }

  // Render the items.
  foreach ($variables['items'] as $item) {
    $output .= drupal_render($item['content']);
  }
  $output .="</div>";

  return $output;
}

/**
 * Custom output all HTML for the field.
 */
function theme_ds_field_expert($variables) {
  $output = '';
  $config = isset($variables['ds-config']['settings']) ? $variables['ds-config']['settings'] : array();

  // Set prefix
  if (!empty($config['prefix'])) {
    $output .= $config['prefix'];
  }

  // Render the label if it's not hidden.
  if (!$variables['label_hidden']) {
    $label_wrapper = !empty($config['lbw-el']) ? $config['lbw-el'] : 'div';
    $class = array('field-label-' . $variables['element']['#label_display']);
    if (!empty($config['lbw-cl'])) $class[] = $config['lbw-cl'];
    $class = !empty($class) ? ' class="' . implode(' ', $class) . '"' : '';

    $attributes = array();
    if (!empty($config['lbw-at'])) $attributes[] = $config['lbw-at'];
    if (!empty($config['lbw-def-at'])) $attributes[] = $variables['title_attributes'];
    $tag_attributes = (!empty($attributes)) ? ' ' . implode(' ', $attributes) : '';

    $output .= '<' . $label_wrapper . $class . $tag_attributes . '>' . $variables['label'];
    if (!empty($config['lb-col'])) $output .= ':';
    $output .= '</' . $label_wrapper . '>';
  }

  // Field items wrapper
  if (!empty($config['fis'])) {
    $class = (!empty($config['fis-cl'])) ? ' class="' . $config['fis-cl'] . '"' : '';
    $attributes = array();
    if (!empty($config['fis-at'])) $attributes[] = $config['fis-at'];
    if (!empty($config['fis-def-at'])) $attributes[] = $variables['content_attributes'];
    $attributes = (!empty($attributes)) ? ' ' . implode(' ', $attributes) : '';
    $output .= '<' . $config['fis-el'] . $class . $attributes . '>';
  }

  // Render items.
  foreach ($variables['items'] as $delta => $item) {
    // Field item wrapper.
    if (!empty($config['fi'])) {
      $class = array();
      if (!empty($config['fi-cl'])) {
        $class[] = $config['fi-cl'];
      }
      $class = !empty($class) ? ' class="'. implode(' ', $class)  .'"' : '';
      $attributes = array();
      if (!empty($config['fi-at'])) {
        $attributes[] = $config['fi-at'];
      }
      if (!empty($config['fi-def-at'])) {
        if (!empty($variables['content_attributes'][$delta])) {
          $attributes[] = $variables['content_attributes'][$delta];
        }
      }
      $attributes = (!empty($attributes)) ? ' ' . implode(' ', $attributes) : '';
      $output .= '<' . $config['fi-el'] . $class . $attributes .'>';
    }

    // Render field content.
    $output .= drupal_render($item['content']);

    // Closing field item wrapper.
    if (!empty($config['fi'])) {
      $output .= '</' . $config['fi-el'] . '>';
    }
  }

  // Closing field items wrapper.
  if (!empty($config['fis'])) {
    $output .= '</' . $config['fis-el'] . '>';
  }

  // Outer wrapper.
  if (!empty($config['ow'])) {
    $class = array();

    if (!empty($config['ow-cl'])) {
      $class[] = $config['ow-cl'];
    }

    if (!empty($config['ow-def-cl']) && isset($variables['attributes']['class'])) {
      foreach ($variables['attributes']['class'] as $class_name) {
        $class[] = $class_name;
      }
    }

    $class = (!empty($class)) ? ' class="' . implode(' ', $class) . '"' : '';
    $attributes = array();
    if (!empty($config['ow-at'])) $attributes[] = $config['ow-at'];
    if (!empty($config['ow-def-at'])) {
      foreach ($variables['attributes'] as $key => $info) {
        if ($key != 'class') {
          $attributes[$key] = $key . '="' . $variables['attributes'][$key] . '"';
        }
      }
    }
    $attributes = (!empty($attributes)) ? ' ' . implode(' ', $attributes) : '';
    $output = '<' . $config['ow-el'] . $class . $attributes . '>' . $output . '</' . $config['ow-el'] . '>';
  }

  // Set suffix
  if (!empty($config['suffix'])) {
    $output .= $config['suffix'];
  }

  return $output;
}
