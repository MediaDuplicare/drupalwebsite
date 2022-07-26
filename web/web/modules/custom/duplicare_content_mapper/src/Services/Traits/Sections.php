<?php

namespace Drupal\duplicare_content_mapper\Services\Traits;

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\image\Entity\ImageStyle;

trait Sections
{
  public function mapSections($node)
  {
    $mapped_sections = [];
    $sections = $node->get('field_sections')->referencedEntities();
    foreach ($sections as $index => $section) {
      $type = $section->getType();
      switch ($type) {
        case 'testimonial':
          $mapped_sections[] = [
            'type' => 'testimonial',
            'value' => $this->_parseTestimonialSection($section)
          ];
          break;

        case 'news':
          $mapped_sections[] = [
            'type' => 'news',
            'value' => $this->_parseNewsSection($section)
          ];
          break;

        case 'tabs':
          $mapped_sections[] = [
            'type' => 'tabs',
            'value' => $this->_parseTabsParagraph($section)
          ];
          break;

        case 'partners':
          $mapped_sections[] = [
            'type' => 'logos',
            'value' => $this->_parseLogosSection($section)
          ];
          break;

        case 'slider':
          $mapped_sections[] = [
            'type' => 'slider',
            'value' => $this->_parseSliderParagraph($section)
          ];
          break;

        case 'text_and_cards':
          $mapped_sections[] = [
            'type' => 'text_and_cards',
            'value' => $this->_parseTextAndCardsSection($section)
          ];
          break;

        case 'clickable_blocks':
          $mapped_sections[] = [
            'type' => 'clickable_blocks',
            'value' => $this->_parseClickableBlocksParagraph($section)
          ];
          break;
        case 'researchers':
          $mapped_sections[] = [
            'type' => 'researchers',
            'value' => $this->_parseResearchersSection($section)
          ];
          break;
        case 'grid':
          $mapped_sections[] = [
            'type' => 'grid',
            'value' => $this->_parseGridSection($section)
          ];
          break;
      }
    }
    return $mapped_sections;
  }

  private function _parseTestimonialSection($section): array {
    if (!$this->checkFieldExistsAndNotEmpty($section, 'field_testimonial')) return [];

    $entity = $section
      ->get("field_testimonial")
      ->first()
      ->get('entity')
      ->getTarget()
      ->getValue();
    $entity = $this->_getEntityTranslation($entity);
    $testimonial['title'] = $entity->get("title")->value;
    $testimonial['description'] = $entity->get("body")->value;
    $testimonial['color'] = $entity->get("field_background_color")->value;

    if ($this->checkFieldExistsAndNotEmpty($entity, 'field_link')) {
      $testimonial['button'] = $this->_parseLink($entity->field_link);
    }

    $testimonial['author'] = [
      "name" => $entity->get("field_author_name")->value,
      "function" => $entity->get("field_author_function")->value,
      "picture" => [
        "src" => $entity->field_author_picture->entity ? ImageStyle::load('thumbnail')->buildUrl($entity->field_author_picture->entity->getFileUri()) : null,
        "alt" => $entity->field_author_picture->alt
      ]
    ];

    return $testimonial;
  }

  private function _parseNewsSection($section): array
  {
    $section = $this->_getEntityTranslation($section);
    $news = [];

    $nids = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('promote', 1)
      ->condition('type', 'article')
      ->sort('created', 'DESC')
      ->range(0, 5)
      ->execute();

    if ($this->checkFieldExistsAndNotEmpty($section, 'field_title')) {
      $news['title'] = $section->get('field_title')->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($section, 'field_small_text')) {
      $news['description'] = $section->get('field_small_text')->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($section, 'field_subtitle')) {
      $news['subtitle'] = $section->get('field_subtitle')->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($section, 'field_link')) {
      $news['cta'] = $this->_parseLink($section->field_link);
    }

    if ($this->checkFieldExistsAndNotEmpty($section, 'field_new_tab')) {
      $news['nTab'] = $this->_parseNewTab($section->field_new_tab->getValue()[0]['value']);
    }

    $news['newsItems'] = $this->_parseNewsItem($nids);
    return $news;
  }

