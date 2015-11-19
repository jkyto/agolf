<?php

/**
 * @file
 * Contains Drupal\ds\Tests\FieldTemplateTest.
 */

namespace Drupal\ds\Tests;

use Drupal\Core\Cache\Cache;

/**
 * Tests for display of nodes and fields.
 *
 * @group ds
 */
class FieldTemplateTest extends FastTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setup() {
    parent::setup();

    // Enable field templates
    \Drupal::configFactory()->getEditable('ds.settings')
      ->set('field_template', TRUE)
      ->save();
  }

  /**
   * Tests on field templates.
   */
  function testDSFieldTemplate() {
    // Get a node.
    $node = $this->entitiesTestSetup('hidden');
    $body_field = $node->body->value;

    // -------------------------
    // Default theming function.
    // -------------------------
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"field field-node--body field-name-body field-type-text-with-summary field-label-hidden\">");
    $this->entitiesSetLabelClass('above', 'body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"field field-node--body field-name-body field-type-text-with-summary field-label-above\">
      <div class=\"field-label\">Body</div>
    <div class=\"field-items\">
          <div class=\"field-item\"><p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('above', 'body', 'My body');
    // @todo ==> WTF WHY DO WE NEED TO CLEAR THE CACHES HERE, AND WHY NOT FOR THE NEXT entitiesSetLabelClass()
    // @todo ==> Render caching? It works on my dev machine
    drupal_flush_all_caches();

    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"field field-node--body field-name-body field-type-text-with-summary field-label-above\">
      <div class=\"field-label\">My body</div>
    <div class=\"field-items\">
          <div class=\"field-item\"><p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('hidden', 'body', '', 'test_field_class');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"test_field_class field field-node--body field-name-body field-type-text-with-summary field-label-hidden\">
    <div class=\"field-items\">
          <div class=\"field-item\"><p>" . $body_field . "</p>");
  }

  /**
   * Tests on field templates.
   */
  function testDSFieldTemplate2() {
    // Get a node.
    $node = $this->entitiesTestSetup('hidden');
    $body_field = $node->body->value;

    // -----------------------
    // Reset theming function.
    // -----------------------
    $edit = array(
      'fs1[ft-default]' => 'reset',
    );
    $this->drupalPostForm('admin/structure/ds/settings', $edit, t('Save configuration'));
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <p>" . $body_field);

    $this->entitiesSetLabelClass('above', 'body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"field-label-above\">Body</div><p>" . $body_field);

    $this->entitiesSetLabelClass('inline', 'body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"field-label-inline\">Body</div><p>" . $body_field);

    $this->entitiesSetLabelClass('above', 'body', 'My body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"field-label-above\">My body</div><p>" . $body_field);

    $this->entitiesSetLabelClass('inline', 'body', 'My body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"field-label-inline\">My body</div><p>" . $body_field);

    $edit = array(
      'fs1[ft-show-colon]' => 'reset',
    );
    $this->drupalPostForm('admin/structure/ds/settings', $edit, t('Save configuration'));
    $tags = $node->getCacheTags();
    Cache::invalidateTags($tags);

    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"field-label-inline\">My body:</div><p>" . $body_field);

    $this->entitiesSetLabelClass('hidden', 'body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <p>" . $body_field);
  }

  /**
   * Tests on field templates.
   */
  function testDSFieldTemplate3() {
    // Get a node.
    $node = $this->entitiesTestSetup('hidden');
    $body_field = $node->body->value;

    // ----------------------
    // Custom field function.
    // ----------------------

    // With outer wrapper.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][id]' => 'expert',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'div',
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();

    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div><p>" . $body_field . "</p>");

    // With outer div wrapper and class.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-cl]' => 'ow-class'
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class\"><p>" . $body_field . "</p>");

    // With outer span wrapper and class.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'span',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-cl]' => 'ow-class-2'
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <span class=\"ow-class-2\"><p>" . $body_field . "</p>");
  }

  /**
   * Tests on field templates.
   */
  function testDSFieldTemplate4() {

    // Get a node.
    $node = $this->entitiesTestSetup('hidden');
    $body_field = $node->body->value;

    // With outer wrapper and field items wrapper.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][id]' => 'expert',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-el]' => 'div'
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div><div><p>" . $body_field . "</p>");

    // With outer wrapper and field items div wrapper with class.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-cl]' => 'fi-class'
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div><div class=\"fi-class\"><p>" . $body_field . "</p>");

    // With outer wrapper and field items span wrapper and class.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-el]' => 'span',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-cl]' => 'fi-class'
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div><span class=\"fi-class\"><p>" . $body_field . "</p>");

    // With outer wrapper class and field items span wrapper and class.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-el]' => 'span',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-cl]' => 'fi-class'
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class\"><span class=\"fi-class\"><p>" . $body_field . "</p>");

    // With outer wrapper span class and field items span wrapper and class.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'span',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-el]' => 'span',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-cl]' => 'fi-class-2'
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <span class=\"ow-class\"><span class=\"fi-class-2\"><p>" . $body_field . "</p>");
  }

  /**
   * Tests on field templates.
   */
  function testDSFieldTemplate5() {
    // Get a node.
    $node = $this->entitiesTestSetup('hidden');
    $body_field = $node->body->value;

    // With field item div wrapper.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][id]' => 'expert',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi]' => '1',
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div><p>" . $body_field . "</p>");

    // With field item span wrapper.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-el]' => 'span',
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <span><p>" . $body_field . "</p>");

    // With field item span wrapper and class.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-el]' => 'span',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-cl]' => 'fi-class',
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <span class=\"fi-class\"><p>" . $body_field . "</p>");

    // With fis and fi.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-cl]' => 'fi-class-2',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-cl]' => 'fi-class',
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"fi-class-2\"><div class=\"fi-class\"><p>" . $body_field . "</p>");
    // With all wrappers.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-cl]' => 'fi-class-2',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-el]' => 'span',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-cl]' => 'fi-class',
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class\"><div class=\"fi-class-2\"><span class=\"fi-class\"><p>" . $body_field . "</p>");

    // With all wrappers and attributes.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-at]' => 'name="ow-att"',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-cl]' => 'fi-class-2',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-at]' => 'name="fis-att"',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-el]' => 'span',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-cl]' => 'fi-class',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-at]' => 'name="fi-at"',
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class\" name=\"ow-att\"><div class=\"fi-class-2\" name=\"fis-att\"><span class=\"fi-class\" name=\"fi-at\"><p>" . $body_field . "</p>");

    // Remove attributes.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-at]' => '',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-cl]' => 'fi-class-2',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fis-at]' => '',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-el]' => 'span',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-cl]' => 'fi-class',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-at]' => '',
    );
    $this->dsEditFormatterSettings($edit);

    // Label tests with custom function.
    $this->entitiesSetLabelClass('above', 'body');
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class\"><div class=\"field-label-above\">Body</div><div class=\"fi-class-2\"><span class=\"fi-class\"><p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('inline', 'body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class\"><div class=\"field-label-inline\">Body</div><div class=\"fi-class-2\"><span class=\"fi-class\"><p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('above', 'body', 'My body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class\"><div class=\"field-label-above\">My body</div><div class=\"fi-class-2\"><span class=\"fi-class\"><p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('inline', 'body', 'My body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class\"><div class=\"field-label-inline\">My body</div><div class=\"fi-class-2\"><span class=\"fi-class\"><p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('inline', 'body', 'My body', '', TRUE);
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class\"><div class=\"field-label-inline\">My body:</div><div class=\"fi-class-2\"><span class=\"fi-class\"><p>" . $body_field . "</p>");

    $this->entitiesSetLabelClass('hidden', 'body');
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class\"><div class=\"fi-class-2\"><span class=\"fi-class\"><p>" . $body_field . "</p>");

    // Test default classes on outer wrapper.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-def-cl]' => '1',
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class field-node--body field-name-body field-type-text-with-summary field-label-hidden\"><div class=\"fi-class-2\"><span class=\"fi-class\"><p>" . $body_field . "</p>");

    // Test default attributes on field item.
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-el]' => 'div',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-cl]' => 'ow-class',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][ow-def-cl]' => '1',
      'fields[body][settings_edit_form][third_party_settings][ds][ft][settings][fi-def-at]' => '1',
    );
    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          <div class=\"ow-class field-node--body field-name-body field-type-text-with-summary field-label-hidden\"><div class=\"fi-class-2\"><span class=\"fi-class\"><p>" . $body_field . "</p>");

    // Use the test field theming function to test that this function is
    // registered in the theme registry through ds_extras_theme().
    $edit = array(
      'fields[body][settings_edit_form][third_party_settings][ds][ft][id]' => 'ds_test_theming_function',
    );

    $this->dsEditFormatterSettings($edit);
    drupal_flush_all_caches();
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw("<div class=\"group-right\">
          Testing field output through custom function
      </div>");
  }
}
