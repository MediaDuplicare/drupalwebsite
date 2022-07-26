<?php

namespace Drupal\duplicare_content_mapper\Services\Traits;

use Drupal\paragraphs\Entity\Paragraph;

trait Paragraphs
{
  public function mapParagraphs($node)
  {
    $mapped_paragraphs = [];
    $paragraphs = $node->get('field_content')->referencedEntities();
    foreach ($paragraphs as $index => $paragraph) {
      $type = $paragraph->getType();

      switch ($type) {
        case 'text':
          $mapped_paragraphs[] = [
            'type' => 'text',
            'value' => $this->_parseTextParagraph($paragraph)
          ];
          break;

        case 'cta_button':
          $mapped_paragraphs[] = [
            'type' => 'cta_button',
            'value' => $this->_parseCtaParagraph($paragraph)
          ];
          break;

        case 'cta_button_image':
          $mapped_paragraphs[] = [
            'type' => 'cta_button_image',
            'value' => $this->_parseCtaParagraph($paragraph)
          ];
          break;

        case 'tab_item':
          $mapped_paragraphs[] = [
            'type' => 'tabs',
            'value' => $this->_parseTabsParagraph($paragraph)
          ];
          break;

        case 'slider':
          $mapped_paragraphs[] = [
            'type' => 'slider',
            'value' => $this->_parseSliderParagraph($paragraph)
          ];
          break;
        case 'column_text':
          $mapped_paragraphs[] = [
            'type' => 'column_text',
            'value' => $this->_parseColumnParagraph($paragraph)
          ];
          break;
        case 'image_subtext':
          $mapped_paragraphs[] = [
            'type' => 'image_caption',
            'value' => $this->_parseImageCaptionParagraph($paragraph)
          ];
          break;
        case 'intro_text':
          $mapped_paragraphs[] = [
            'type' => 'intro',
            'value' => $this->_parseIntroParagraph($paragraph)
          ];
          break;

        case 'image_and_text':
          $mapped_paragraphs[] = [
            'type' => 'image_and_text',
            'value' => $this->_parseCtaParagraph($paragraph)
          ];
          break;

        case 'timeline_item':
          $mapped_paragraphs[] = [
            'type' => 'timeline_item',
            'value' => $this->_parseTimeline($paragraph)
          ];
          break;

        case 'form':
          $mapped_paragraphs[] = [
            'type' => 'form',
            'value' => $this->_parseForm($paragraph)
          ];
          break;

        case 'clickable_blocks':
          $mapped_paragraphs[] = [
            'type' => 'clickable_blocks',
            'value' => $this->_parseClickableBlocksParagraph($paragraph)
          ];
          break;

        case 'faqs':
          $mapped_paragraphs[] = [
            'type' => 'faqs',
            'value' => $this->_parseFAQsParagraph($paragraph)
          ];
          break;
      }
    }

    return $mapped_paragraphs;
  }

  private function _parseTextParagraph($paragraph)
  {
    $paragraph = $this->_getEntityTranslation($paragraph);
    $text = [];

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_title')) {
      $text['title'] = $paragraph->field_title->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_content')) {
      $text['content'] = $paragraph->field_content->getValue()[0]['value'];
    }

