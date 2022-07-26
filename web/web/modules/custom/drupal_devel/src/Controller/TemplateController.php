<?php
/**
 * @file
 * Contains \Drupal\drupal_devel\Controller\TemplateController.
 */
 
namespace Drupal\drupal_devel\Controller;
 
use Drupal\Core\Controller\ControllerBase;
 
class TemplateController extends ControllerBase {

  public function test() {
 
    return [
        '#theme' => 'fe-template',
        '#fe_template' => (isset($_GET['template'])) ? $_GET['template'] : NULL
    ];

  }
}