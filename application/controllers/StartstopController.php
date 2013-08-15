<?php
require_once 'modules/Checklist/htmlhelp.php';
require_once 'modules/Checklist/logger.php';
require_once '../application/controllers/ActionController.php';

class StartstopController extends Application_Controller_Action
// Zend_Controller_Action
{

  public function init()
  {
    /* Initialize action controller here
     *
     * initialize parent here
     *
     */
    parent::init();
  }
  
  public function loginAction() {
    // log a user in
    $dialog_name = 'startstop/login';
    if ($this->getRequest()->isPost()) {
      logit ( 'in post' );
      $formData = $this->getRequest();
      logit("EC: " . print_r ( $this->echecklistNamespace, true ) );
      // if ($form->isValid($formData)) {
      logit('POST: ', print_r($formData->getRawBody(), true));
      $userid = $formData->getPost ('userid','');
      $password = $formData->getPost ('password','');
      $user = new Application_Model_DbTable_User();
      $row = $user->getUserByUsername($userid);
      logit ( 'User: ' . print_r($row, true));
      // logit ( "eChecklist userct: {$this->echecklistNamespace->userct}" );
      $xuser = array();
      foreach($row as $a => $b) {
        if ($a != 'password') { // FIXME - goto BCRYPT
          $xuser [$a] = $b;
          logit ("Added {$a} => {$b}");
        }
      }
      $this->echecklistNamespace->user = $xuser;
      
      logit ( "EC2: " . print_r ( $this->echecklistNamespace->user, true ) );
      $baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
      $this->_redirector->gotoUrl ( "/audit/edit/1/" ); //"/slipta/edit" );
    } else {
      if ($this->usertype != '') {
        logit('redirect');
        $this->_redirector->gotoUrl("/audit/edit/1/" ); //"/slipta/edit");
      }
      $dialog = new Application_Model_DbTable_DialogRow();
      $allrows = $dialog->getFullDialog($dialog_name);
      $title = $allrows['dialog']['title'];
      $drows = $allrows['dialog_rows'];
      // generate_dialog_processing($drows);
      $this->view->outlines = calculate_dialog($drows, array(''=>''), $this->view->langtag);
      $this->view->title = $title;
      $this->_helper->layout->setLayout ( 'overall' );
    }
  }

  public function loginOrigAction() {
    logit ( 'in login' );
    if ($this->getRequest()->isPost()) {
      logit ( 'in post' );
      $formData = $this->getRequest();
      logit ("EC: " . print_r ( $this->echecklistNamespace, true ) );
      // if ($form->isValid($formData)) {
      $username = $formData->getPost ( 'username', '' );
      $password = $formData->getPost ( 'password', '' );
      $user = new Application_Model_DbTable_User ();
      $row = $user->getUserByUsername ( $username );
      logit ( 'User: ' . print_r ( $row, true ) );
      // logit ( "eChecklist userct: {$this->echecklistNamespace->userct}" );
      $xuser = array ();
      foreach ( $row as $a => $b ) {
        if ($a != 'password') { // FIXME - goto BCRYPT
          $xuser [$a] = $b;
          logit ( "Added {$a} => {$b}" );
        }
      }
      $this->echecklistNamespace->user = $xuser;
      
      logit ( "EC2: " . print_r ( $this->echecklistNamespace->user, true ) );
      $baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
      $this->_redirector->gotoUrl ( "/audit/edit/1/" ); //"/slipta/edit" );
    } else {
      if ($this->usertype != '') {
        logit('redirect');
        $this->_redirector->gotoUrl("/audit/edit/1/" ); //"/slipta/edit");
      }
      $this->view->title = 'Login';
      $this->_helper->layout->setLayout ( 'overall' );
    }
    // }
  }
  
  public function logoutAction() {
    /* logout and clear the user entry from the session */
    unset($this->echecklistNamespace->user);
    $this->_redirector->gotoUrl("/startstop/login");
  }
  
}
