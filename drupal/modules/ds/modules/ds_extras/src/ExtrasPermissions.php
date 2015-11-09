<?php

/**
 * @file
 * Contains \Drupal\field_ui\FieldUiPermissions.
 */

namespace Drupal\ds_extras;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ds\Ds;

/**
 * Provides dynamic permissions of the ds extras module.
 */
class extrasPermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new FieldUiPermissions instance.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.manager'));
  }

  /**
   * Returns an array of ds extras permissions.
   *
   * @return array
   */
  public function extrasPermissions() {
    $permissions = [];

    // @todo inject config
    if (\Drupal::config('ds.extras')->get('switch_view_mode')) {
      foreach (node_type_get_names() as $key => $name) {
        $permissions['ds_switch ' . $key] = array(
          'title' => t('Switch view modes on :type', array(':type' => $name))
        );
      }
    }

    if (\Drupal::config('ds.extras')->get('field_permissions')) {
      $entities = $this->entityManager->getDefinitions();
      foreach ($entities as $entity_type => $info) {
        // @todo do this on all fields ?
        // @todo hide switch field if enabled
        $fields = Ds::getFields($entity_type);
        foreach ($fields as $key => $finfo) {
          $permissions['view ' . $key . ' on ' . $entity_type] = array(
            'title' => t('View !field on !entity_type', array('!field' => $finfo['title'], '!entity_type' => $info->getLabel())),
          );
        }
      }
    }

    return $permissions;
  }

}
