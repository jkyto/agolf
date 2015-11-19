<?php

/**
 * @file
 * Contains Drupal\ds_extras\EventSubscriber\RouteSubscriber.
 */

namespace Drupal\ds_extras\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\Core\Routing\RouteBuildEvent;

/**
 * Alter the node view route
 */
class RouteSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[RoutingEvents::ALTER][] = array('alterRoutes', 100);
    return $events;
  }

  /**
   * Alters the routes.
   *
   * @param \Drupal\Core\Routing\RouteBuildEvent $event
   *   The event to process.
   */
  public function alterRoutes(RouteBuildEvent $event) {
    if (\Drupal::config('ds.extras')->get('override_node_revision')) {
      $route = $event->getRouteCollection()->get('node.revision_show');
      if (!empty($route)) {
        $route->setDefault('_controller', '\Drupal\ds_extras\Controller\DsExtrasController::revisionShow');
      }
    }
  }

}
