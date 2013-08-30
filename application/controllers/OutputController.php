<?php


require_once 'modules/Checklist/logger.php';
require_once 'modules/Checklist/datefns.php';
require_once 'modules/Checklist/general.php';
require_once '../application/controllers/ActionController.php';

class OutputController extends Application_Controller_Action {
  public $debug = 0;

  public function init() {
    /* Initialize action controller here */
    // logit("MT aud init: ". microtime(true));
    parent::init();
  }

  public function indexAction() {
  }

  public function processAction() {
    // process the selected action
    $prefix = 'cb_';
    if ($this->collectData())
      return;
    logit('Data: ' . print_r($this->data, true));
    $this->collectextraData($prefix);
    logit('Data: ' . print_r($this->extra, true));
    exit();
  }





}
