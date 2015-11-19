<?php

/**
 * @file
 * Contains \Drupal\field_ui\Tests\ManageFieldsTest.
 */

namespace Drupal\ds\Tests;

use Drupal\Core\Language\LanguageInterface;
use Drupal\field\Tests\EntityReference\EntityReferenceTestTrait;
use Drupal\field_ui\Tests\FieldUiTestTrait;
use Drupal\simpletest\WebTestBase;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\Tests\TaxonomyTestTrait;

/**
 * Base test for Display Suite.
 *
 * @group ds
 */
abstract class FastTestBase extends WebTestBase {

  use DsTestTrait;
  use EntityReferenceTestTrait;
  use FieldUiTestTrait;
  use TaxonomyTestTrait;

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('node', 'field_ui', 'taxonomy', 'block', 'ds', 'ds_test', 'layout_plugin');

  /**
   * The label for a random field to be created for testing.
   *
   * @var string
   */
  protected $fieldLabel;

  /**
   * The input name of a random field to be created for testing.
   *
   * @var string
   */
  protected $fieldNameInput;

  /**
   * The name of a random field to be created for testing.
   *
   * @var string
   */
  protected $fieldName;

  /**
   * The created taxonomy vocabulary.
   *
   * @var \Drupal\taxonomy\Entity\Vocabulary
   */
  protected $vocabulary;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->drupalPlaceBlock('system_breadcrumb_block');

    // Create a test user.
    $admin_user = $this->drupalCreateUser(array(
      'access content',
      'admin classes',
      'admin display suite',
      'admin fields',
      'administer content types',
      'administer node fields',
      'administer node form display',
      'administer node display',
      'administer taxonomy',
      'administer taxonomy_term fields',
      'administer taxonomy_term display',
      'administer users',
      'administer permissions',
      'administer account settings',
      'administer user display',
      'administer software updates',
      'access site in maintenance mode',
      'administer site configuration',
      'bypass node access'
    ));
    $this->drupalLogin($admin_user);

    // Create random field name.
    $this->fieldLabel = $this->randomMachineName(8);
    $this->fieldNameInput = strtolower($this->randomMachineName(8));
    $this->fieldName = 'field_' . $this->fieldNameInput;

    // Create Article node type.
    $this->drupalCreateContentType(array('type' => 'article', 'name' => 'Article'));
    $this->drupalCreateContentType(array('type' => 'page', 'name' => 'Page'));

    // Create a vocabulary named "Tags".
    $this->vocabulary = Vocabulary::create(array(
      'name' => 'Tags',
      'vid' => 'tags',
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
    ));
    $this->vocabulary->save();

    $handler_settings = array(
      'target_bundles' => array(
        $this->vocabulary->id() => $this->vocabulary->id(),
      ),
    );
    $this->createEntityReferenceField('node', 'article', 'field_' . $this->vocabulary->id(), 'Tags', 'taxonomy_term', 'default', $handler_settings);

    entity_get_form_display('node', 'article', 'default')
      ->setComponent('field_' . $this->vocabulary->id())
      ->save();
  }

}
