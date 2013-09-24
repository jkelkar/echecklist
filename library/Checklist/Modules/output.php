<?php


require_once 'modules/Checklist/logger.php';
require_once 'modules/Checklist/datefns.php';
require_once 'modules/Checklist/general.php';
require_once 'modules/Checklist/processor.php';

class Output {
  public $debug = 0;

  public function process($name, $list) {
    // process the selected action
    logit("ProDialog Name: {$this->dialog_name}");
    $proc = new Processing();

    $proc->process($list, $name);
    $this->session->flash = 'Excel sheet done.';
    $this->_redirector->gotoUrl($this->mainpage);
  }





}
