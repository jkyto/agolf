<?php

/**
 * @file
 * Contains \Drupal\agolf_base\Plugin\Field\FieldFormatter\TextLinkFormatter.
 */

namespace Drupal\agolf_base\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'text_link_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "text_link_formatter",
 *   label = @Translation("Text link"),
 *   field_types = {
 *     "string"
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class TextLinkFormatter extends FormatterBase {

    /**
     * {@inheritdoc}
     */
    public function viewElements(FieldItemListInterface $items, $langcode) {
        $elements = array();
        foreach ($items as $delta => $item) {
            $entity = $items->getEntity();
            $elements[$delta] = array(
                '#type' => 'processed_text',
                '#text' => $item->value,
                '#format' => $item->format,
                '#langcode' => $item->getLangcode(),
                '#theme' => 'text_link_formatter',
                '#item' => $item,
                '#url' => $entity->urlInfo()
            );
        }
        return $elements;
    }

}
