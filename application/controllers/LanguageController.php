<?php
require_once 'modules/Checklist/fillout.php';
require_once 'modules/Checklist/logger.php';
require_once '../application/controllers/ActionController.php';

class LanguageController extends Application_Controller_Action {
  public $debug = 0;

  public function init() {
    /* Initialize action controller here */
    parent::init();
  }

  public function indexAction() {
  
  }

  
  public function switchAction() {
    $lang_default= 'EN';
    $baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
    $lang = new Application_Model_DbTable_Language ();
    $lang_word = new Application_Model_DbTable_langword ();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $langtag = $pinfo[3];

    logit ( 'In language beginning' );

    // if (! $this->getRequest ()->isPost ()) {
      // write out the page
      $tword = $lang_word->getWords ( $langtag );

      if ($this->debug) {
        logit ( "Got language value: {$langtag}" );
      }
      // set the session language and go back to referer
      $this->echecklistNamespace->lang = $langtag;
      $request = $this->getRequest();
      $referer = $request->getHeader('referer');
      $this->_redirector->gotoUrl($referer);
//} 
  
  }

  
}
 
