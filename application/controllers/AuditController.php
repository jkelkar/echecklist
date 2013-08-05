<?php
require_once 'modules/Checklist/htmlhelp.php';
require_once 'modules/Checklist/logger.php';
require_once '../application/controllers/Action.php';

class AuditController extends Application_Controller_Action// Zend_Controller_Action
{

  public function init() {
    /* Initialize action controller here */
    /**
     * initialize parent here
     */
    parent::init ();
  
  }

  public function mainAction() {
    if ($this->getRequest ()->isPost ()) {
      $formData = $this->getRequest ();
      // if ($form->isValid($formData)) {
      $username = $formData->getPost ( 'username', '' );
      $password = $formData->getPost ( 'password', '' );
      $user = new Application_Model_DbTable_User ();
      $row = $user->getUserByUsername ( $username );
      // $u = array();
      // $eNamespace = parent::getHandle();
      logit ( "eChecklist {$eNamespace->userct}" );
      $eNamespace->user = array ();
      foreach ( $row as $a => $b ) {
        if ($a != 'password') { // FIXME - goto BCRYPT
          $eNamespace->user [$a] = $b;
          logit ( "Added {$a} => {$b}" );
        }
      }
      // $echecklistNamespace->user = $u;
      /* $this->_helper->redirector('index'); */
    } else {
      
      $this->_helper->layout->setLayout ( 'overall' );
    }
    // }
  }

  public function findAction() {
    logit ( 'Find: outer' );
    if ($this->getRequest ()->isPost ()) {
      logit ( 'Find: In post' );
      $formData = $this->getRequest ();
      // if ($form->isValid($formData)) {
      $labname = $formData->getPost ( 'labname', '' );
      $country = $formData->getPost ( 'country', '' );
      $stdate = $formData->getPost ( 'stdate', '' );
      $enddate = $formData->getPost ( 'enddate', '' );
      $audittype = $formData->getPost ( 'audittype', '' );
      logit ( "labname: {$username}" );
      logit ( "country: {$country}" );
      logit ( "stdate: {$stdate}" );
      logit ( "enddate: {$enddate}" );
      logit ( "audittype: {$audittype}" );
      
      $data = array (
          'labname' => $labneme,
          'country' => $country,
          'stdate' => strtotime ( $stdate ),
          'enddate' => strtotime ( $enddate ),
          'audittype' => $audittype
      );
      $labh = new Application_Model_DbTable_Lab ();
      $labs = $labh ( $data, 0, 20 );
    } else {
      logit ( 'Find: In display' );
      $this->_helper->layout->setLayout ( '13' );
      $labh = new Application_Model_DbTable_Lab ();
      $rows = $labh->getLabs ( 0, 20 );
      $this->view->baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
      $this->view->rows = $rows;
    }
  
  }
}
