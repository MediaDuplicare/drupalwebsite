<?php

namespace Drupal\Duplicare_content_mapper\Services;

use Drupal\Core\Url;
use Drupal\media\Entity\Media;
use Drupal\image\Entity\ImageStyle;
use Drupal\duplicare_content_mapper\Services\Traits\Sections;
use Drupal\duplicare_content_mapper\Services\Traits\Paragraphs;
use Drupal\duplicare_content_mapper\Services\Traits\Overlap;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\user\Entity\User;

class DuplicareMapper
{

  use Sections;
  use Paragraphs;
  use Overlap;

  public function mapLandingPage($node, $view_mode)
  {
    $landingPage = [];

    switch ($view_mode) {
      case 'full':
        $landingPage['front'] = true;

        //title
        $landingPage['title'] = $node->label();
        $landingPage['url'] = $this->_getNodeUrl($node->id());
        $landingPage['type'] = $node->getType();

        //hero
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_media_image')) {
          $landingPage['hero'] = $this->_mapHero($node);
        }

        //paragraphs
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_content')) {
          $landingPage['paragraphs'] = $this->mapParagraphs($node);
        }

        //sections
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_sections')) {
          $landingPage['sections'] = $this->mapSections($node);
        }
        break;

      case 'teaser' || 'search_index':
        $landingPage['front'] = true;

        //title
        $landingPage['title'] = $node->label();
        $landingPage['url'] = $this->_getNodeUrl($node->id());
        $landingPage['type'] = $node->getType();
        $landingPage['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";
        break;
    }
    return $landingPage;
  }
  public function mapPage($node, $view_mode)
  {
    $page = [];

    $node = $this->_getEntityTranslation($node);

    switch ($view_mode) {
      case 'full':
        $page['title'] = $node->label();
        $page['intro'] =  $node->field_intro->value;
        $page['url'] = $this->_getNodeUrl($node->id());
        $page['type'] = $node->getType();
        $page['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";
        $page['created'] = \Drupal::service('date.formatter')->format($node->getCreatedTime());

        $page['hero'] = $this->_mapHero($node);

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_content')) {
          $page['paragraphs'] = $this->mapParagraphs($node);
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_sections')) {
          $page['sections'] = $this->mapSections($node);
        }

        break;

      case 'teaser' || 'search_index':
        $page['title'] = $node->label();
        $page['url'] = $this->_getNodeUrl($node->id());
        $page['type'] = $node->getType();
        $page['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";
        break;
    }
    return $page;
  }
  public function mapModule($node, $view_mode)
  {
    $module = [];
    $module['title'] = $node->label();
    $module['url'] = $this->_getNodeUrl($node->id());
    $module['type'] = $node->getType();
    $module['created'] = \Drupal::service('date.formatter')->format($node->getCreatedTime());

    if($view_mode === 'full') {
       //hero
       if ($this->checkFieldExistsAndNotEmpty($node, 'field_intro'))
       {
           $module['hero'] = $this->_mapHero($node);
       }

       //paragraphs
       if ($this->checkFieldExistsAndNotEmpty($node, 'field_content'))
       {
         $module['paragraphs'] = $this->mapParagraphs($node);
       }
    }

    return $module;
  }
  public function mapArticle($node, $view_mode)
  {
    $article = [];
    switch ($view_mode) {
      case 'full':
        $node = $this->_getEntityTranslation($node);
        $uid = $node->getOwnerId();
        $user_picture = User::load($uid)->user_picture->entity;

        $article['title'] = $node->label();
        $article['url'] = $this->_getNodeUrl($node->id());
        $article['type'] = $node->getType();
        $article['created'] = \Drupal::service('date.formatter')->format($node->getCreatedTime(), "duplicare_date");
        $article['author'] = [
          "name" => User::load($uid)->getDisplayName(),
          "avatar" => $user_picture ? ImageStyle::load("large")->buildUrl($user_picture->getFileUri()) : null,
          "roles" => User::load($uid)->getRoles()
        ];

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_media_image')) {
          $article['image'] = $this->_getImageStyleFromMedia($node->field_media_image->target_id, 'large');
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_media_image')) {
          $article['hero'] = $this->_mapHero($node);
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_content')) {
          $article['paragraphs'] = $this->mapParagraphs($node);
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_sections')) {
          $article['sections'] = $this->mapSections($node);
        }
        break;

      case 'teaser' || 'search_index':
        $article['title'] = $node->label();
        $article['url'] = $this->_getNodeUrl($node->id());
        $article['type'] = $node->getType();
        $article['created'] = \Drupal::service('date.formatter')->format($node->getCreatedTime());
        $article['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_media_image')) {
          $article['image'] = $this->_getImageStyleFromMedia($node->field_media_image->target_id, 'large');
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_tags')) {
          $tag = $this->_getEntityTranslation($node->get('field_tags')->first()->get('entity')->getTarget()->getValue());
          $article['category'] = $tag->getName();
        }
        break;
    }
    return $article;
  }
  public function mapVacancy($node, $view_mode)
  {
    $vacancy = [];
    $node = $this->_getEntityTranslation($node);
    switch ($view_mode) {
      case 'full':
        $vacancy['title'] = $node->label();
        $vacancy['url'] = $this->_getNodeUrl($node->id());
        $vacancy['type'] = $node->getType();
        $vacancy['created'] = \Drupal::service('date.formatter')->format($node->getCreatedTime());

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_media_image')) {
          $vacancy['image'] = $this->_getImageStyleFromMedia($node->field_media_image->target_id, 'large');
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_logo')) {
          $vacancy['hero'] = $this->_mapHero($node);
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_content')) {
          $vacancy['paragraphs'] = $this->mapParagraphs($node);
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_sections')) {
          $vacancy['sections'] = $this->mapSections($node);
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_recruiter_name')) {
          $vacancy['recruiterName'] = $node->field_recruiter_name->getValue()[0]['value'];
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_recruiter_function')) {
          $vacancy['recruiterFunction'] = $node->field_recruiter_function->getValue()[0]['value'];
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_phone')) {
          $vacancy['recruiterPhone'] = $node->field_phone->getValue()[0]['value'];
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_email')) {
          $vacancy['recruiterEmail'] = $node->field_email->getValue()[0]['value'];
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_address')) {
          $vacancy['recruiterAddress'] = $node->field_address->getValue()[0]['value'];
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_avatar')) {
          $vacancy['recruiterAvatar'] = $this->_getImageStyleFromMedia($node->field_avatar->target_id, 'large');
        }

        $vacancy['extern'] = ($node->field_extern->getValue()[0]['value']) ? "Extern" : "Intern";

        break;

      case 'teaser' || 'search_index':
        $vacancy['title'] = $node->label();
        $vacancy['url'] = $this->_getNodeUrl($node->id());
        $vacancy['extern'] = ($node->field_extern->getValue()[0]['value']) ? "Extern" : "Intern";
        $vacancy['organisation'] = $node->field_organisation->getValue()[0]['value'];
        $vacancy['created'] = \Drupal::service('date.formatter')->format($node->getCreatedTime());
        $vacancy['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_media_image')) {
          $vacancy['image'] = $this->_getImageStyleFromMedia($node->field_media_image->target_id, 'large');
        }
        break;
    }

    if ($this->checkFieldExistsAndNotEmpty($node, 'field_external_link')) {
        $vacancy['externalLink'] = $this->_parseLink($node->field_external_link);
    }

    return $vacancy;
  }
  public function mapEducation($node, $view_mode)
  {
    $education = [];

    switch ($view_mode) {
      case 'full':
        //title
        $education['title'] = $node->label();
        $education['url'] = $this->_getNodeUrl($node->id());
        $education['type'] = $node->getType();

        //hero
        $education['hero'] = $this->_mapHero($node);

        //menu
        $main_menu = $this->_getMenuTree(0, 6, 'main');
        $submenu = $this->_getSubMenu($node, $main_menu['#items']);
        if(!empty($submenu))  $education['menu'] = $submenu;

        //paragraphs
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_content')) {
          $education['paragraphs'] = $this->mapParagraphs($node);
        }

        //sections
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_sections')) {
          $education['sections'] = $this->mapSections($node);
        }
        break;
      case 'teaser' || 'search_index':
        $education['title'] = $node->label();
        $education['url'] = $this->_getNodeUrl($node->id());
        $education['type'] = $node->getType();
        $education['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";
        break;
    }
    return $education;
  }
  public function mapTraining($node, $view_mode)
  {
    $training = [];

    switch ($view_mode) {
      case 'full':
        //title
        $training['title'] = $node->label();
        $training['url'] = $this->_getNodeUrl($node->id());
        $training['type'] = $node->getType();

        //hero
        $training['hero'] = $this->_mapHero($node);

        //menu
        $main_menu = $this->_getMenuTree(0, 6, 'main');
        $submenu = $this->_getSubMenu($node, $main_menu['#items']);
        if(!empty($submenu))  $training['menu'] = $submenu;

        //paragraphs
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_content')) {
          $training['paragraphs'] = $this->mapParagraphs($node);
        }
        break;
      case 'teaser' || 'search_index':
        $training['title'] = $node->label();
        $training['url'] = $this->_getNodeUrl($node->id());
        $training['type'] = $node->getType();
        $training['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";
        break;
    }

    return $training;
  }
  public function mapHistory($node, $view_mode)
  {
    $history = [];

    switch ($view_mode) {
      case 'full':
        //title
        $history['title'] = $node->label();
        $history['url'] = $this->_getNodeUrl($node->id());
        $history['type'] = $node->getType();

        //hero
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_intro_extra')) {
          $history['hero'] = $this->_mapHero($node);
        }

        //paragraphs
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_content')) {
          $history['paragraphs'] = $this->mapParagraphs($node);
        }
        break;
      case 'teaser' || 'search_index':
        $history['title'] = $node->label();
        $history['url'] = $this->_getNodeUrl($node->id());
        $history['type'] = $node->getType();
        $history['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";
        break;
    }

    return $history;
  }
  public function mapResearchGroup($node, $view_mode)
  {
    $researchGroup = [];

    switch ($view_mode) {
      case 'full':
        //title
        $researchGroup['title'] = $node->label();
        $researchGroup['url'] = $this->_getNodeUrl($node->id());
        $researchGroup['type'] = $node->getType();

        //hero
        $researchGroup['hero'] = $this->_mapHero($node);

        //menu
        $main_menu = $this->_getMenuTree(0, 6, 'main');
        $submenu = $this->_getSubMenu($node, $main_menu['#items']);
        if(!empty($submenu))  $researchGroup['menu'] = $submenu;

        //paragraphs
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_content')) {
          $researchGroup['paragraphs'] = $this->mapParagraphs($node);
        }

        //sections
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_sections')) {
          $researchGroup['sections'] = $this->mapSections($node);
        }
        break;
      case 'teaser' || 'search_index':
        $researchGroup['title'] = $node->label();
        $researchGroup['url'] = $this->_getNodeUrl($node->id());
        $researchGroup['type'] = $node->getType();
        $researchGroup['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_media_image')) {
          $researchGroup['image'] = $this->_getImageStyleFromMedia($node->field_media_image->target_id, 'large');
        }
        break;
    }

    return $researchGroup;
  }
  public function mapSubpage($node, $view_mode)
  {
      //fields for all $view_mode
      $subpage['title'] = $this->checkFieldExistsAndNotEmpty($node, 'field_main_title') ?  $node->field_main_title->getValue()[0]['value'] : $node->label();
      $subpage['url'] = $this->_getNodeUrl($node->id());
      $subpage['type'] = $node->getType();
      $modules_active = (bool) $node->field_modules->getValue()[0]['value'];

      if($view_mode === "full" )
      {
          if(($this->checkFieldExistsAndNotEmpty($node, 'field_education')
              || $this->checkFieldExistsAndNotEmpty($node, 'field_training')
              || $this->checkFieldExistsAndNotEmpty($node, 'field_research_group'))
          )
          {
            if($this->checkFieldExistsAndNotEmpty($node, 'field_education') ) {
              $parent = Node::load($node->field_education->getValue()[0]['target_id']);
            }

            if($this->checkFieldExistsAndNotEmpty($node, 'field_training') ) {
              $parent = Node::load($node->field_training->getValue()[0]['target_id']);
            }

            if($this->checkFieldExistsAndNotEmpty($node, 'field_research_group') ) {
              $parent = Node::load($node->field_research_group->getValue()[0]['target_id']);
            }


            //menu
            $main_menu = $this->_getMenuTree(0, 6, 'main');
            $submenu = $this->_getSubMenu($parent, $main_menu['#items']);
            if(!empty($submenu))  $subpage['menu'] = $submenu;
          }

          //hero
          $subpage['hero'] = $this->_mapHero($node);

          //paragraphs
          if ($this->checkFieldExistsAndNotEmpty($node, 'field_content'))
          {
            $subpage['paragraphs'] = $this->mapParagraphs($node);
          }

          //sections
          if ($this->checkFieldExistsAndNotEmpty($node, 'field_sections'))
          {
            $subpage['sections'] = $this->mapSections($node);
          }
      }

      return $subpage;
  }
  public function mapProject($node, $view_mode)
  {
    $project = [];

    switch ($view_mode) {
      case 'full':
        $project['title'] = $node->label();
        $project['url'] = $this->_getNodeUrl($node->id());
        $project['type'] = $node->getType();
        $project['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";
        $project['created'] = \Drupal::service('date.formatter')->format($node->getCreatedTime());

        $project['hero'] = $this->_mapHero($node);


        if ($this->checkFieldExistsAndNotEmpty($node, 'field_address')) {
          $project['address'] = $node->field_address->getValue()[0]['value'];
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_email')) {
          $project['email'] = $node->field_email->getValue()[0]['value'];
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_phone')) {
          $project['phone'] = $node->field_phone->getValue()[0]['value'];
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_content')) {
          $project['paragraphs'] = $this->mapParagraphs($node);
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_sections')) {
          $project['sections'] = $this->mapSections($node);
        }

        $project['researchers'] = $this->_getResearchersByProjectId($node->id());

        $project['researchGroup'] =  ($this->checkFieldExistsAndNotEmpty($node, 'field_research_group')) ? $this->_getEntityTranslation(Node::load($node->field_research_group->target_id)) : null;

        break;

      case 'teaser' || 'search_index':
        $project['title'] = $node->label();
        $project['url'] = $this->_getNodeUrl($node->id());
        $project['type'] = $node->getType();
        $project['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_media_image')) {
          $project['image'] = $this->_getImageStyleFromMedia($node->field_media_image->target_id, 'large');
        }
        break;
    }
    return $project;
  }

  public function mapPerson($node,  $view_mode)
  {
    $person = [];

    switch ($view_mode) {
      case 'full':
        $person['title'] = $node->label();
        $person['url'] = $this->_getNodeUrl($node->id());
        $person['type'] = $node->getType();
        $person['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";

        $person['created'] = \Drupal::service('date.formatter')->format($node->getCreatedTime());
        if ($this->checkFieldExistsAndNotEmpty($node, 'field_content')) {
          $person['paragraphs'] = $this->mapParagraphs($node);
        }

        if ($this->checkFieldExistsAndNotEmpty($node, 'field_sections')) {
          $person['sections'] = $this->mapSections($node);
        }

        $person['projects'] = [];
        if($this->checkFieldExistsAndNotEmpty($node, 'field_project')){
          foreach ($node->get('field_project')->referencedEntities() as $key => $item)
          {
            $item = $this->_getEntityTranslation($item);
            $item = $this->mapProject($item, 'teaser');
              $person['projects'][] = $item;
          }
        }
        break;

      case 'teaser' || 'search_index':
        $person['title'] = $node->label();
        $person['url'] = $this->_getNodeUrl($node->id());
        $person['type'] = $node->getType();
        $person['description'] = ($this->checkFieldExistsAndNotEmpty($node, 'field_summary')) ? $node->field_summary->getValue()[0]['value'] : "";
        $person['image'] = $this->checkFieldExistsAndNotEmpty($node, 'field_media_image') ? $this->_getImageStyleFromMedia($node->field_media_image->getValue()[0]['target_id'], 'thumbnail') : null;
        $person['function'] = $this->checkFieldExistsAndNotEmpty($node, 'field_function') ? $node->field_function->getValue()[0]['value'] : null;
        break;
    }
    return $person;
  }

  private function _getResearchersByProjectId($projectId)
  {
    $items = [];
    $nids = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', 'person')
        ->condition('field_project', [$projectId], 'IN')
        ->sort('title')
        ->execute();

      foreach ($nids as $item) {
        $person = Node::load($item);
        $person = $this->_getEntityTranslation($person);
        $items[] = [
            'title' => $person->getTitle(),
            'function' => $this->checkFieldExistsAndNotEmpty($person, 'field_function') ? $person->field_function->getValue()[0]['value'] : null,
            'image' => $this->checkFieldExistsAndNotEmpty($person, 'field_media_image') ? $this->_getImageStyleFromMedia($person->field_media_image->getValue()[0]['target_id'], 'thumbnail') : null
        ];
      }
      return $items;
  }

  private function _mapHero($node)
  {
    $hero = [];
    if ($this->checkFieldExistsAndNotEmpty($node, 'field_intro_title'))
    {
      $hero['title'] = $node->field_intro_title->getValue()[0]['value'];
    } elseif($this->checkFieldExistsAndNotEmpty($node, 'field_main_title'))
    {
      $hero['title'] = $node->field_main_title->getValue()[0]['value'];
    } else
    {
      $hero['title'] = $node->getTitle();
    }

    if ($this->checkFieldExistsAndNotEmpty($node, 'field_logo')) {
      $hero['logo'] = [
        'type' => 'image',
        'image' => $this->_getImageStyleFromMedia($node->field_logo->getValue()[0]['target_id'], 'medium')
      ];
    }

    if ($this->checkFieldExistsAndNotEmpty($node, 'field_link'))
    {
      $hero['link'] = $this->_parseLink($node->field_link);
      $hero['newTab'] = $this->_parseNewTab($node->field_new_tab);
    }

    if ($this->checkFieldExistsAndNotEmpty($node, 'field_intro_extra'))
    {
      $hero['description'] = $node->field_intro_extra->getValue()[0]['value'];
    } elseif ($this->checkFieldExistsAndNotEmpty($node, 'field_intro')) {
      $hero['description'] = $node->field_intro->getValue()[0]['value'];
    } else {
      $hero['description'] = "";
    }

    if ($this->checkFieldExistsAndNotEmpty($node, 'field_media_image')) {
      $hero['media'] = [
        'type' => 'image',
        'image' => $this->_getImageStyleFromMedia($node->field_media_image->getValue()[0]['target_id'], 'article_large')
      ];
    }

    if ($this->checkFieldExistsAndNotEmpty($node, 'field_header_image')) {
      $paragraphs = $node->get('field_header_image')->referencedEntities();

      foreach ($paragraphs as $index => $paragraph) {
        $type = $paragraph->getType();

        switch ($type) {
          case 'two_pictures':
            $hero['two_pictures'] = $this->_parseTwoImagesParagraph(
              Paragraph::load($node->field_header_image->getValue()[0]['target_id'])
            );
            break;
          case 'image_notification':
            $paragraph = $this->_getEntityTranslation($paragraph);
            $hero['image_notification'] = [
              'title' => $paragraph->field_title->value,
              'description' => $paragraph->field_small_text->value,
              'button' => $this->_parseLink($paragraph->field_link),
              'image' => $this->_getImageStyleFromMedia($paragraph->field_media_image->target_id)
            ];
            break;
        }
      }
    }

    if ($this->checkFieldExistsAndNotEmpty($node, 'field_buttons')) {
      $buttons = [];

      foreach ($node->field_buttons as $index => $button) {
        array_push($buttons, $this->_parseLink($button, false));
      }

      $hero['buttons'] = $buttons;
    }
    return $hero;
  }
  private function _getModulesMenu($trainingId)
  {
    $items = [];
    $nids = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', 'module')
        ->condition('field_training', [$trainingId], 'IN')
        ->sort('title')
        ->execute();

      foreach ($nids as $item) {
        $module = Node::load($item);
        $items[] = [
          'link' => [
              "url" => $this->_getNodeUrl($module->id()),
              "text" => $module->getTitle(),
              "external" => false
          ],
          'content' => $this->mapParagraphs($module)
        ];
      }
      return $items;
  }
  private function _mapMenu($node)
  {
    $menu['title'] = $node->field_menu_title->getValue()[0]['value'];

    if ($this->checkFieldExistsAndNotEmpty($node, 'field_menu_item')) {
      $menuItems = [];
      foreach ($node->field_menu_item as $item) {
        $data = [];
        $paragraph = Paragraph::load($item->target_id);
        $data['link'] = $this->_parseLink($paragraph->field_link);
        $data['isButton'] = $paragraph->field_is_button->getValue()[0]['value'];
        $data['isActive'] = $paragraph->field_is_active->getValue()[0]['value'];
        array_push($menuItems, $data);
      }
      $menu['menuLinks'] = $menuItems;
    }
    return $menu;
  }

  public function checkFieldExistsAndNotEmpty($entity, $field_name)
  {
    if ($entity->hasField($field_name)
      && !$entity->get($field_name)->isEmpty()) return true;
    return false;
  }
  private function _getImageStyleFromMedia($media_id, $style = 'article_large')
  {
    $media = Media::load($media_id);
    $src = ImageStyle::load($style)->buildUrl($media->field_media_image->entity->getFileUri());
    return [
      'src' => $src,
      'title' => $media->field_media_image->title,
      'alt' => $media->field_media_image->alt,
      'copyright' => ($this->checkFieldExistsAndNotEmpty($media, 'field_copyright')) ? $media->field_copyright->getValue()[0]['value'] : "",
      'description' => ($this->checkFieldExistsAndNotEmpty($media, 'field_description')) ? $media->field_description->getValue()[0]['value'] : ""
    ];
  }
  private function _parseLink($link, $external = null)
  {
    $isExternal = $external !== null ? $external : $link->first()->getUrl()->isExternal();
    if ($isExternal) {
      return [
        "url" => $link->uri,
        "text" => $link->title,
        "external" => true
      ];
    }
    return [
      "url" => Url::fromUri($link->uri),
      "text" => $link->title,
      "external" => false
    ];
  }

  private function _parseNewTab($tab)
  {
    if ($tab == "1") {
      return "target = _blank";
    }

    return "";
  }
  private function _getNodeUrl($nid)
  {
    return Url::fromRoute('entity.node.canonical', ['node' => $nid])->toString();
  }
  function _getMenuTree($min_depth, $max_depth, $menu_name)
  {
      // $menu_parameters = new \Drupal\Core\Menu\MenuTreeParameters();
      $menu_tree = \Drupal::menuTree();
      $menu_parameters = $menu_tree->getCurrentRouteMenuTreeParameters('main');
      $menu_parameters
          ->setMinDepth($min_depth)
          ->setMaxDepth($max_depth)
          ->onlyEnabledLinks();
      // Get the tree.
      $menu_tree_service = \Drupal::service('menu.link_tree');
      $tree = $menu_tree_service->load($menu_name, $menu_parameters);
      // Apply some manipulators (checking the access, sorting).
      $manipulators = [
        ['callable' => 'menu.default_tree_manipulators:checkNodeAccess'],
        ['callable' => 'menu.default_tree_manipulators:checkAccess'],
        ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
        ['callable' => 'menu_manipulator.menu_tree_manipulators:filterTreeByCurrentLanguage'],
      ];
      $tree = $menu_tree_service->transform($tree, $manipulators);
      return $menu_tree_service->build($tree);
  }
  public function _getEntityTranslation($entity){
    if ($entity !== null && gettype($entity) !== "array") {
      $langcode = \Drupal::service('language_manager')->getCurrentLanguage()->getId();
      if ($entity->hasTranslation($langcode)){
        return $entity->getTranslation($langcode);
      }
    }
    return $entity;
  }

  private function _getSubMenu($node, $menu_items, &$items = []) {
    //items from same level
    $node = $this->_getEntityTranslation($node);
    $nodeUrl = $node->toUrl()->toString();
    $currentNode = \Drupal::routeMatch()->getParameter('node');
    foreach($menu_items  as $index => $item)
    {
      if($item['url']->toString() === $nodeUrl)
      {
        foreach ($item['below'] as $child) {

          $items[] = [
            'isButton' => $child['original_link']->getOptions() ? $child['original_link']->getOptions()['attributes']['button'] : 0,
            'targetBlank' => (isset($child['original_link']->getOptions()['attributes']['target'])) ? $child['original_link']->getOptions()['attributes']['target'] : "self",
            'isActive' => ($child['in_active_trail'] || ($child['url']->toString() === $currentNode->toUrl()->toString())) ? true : false,
            'link' => [
              'text' =>  $child['title'],
              'url' =>  $child['url']->toString()
            ]
          ];
        }
      }
      if(!empty($item['below'])) $this->_getSubmenu($node, $item['below'], $items);
    }
    return [
      'title' => $node->getTitle(),
      'links' => $items
    ];
  }
}

