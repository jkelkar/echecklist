<?php

/**
 * This is the super class for our controllers
 */

class Application_Controller_Action extends Zend_Controller_Action
{
  protected $echecklistNamespace = new Zend_Session_Namespace('eChecklist');

  public function init()
  {
    /* initialize here */
    Zend_Session::start();
    if (isset($echecklistNamespace->userct)) {
      // this will increment for each page load.
      $echecklistNamespace->userct++;
    } else {
      $echecklistNamespace->userct = 1; // first time
    }
    
    echo "User count: ",
    $echecklistNamespace->userct;
  }

  
}