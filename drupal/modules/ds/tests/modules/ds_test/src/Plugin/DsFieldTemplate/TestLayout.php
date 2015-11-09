<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsFieldTemplate\TestLayout.
 */

namespace Drupal\ds_test\Plugin\DsFieldTemplate;

use Drupal\ds\Plugin\DsFieldTemplate\DsFieldTemplateBase;

/**
 * Plugin for the expert field template.
 *
 * @DsFieldTemplate(
 *   id = "ds_test_theming_function",
 *   title = @Translation("Field test function"),
 *   theme = "ds_test_theming_function",
 * )
 */
class TestLayout extends DsFieldTemplateBase {

}