  private function _parseNewsItem($news): array
  {
    // $news = $this->_getEntityTranslation($news);
    $items = [];
    foreach ($news as $key => $item) {
      $item = Node::load($item);
      $item = $this->_getEntityTranslation($item);
      $data = $this->mapArticle($item, 'teaser');
      array_push($items, $data);
    }
    return $items;
  }

  private function _parseLogosSection($logos)
  {
    $data = [];
    $blocks = [];
    if ($this->checkFieldExistsAndNotEmpty($logos, 'field_logo')) {

      foreach ($logos->field_logo as $key => $item) {
        $logo = $this->_getImageStyleFromMedia($item->target_id);
        array_push($blocks, $logo);
      }
      $data['logos'] = $blocks;
    }

    return $data;
  }

  private function _parseTextAndCardsSection($cards)
  {
    $cards = $this->_getEntityTranslation($cards);
    $data = [];
    $blocks = [];
    $data['title'] = $cards->field_title->getValue()[0]['value'];

    if ($this->checkFieldExistsAndNotEmpty($cards, 'field_content')) {
      $data['description'] = $cards->field_content->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($cards, 'field_link')) {
      $data['link'] = $this->_parseLink($cards->field_link);
    }

    if ($this->checkFieldExistsAndNotEmpty($cards, 'field_new_tab')) {
      $data['nTab'] = $this->_parseNewTab($cards->field_new_tab->getValue()[0]['value']);
    }

    if ($this->checkFieldExistsAndNotEmpty($cards, 'field_cards')) {
      for ($x = 0; $x < count($cards->field_cards); $x++){
        $paragraph = Paragraph::load($cards->field_cards->getValue()[$x]['target_id']);
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
        array_push($blocks, $card);
      }
    }
    $data['tips'] = $blocks;
    return $data;
  }

  private function _parseResearchersSection($section) {
    $items = [];

    if(!$this->checkFieldExistsAndNotEmpty($section, 'field_researchers')) return $items;

    foreach ($section->get('field_researchers')->referencedEntities() as $key => $item)
    {
      $item = $this->_getEntityTranslation($item);
      $data = $this->mapPerson($item, 'teaser');
      array_push($items, $data);
    }
    return ['items' => $items];
  }

  private function _parseGridSection($section)
  {
    $section = $this->_getEntityTranslation($section);

    $data = [];

    if ($this->checkFieldExistsAndNotEmpty($section, 'field_title')) {
       $data['title'] = $section->field_title->getValue()[0]['value'];
    }
    if ($this->checkFieldExistsAndNotEmpty($section, 'field_content')) {
      $data['intro'] = $section->field_content->getValue()[0]['value'];
    }

    if ($this->checkFieldExistsAndNotEmpty($section, 'field_link')) {
      $data['link'] = $this->_parseLink($section->field_link);
    }

    if ($this->checkFieldExistsAndNotEmpty($section, 'field_new_tab')) {
      $data['nTab'] = $this->_parseNewTab($section->field_new_tab->getValue()[0]['value']);
    }

    $data['items'] = [];
    if ($this->checkFieldExistsAndNotEmpty($section, 'field_grid_items')) {
      foreach ($section->get('field_grid_items')->referencedEntities() as $key => $item)
      {
        $item = $this->_getEntityTranslation($item);


        $data['items'][] = [
            'title' => $item->getTitle(),
            'description' => ($this->checkFieldExistsAndNotEmpty($item, 'field_summary')) ? $item->field_summary->getValue()[0]['value'] : "",
            'image' => ($this->checkFieldExistsAndNotEmpty($item, 'field_media_image')) ? $this->_getImageStyleFromMedia($item->field_media_image->target_id, 'large') : null,
            'url' => $this->_getNodeUrl($item->id())
        ];
      }
    }

    return $data;
  }
}
