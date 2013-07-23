<?php

/**
 * This is the super class for our controllers
 */
require_once 'modules/Checklist/logger.php';
class Application_Controller_Action extends Zend_Controller_Action
{
  private static $echecklistNamespace ;

  public function init()
  {
    /* initialize here */
    self::$echecklistNamespace = new Zend_Session_Namespace('eChecklist');
    Zend_Session::start();
    /* Remove this when seesions work correctly */
    if (isset($echecklistNamespace->userct)) {
      // this will increment for each page load.
      $echecklistNamespace->userct++;
    } else {
      $echecklistNamespace->userct = 1; // first time
    }
    
    logit( "User count: {$echecklistNamespace->userct}");
  }

  public function getHandle()
  {
    return self::$echecklistNamespace;
  }
}