    return $text;
  }

  private function _parseCtaParagraph($paragraph)
  {
    $paragraph = $this->_getEntityTranslation($paragraph);
    $cta = [];

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_title')) {
      $cta['title'] = $paragraph->field_title->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_content')) {
      $cta['description'] = $paragraph->field_content->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_link')) {
      $cta['link'] = $this->_parseLink($paragraph->field_link);
    }

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_new_tab')) {
      $cta['nTab'] = $this->_parseNewTab($paragraph->field_new_tab->getValue()[0]['value']);
    }

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_media_image')) {
      $cta['media']['type'] = 'image';
      $cta['media']['image'] = $this->_getImageStyleFromMedia($paragraph->field_media_image->getValue()[0]['target_id'], 'ratio_1_1_large');
    }
    return $cta;
  }

  private function _parseColumnParagraph($paragraph)
  {
    $paragraph = $this->_getEntityTranslation($paragraph);
    $column = [];

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_title')) {
      $column['title'] = $paragraph->field_title->getValue()[0]['value'];
    }
    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_intro')) {
      $column['intro'] = $paragraph->field_intro->getValue()[0]['value'];
    }
    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_content')) {
      $column['description'] = $paragraph->field_content->getValue()[0]['value'];
    }

    return $column;
  }

  private function _parseTwoImagesParagraph($paragraph)
  {
    $header = [];
    $images = $paragraph->get('field_two_images');
    foreach ($images as $item) {
      array_push($header, $this->_getImageStyleFromMedia($item->getValue()['target_id']));
    }
    return $header;
  }

  private function _parseImageCaptionParagraph($paragraph)
  {
    $paragraph = $this->_getEntityTranslation($paragraph);
    $image = [];
    $image['type'] = 'image';

    $image['image'] = $this->_getImageStyleFromMedia($paragraph->field_media_image->getValue()[0]['target_id']);

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_image_description')) {
      $image['caption'] = $paragraph->field_image_description->getValue()[0]['value'];
    }

    return $image;
  }

  private function _parseIntroParagraph($paragraph)
  {
    $paragraph = $this->_getEntityTranslation($paragraph);
    $intro = [];

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_title')) {
      $intro['title'] = $paragraph->field_title->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_small_text')) {
      $intro['intro'] = $paragraph->field_small_text->getValue()[0]['value'];
    }

    return $intro;
  }

  private function _parseTimeline($paragraph)
  {
    $paragraph = $this->_getEntityTranslation($paragraph);
    $timeline = [];

    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_title')) {
      $timeline['title'] = $paragraph->field_title->getValue()[0]['value'];
    }
    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_content_extra')) {
      $timeline['description'] = $paragraph->field_content_extra->getValue()[0]['value'];
    }
    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_date')) {
      $timeline['date'] = $paragraph->field_date->getValue()[0]['value'];
    }
    if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_media_image')) {
      $timeline['media']['type'] = 'image';
      $timeline['media']['image'] = $this->_getImageStyleFromMedia($paragraph->field_media_image->getValue()[0]['target_id']);
    }

    return $timeline;
  }

  private function _parseForm($paragraph)
  {
    if(!$this->checkFieldExistsAndNotEmpty($paragraph, 'field_form')) {
      return [];
    }

    $webform = \Drupal::entityTypeManager()->getStorage('webform')->load($paragraph->field_form->getValue()[0]['target_id']);
    $webform = $webform->getSubmissionForm();

    return [
      'form' => $webform,
    ];
  }

  private function _parseFAQsParagraph($paragraph) {
    $translatedParagraph = $this->_getEntityTranslation($paragraph);

    $data = [];
    $data['cards'] = [];

    if ($this->checkFieldExistsAndNotEmpty($translatedParagraph, 'field_title')) {
      $data['title'] = $translatedParagraph->field_title->value;
    }

    if ($this->checkFieldExistsAndNotEmpty($translatedParagraph, 'field_subtitle')) {
      $data['subtitle'] = $translatedParagraph->field_subtitle->value;
    }

    if ($this->checkFieldExistsAndNotEmpty($translatedParagraph, 'field_cards')) {
      for ($x = 0; $x < count($translatedParagraph->field_cards); $x++){
        $paragraph = Paragraph::load($translatedParagraph->field_cards->getValue()[$x]['target_id']);
        $paragraph = $this->_getEntityTranslation($paragraph);

        $card = [];
        $card['title'] = $paragraph->field_title->getValue()[0]['value'];
        $card['description'] = $paragraph->field_small_text->getValue()[0]['value'];

        if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_icons')){
          $card['icon'] = $paragraph->field_icons->getValue()[0]['value'];
        } else {
          $card['icon'] = false;
        }

        if ($this->checkFieldExistsAndNotEmpty($paragraph, 'field_link')) {
          $card['link'] = $this->_parseLink($paragraph->field_link);
        }
        array_push($data['cards'], $card);
      }
    }

    if($this->checkFieldExistsAndNotEmpty($translatedParagraph, 'field_form')) {
      $webform = \Drupal::entityTypeManager()->getStorage('webform')->load($translatedParagraph->field_form->getValue()[0]['target_id']);
      $data['form'] = $webform->getSubmissionForm();
    }

    return $data;
  }
}
