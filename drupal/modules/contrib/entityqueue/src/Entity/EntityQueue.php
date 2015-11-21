<?php

/**
 * @file
 * Contains \Drupal\entityqueue\Entity\EntityQueue.
 */

namespace Drupal\entityqueue\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\entityqueue\EntityQueueHandlerPluginCollection;
use Drupal\entityqueue\EntityQueueInterface;

/**
 * Defines the EntityQueue entity class.
 *
 * @ConfigEntityType(
 *   id = "entity_queue",
 *   label = @Translation("Entity queue"),
 *   handlers = {
 *     "list_builder" = "Drupal\entityqueue\EntityQueueListBuilder",
 *     "form" = {
 *       "add" = "Drupal\entityqueue\Form\EntityQueueForm",
 *       "edit" = "Drupal\entityqueue\Form\EntityQueueForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   admin_permission = "administer entityqueue",
 *   config_prefix = "entity_queue",
 *   bundle_of = "entity_subqueue",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "status" = "status"
 *   },
 *   links = {
 *     "edit-form" = "/admin/structure/entityqueue/{entity_queue}",
 *     "delete-form" = "/admin/structure/entityqueue/{entity_queue}/delete",
 *     "collection" = "/admin/structure/entityqueue",
 *     "enable" = "/admin/structure/entityqueue/{entity_queue}/enable",
 *     "disable" = "/admin/structure/entityqueue/{entity_queue}/disable",
 *     "subqueue-list" = "/admin/structure/entityqueue/{entity_queue}/list"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "target_type",
 *     "handler",
 *     "handler_configuration",
 *     "min_size",
 *     "max_size",
 *     "act_as_queue"
 *   }
 * )
 */
class EntityQueue extends ConfigEntityBundleBase implements EntityQueueInterface, EntityWithPluginCollectionInterface {

  /**
   * The EntityQueue ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The EntityQueue label.
   *
   * @var string
   */
  protected $label;

  /**
   * The type of the target entities.
   *
   * @var string
   */
  protected $target_type = '';

  /**
   * The minimum number of items that this queue can hold.
   *
   * @var int
   */
  protected $min_size = 0;

  /**
   * The maximum number of items that this queue can hold.
   *
   * @var int
   */
  protected $max_size = 0;

  /**
   * The behavior of exceeding the maximum number of queue items.
   *
   * @var bool
   */
  protected $act_as_queue = FALSE;

  /**
   * The ID of the EntityQueueHandler.
   *
   * @var string
   */
  protected $handler;

  /**
   * An array to store and load the EntityQueueHandler plugin configuration.
   *
   * @var array
   */
  protected $handler_configuration = [];

  /**
   * The EntityQueueHandler plugin.
   *
   * @var \Drupal\entityqueue\EntityQueueHandlerPluginCollection
   */
  protected $handlerPluginCollection;

  /**
   * {@inheritdoc}
   */
  public function getTargetEntityTypeId() {
    return $this->target_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getMinimumSize() {
    return $this->min_size;
  }

  /**
   * {@inheritdoc}
   */
  public function getMaximumSize() {
    return $this->max_size;
  }

  /**
   * {@inheritdoc}
   */
  public function getActAsQueue() {
    return $this->act_as_queue;
  }

  /**
   * {@inheritdoc}
   */
  public function getHandler() {
    return $this->handler;
  }

  /**
   * {@inheritdoc}
   */
  public function setHandler($handler) {
    $this->handler = $handler;
    $this->getPluginCollection()->addInstanceID($handler, []);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHandlerPlugin() {
    return $this->getPluginCollection()->get($this->handler);
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    return ['handler_configuration' => $this->getPluginCollection()];
  }

  /**
   * Encapsulates the creation of the EntityQueueHandlerPluginCollection.
   *
   * @return \Drupal\entityqueue\EntityQueueHandlerPluginCollection
   *   The entity queue's plugin collection.
   */
  protected function getPluginCollection() {
    if (!$this->handlerPluginCollection) {
      $this->handlerPluginCollection = new EntityQueueHandlerPluginCollection(
        \Drupal::service('plugin.manager.entityqueue.handler'),
        $this->handler, $this->handler_configuration, $this);
    }
    return $this->handlerPluginCollection;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    $this->getHandlerPlugin()->onQueuePreSave($this, $storage);
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    $this->getHandlerPlugin()->onQueuePostSave($this, $storage, $update);
  }

  /**
   * {@inheritdoc}
   */
  public static function preDelete(EntityStorageInterface $storage, array $entities) {
    parent::preDelete($storage, $entities);

    foreach ($entities as $queue) {
      $queue->getHandlerPlugin()->onQueuePreDelete($queue, $storage);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    foreach ($entities as $queue) {
      $queue->getHandlerPlugin()->onQueuePostDelete($queue, $storage);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postLoad(EntityStorageInterface $storage, array &$entities) {
    parent::postLoad($storage, $entities);

    foreach ($entities as $queue) {
      $queue->getHandlerPlugin()->onQueuePostLoad($queue, $storage);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function loadMultipleByTargetType($target_entity_type_id) {
    $ids = \Drupal::entityManager()->getStorage('entity_queue')->getQuery()
      ->condition('target_type', $target_entity_type_id)
      ->execute();

    return $ids ? static::loadMultiple($ids) : [];
  }

}
