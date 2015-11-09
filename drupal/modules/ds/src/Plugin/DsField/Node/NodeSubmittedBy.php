<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\Node\NodeSubmittedBy.
 */

namespace Drupal\ds\Plugin\DsField\Node;

use Drupal\ds\Plugin\DsField\Date;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;

/**
 * Plugin that renders the submitted by field.
 *
 * @DsField(
 *   id = "node_submitted_by",
 *   title = @Translation("Submitted by"),
 *   entity_type = "node",
 *   provider = "node"
 * )
 */
class NodeSubmittedBy extends Date {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $field = $this->getFieldConfiguration();

    /** @var $node NodeInterface */
    $node = $this->entity();

    /** @var $account UserInterface */
    $account = $node->getOwner();

    switch ($field['formatter']) {
      default:
        $date_format = str_replace('ds_post_date_', '', $field['formatter']);
        $user_name = array(
          '#theme' => 'username',
          '#account' => $account,
        );
        return array(
          '#markup' => t('Submitted by !user on !date.', array('!user' => drupal_render($user_name), '!date' => format_date($this->entity()->created->value, $date_format))),
          '#cache' => array(
            'tags' => $account->getCacheTags()
          ),
        );
    }
  }

}
