<?php

/**
 * This is the super class for our controllers
 */
require_once 'modules/Checklist/logger.php';
class Application_Controller_Action extends Zend_Controller_Action
{
  private  $echecklistNamespace ;
  public $debug = 0;

  public function init()
  {
    /* initialize here */
    $this->echecklistNamespace = new Zend_Session_Namespace('eChecklist');
    Zend_Session::start();
    /* Remove this when seesions work correctly */
    if ($this->debug) {
      if (isset($echecklistNamespace->userct)) {
        // this will increment for each page load.
        $echecklistNamespace->userct++;
      } else {
        $echecklistNamespace->userct = 1; // first time
      }
      
      logit( "User count: {$echecklistNamespace->userct}");
    }
  }
  
  public function convert2PDF($html)
  {
    //echo 'Create HTML & then convert it to PDF!';
    /* $data = $this->renderPhpToString(); */
    /**$albums = new Application_Model_DbTable_Albums();
    $sql = "order by artist name";
    $this->view->albums = $albums->getAlbums();
    $html = $this->view->render('index/index.phtml');
    logit("Data: {$html}");
    **/
    require_once 'modules/mpdf56/examples/testmpdf.php';
    html2pdf($html);
    
  }

}