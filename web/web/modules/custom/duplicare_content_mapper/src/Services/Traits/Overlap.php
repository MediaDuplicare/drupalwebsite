<?php

namespace Drupal\duplicare_content_mapper\Services\Traits;

use Drupal\paragraphs\Entity\Paragraph;

trait Overlap
{
  private function _parseTabsParagraph($paragraph)
  {
    $paragraph = $this->_getEntityTranslation($paragraph);
    $tabsSection = [];
    $tabs = [];
    foreach ($paragraph->field_item as $item) {
      array_push($tabs, $this->_parseTabItems(\Drupal\paragraphs\Entity\Paragraph::load($item->getValue()['target_id'])));
    }

    $tabsSection['title'] = $paragraph->field_title->getValue()[0]['value'];
    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_small_text')) {
      $tabsSection['description'] = $paragraph->field_small_text->getValue()[0]['value'];
    }
    $tabsSection['tabs'] = $tabs;

    return $tabsSection;
  }

  private function _parseTabItems($tab): array
  {
    $tab = $this->_getEntityTranslation($tab);
    $item = [];
    if ($this->checkFieldExistsAndNotEmpty($tab, 'field_title')) {
      $item['title'] = $tab->field_title->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($tab, 'field_content')) {
      $item['description'] = $tab->field_content->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($tab, 'field_media_image')) {
      $item['media']['image'] = $this->_getImageStyleFromMedia($tab->field_media_image->getValue()[0]['target_id']);
      $item['media']['type'] = "image";
    }

    if ($this->checkFieldExistsAndNotEmpty($tab, 'field_link')) {
      $item['link'] = $this->_parseLink($tab->field_link);
    };

    if ($this->checkFieldExistsAndNotEmpty($tab, 'field_new_tab')) {
      $item['nTab'] = $this->_parseNewTab($tab->field_new_tab);
    }
    return $item;
  }

  private function _parseSliderParagraph($paragraph)
  {
    $paragraph = $this->_getEntityTranslation($paragraph);
    $slider = [];
    $data = [];
    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_title')) {
      $slider['title'] = $paragraph->field_title->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_caption')) {
      $caption = $paragraph->field_caption->getValue()[0]['value'];
    }
    $images = $paragraph->get('field_media_images');
    foreach ($images as $item) {
      if (isset($caption)) {
        array_push($data, ['image' => $this->_getImageStyleFromMedia($item->getValue()['target_id']), 'type' => 'image', 'caption' => $caption]);
      } else {
        array_push($data, ['image' => $this->_getImageStyleFromMedia($item->getValue()['target_id'], 'slider_image'), 'type' => 'image']);
      }

    }
    $slider['slider'] = $data;
    return $slider;
  }

  private function _parseClickableBlocksParagraph($paragraph) {
    $paragraph = $this->_getEntityTranslation($paragraph);

    $blocks = [];

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_title')) {
      $blocks['title'] = $paragraph->field_title->value;
    }

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_clickable_blocks')) {
      $blocks['blocks'] = $this->_parseClickableBlocks($paragraph->field_clickable_blocks);
    }

    return $blocks;
  }

  private function _parseClickableBlocks($blocks) {
    $data = [];

    foreach ($blocks as $key => $item) {
      $block = [];

      $paragraph = Paragraph::load($item->target_id);
      $node = $this->_getEntityTranslation($paragraph);

      if ($this->checkFieldExistsAndNotEmpty($node, 'field_title')) {
        $block['title'] = $node->field_title->value;
      }
      if ($this->checkFieldExistsAndNotEmpty($node, 'field_small_text')) {
        $block['description'] = $node->field_small_text->value;
      }
      if ($this->checkFieldExistsAndNotEmpty($node, 'field_link')) {
        $block['link'] = $this->_parseLink($node->field_link);
      }
      if ($this->checkFieldExistsAndNotEmpty($node, 'field_media_image')) {
        $block['image'] = $this->_getImageStyleFromMedia($node->field_media_image->target_id);
      }
      if ($this->checkFieldExistsAndNotEmpty($node, 'field_new_tab')) {
        $block['nTab'] = $this->_parseNewTab($node->field_new_tab->getValue()[0]['value']);
      }

      array_push($data, $block);
    }

    return $data;
  }
}
