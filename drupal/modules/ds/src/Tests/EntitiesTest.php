<?php

/**
 * @file
 * Contains Drupal\ds\Tests\EntitiesTest.
 */

namespace Drupal\ds\Tests;

use Drupal\Component\Utility\Html;

/**
 * Tests for display of nodes and fields.
 *
 * @group ds
 */
class EntitiesTest extends FastTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setup() {
    parent::setup();

    // Enable field templates
    \Drupal::configFactory()->getEditable('ds.settings')
      ->set('field_template', TRUE)
      ->save();

    // Enable switch view mode
    \Drupal::configFactory()->getEditable('ds.extras')
      ->set('switch_view_mode', TRUE)
      ->save();

    // Run update.php to add the ds_switch field.
    $url = $GLOBALS['base_url'] . '/update.php';
    $this->drupalGet($url, array('external' => TRUE));
    $this->clickLink(t('Continue'));
    $this->clickLink(t('Apply pending updates'));

    // Turn off maintenance mode.
    $edit = array(
      'maintenance_mode' => FALSE,
    );
    $this->drupalPostForm('admin/config/development/maintenance', $edit, t('Save configuration'));
  }

  /**
   * Test basic node display fields.
   */
  function testDSNodeEntity() {

    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->entitiesTestSetup();

    // Look at node and verify token and block field.
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('view-mode-full', 'Template file found (in full view mode)');
    $this->assertRaw('<div class="field-item">' . $node->getTitle() . '</div>', t('Token field found'));
    $this->assertRaw('group-header', 'Template found (region header)');
    $this->assertRaw('group-footer', 'Template found (region footer)');
    $this->assertRaw('group-left', 'Template found (region left)');
    $this->assertRaw('group-right', 'Template found (region right)');
    $this->assertPattern('/<div[^>]*>Submitted[^<]*<a[^>]+href="' . preg_quote(base_path(), '/') . 'user\/' . $node->getOwnerId() . '"[^>]*>' . Html::escape($node->getOwner()->getUsername()) . '<\/a>.*<\/div>/', t('Submitted by line found'));

    // Configure teaser layout.
    $teaser = array(
      'layout' => 'ds_2col',
    );
    $teaser_assert = array(
      'regions' => array(
        'left' => '<td colspan="8">' . t('Left') . '</td>',
        'right' => '<td colspan="8">' . t('Right') . '</td>',
      ),
    );
    $this->dsSelectLayout($teaser, $teaser_assert, 'admin/structure/types/manage/article/display/teaser');

    $fields = array(
      'fields[dynamic_token_field:node-token_field][region]' => 'left',
      'fields[body][region]' => 'right',
      'fields[node_links][region]' => 'right',
    );
    $this->dsConfigureUI($fields, 'admin/structure/types/manage/article/display/teaser');

    // Switch view mode on full node page.
    $edit = array('ds_switch' => 'teaser');
    $this->drupalPostForm('node/' . $node->id() . '/edit', $edit, t('Save and keep published'));
    $this->assertRaw('view-mode-teaser', 'Switched to teaser mode');
    $this->assertRaw('group-left', 'Template found (region left)');
    $this->assertRaw('group-right', 'Template found (region right)');
    $this->assertNoRaw('group-header', 'Template found (no region header)');
    $this->assertNoRaw('group-footer', 'Template found (no region footer)');

    $edit = array('ds_switch' => '');
    $this->drupalPostForm('node/' . $node->id() . '/edit', $edit, t('Save and keep published'));
    $this->assertRaw('view-mode-full', 'Switched to full mode again');

    // Test all options of a block field.
    $block = array(
      'name' => 'Test block field',
    );
    $this->dsCreateBlockField($block);
    $fields = array(
      'fields[dynamic_block_field:node-test_block_field][region]' => 'left',
      'fields[dynamic_token_field:node-token_field][region]' => 'hidden',
      'fields[body][region]' => 'hidden',
      'fields[node_links][region]' => 'hidden',
    );
    $this->dsConfigureUI($fields);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('view-id-content_recent');

    /*
    $block = array(
      'block_render' => DS_BLOCK_TITLE_CONTENT,
    );
    $this->dsCreateBlockField($block, 'admin/structure/ds/fields/manage_block/test_block_field', FALSE);
    $this->drupalGet('node/' . $node->id());
    $this->assertNoRaw('<h2>Recent content</h2>');
    $this->assertRaw('Recent content');

    $block = array(
      'block_render' => DS_BLOCK_CONTENT,
    );
    $this->dsCreateBlockField($block, 'admin/structure/ds/fields/manage_block/test_block_field', FALSE);
    $this->drupalGet('node/' . $node->id());
    $this->assertNoRaw('<h2>Recent content</h2>');
    $this->assertNoRaw('Recent content');
    */

    // Test revisions. Enable the revision view mode
    $edit = array(
      'display_modes_custom[revision]' => '1'
    );
    $this->drupalPostForm('admin/structure/types/manage/article/display', $edit, t('Save'));

    // Enable the override revision mode and configure it
    $edit = array(
      'fs3[override_node_revision]' => TRUE,
      'fs3[override_node_revision_view_mode]' => 'revision'
    );
    $this->drupalPostForm('admin/structure/ds/settings', $edit, t('Save configuration'));

    // Select layout and configure fields.
    $edit = array(
      'layout' => 'ds_2col',
    );
    $assert = array(
      'regions' => array(
        'left' => '<td colspan="8">' . t('Left') . '</td>',
        'right' => '<td colspan="8">' . t('Right') . '</td>',
      ),
    );
    $this->dsSelectLayout($edit, $assert, 'admin/structure/types/manage/article/display/revision');
    $edit = array(
      'fields[body][region]' => 'left',
      'fields[node_link][region]' => 'right',
      'fields[node_author][region]' => 'right',
    );
    $this->dsConfigureUI($edit, 'admin/structure/types/manage/article/display/revision');

    // Create revision of the node.
    $edit = array(
      'revision' => TRUE,
      'revision_log[0][value]' => 'Test revision',
    );
    $this->drupalPostForm('node/' . $node->id() . '/edit', $edit, t('Save and keep published'));
    $this->assertText('Revisions');

    // Assert revision is using 2 col template.
    $this->drupalGet('node/' . $node->id() . '/revisions/1/view');
    $this->assertText('Body', 'Body label');

    // Assert full view is using stacked template.
    $this->drupalGet('node/' . $node->id());
    $this->assertNoText('Body', 'No Body label');

    // Test formatter limit on article with tags.
    $edit = array(
      'ds_switch' => '',
      'field_tags[target_id]' => 'Tag 1, Tag 2'
    );
    $this->drupalPostForm('node/' . $node->id() . '/edit', $edit, t('Save and keep published'));
    $edit = array(
      'fields[field_tags][region]' => 'right',
    );
    $this->dsConfigureUI($edit, 'admin/structure/types/manage/article/display');
    $this->drupalGet('node/' . $node->id());
    $this->assertText('Tag 1');
    $this->assertText('Tag 2');
    $edit = array(
      'fields[field_tags][settings_edit_form][third_party_settings][ds][ds_limit]' => '1',
    );
    $this->dsEditLimitSettings($edit, 'field_tags');
    $this->drupalGet('node/' . $node->id());
    $this->assertText('Tag 1');
    $this->assertNoText('Tag 2');

    // Test \Drupal\Component\Utility\Html::escape() on ds_render_field() with the title field.
    $edit = array(
      'fields[node_title][region]' => 'right',
    );
    $this->dsConfigureUI($edit, 'admin/structure/types/manage/article/display');
    $edit = array(
      'title[0][value]' => 'Hi, I am an article <script>alert(\'with a javascript tag in the title\');</script>',
    );
    $this->drupalPostForm('node/' . $node->id() . '/edit', $edit, t('Save'));
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('<h2>Hi, I am an article &lt;script&gt;alert(&#039;with a javascript tag in the title&#039;);&lt;/script&gt;</h2>');
  }

}
