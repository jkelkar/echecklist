<?php

class TestController extends Zend_Controller_Action
{
  public function init()
  {
    /* Initialize action controller here */
  }

  public function indexAction()
  {
    $test = new Application_Model_Test();
    $this->view->xtest = $test->getchAll();
  }
}