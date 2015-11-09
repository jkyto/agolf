<?php

/**
 * @file
 * Contains \Drupal\ds\Access\ContextualTabAccessCheck.
 */

namespace Drupal\ds\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface as RoutingAccessInterface;
use Symfony\Component\Routing\Route;

/**
 * Provides an access check for ds extras switch field routes.
 */
class ContextualTabAccessCheck implements RoutingAccessInterface {

  /**
   * {@inheritdoc}
   */
  public function access(Route $route) {
    if (\Drupal::moduleHandler()->moduleExists('contextual') && \Drupal::moduleHandler()->moduleExists('field_ui')) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

}
