<?php

/**
 * @file
 * Contains \Drupal\ds_devel\Controller\DsDevelController.
 */

namespace Drupal\ds_devel\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * Returns responses for Views UI routes.
 */
class DsDevelController {

  /**
   * Lists all instances of fields on any views.
   *
   * @return array
   *   The Views fields report page.
   */
  public function nodeMarkup($node, $key = 'default') {
    $node = Node::load($node);

    $builded_entity = entity_view($node, $key);
    $markup = drupal_render($builded_entity);

    $links = array();
    $links['default'] = array(
      'title' => 'Default',
      'url' => Url::fromRoute('ds_devel.markup', array('node' => $node->id())),
    );
    $view_modes = \Drupal::entityManager()->getViewModes('node');
    foreach ($view_modes as $id => $info) {
      if (!empty($info['status'])) {
        $links[] = array(
          'title' => $info['label'],
          'url' => Url::fromRoute('ds_devel.markup_view_mode', array('node' => $node->id(), 'key' => $id)),
        );
      }
    }

    $build['links'] = array(
      '#theme' => 'links',
      '#links' => $links,
      '#prefix' => '<div>',
      '#suffix' => '</div><hr />',
    );
    $build['markup'] = [
      '#markup' => '<code><pre>' . Html::escape($markup) . '</pre></code>',
      '#allowed_tags' => ['code', 'pre'],
    ];

    return $build;
  }

}
