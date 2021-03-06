<?php

/**
 * @file
 * Contains \Drupal\entityqueue\Plugin\views\filter\EntityQueueInQueue.
 */

namespace Drupal\entityqueue\Plugin\views\filter;

use Drupal\Core\Session\AccountInterface;
use Drupal\entityqueue\Plugin\views\relationship\EntityQueueRelationship;
use Drupal\views\Plugin\views\filter\BooleanOperator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Filter for entities that are available or not in an entity queue.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("entity_queue_in_queue")
 */
class EntityQueueInQueue extends BooleanOperator {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();

    // Try to find an entity queue relationship in this view, and pick the first
    // one available.
    foreach ($this->view->relationship as $id => $relationship) {
      if ($relationship instanceof EntityQueueRelationship) {
        $this->options['relationship'] = $id;
        $this->setRelationship();

        break;
      }
    }

    if (isset($this->relationship) && ($subqueue_items_table_alias = $this->query->ensureTable($this->definition['field table'], $this->relationship))) {
      $field_field = $this->definition['field field'];
      $operator  = $this->value ? 'IS NOT NULL' : 'IS NULL';
      $condition = "$subqueue_items_table_alias.$field_field $operator";

      $this->query->addWhereExpression($this->options['group'], $condition);

      // Limit to a specific queue if the relationship specifies it.
      if (isset($relationship) && !empty($relationship->options['limit_queue'])) {
        $column = "$subqueue_items_table_alias.bundle";
        $this->query->addWhere($this->options['group'], $column, $relationship->options['limit_queue'], '=');
      }
    }
    else {
      if ($this->currentUser->hasPermission('administer views')) {
        drupal_set_message($this->t('In order to sort by the queue position, you need to add the Entityqueue: Queue relationship on View: @view with display: @display', ['@view' => $this->view->storage->label(), '@display' => $this->view->current_display]), 'error');
      }
    }
  }

}
