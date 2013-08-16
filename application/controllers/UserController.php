<?php
require_once 'modules/Checklist/htmlhelp.php';
require_once 'modules/Checklist/logger.php';
require_once '../application/controllers/ActionController.php';

class UserController extends Application_Controller_Action 
// Zend_Controller_Action
{

  public function init()
  {
    /* Initialize action controller here */
    /**
     * initialize parent here
     **/
    parent::init();
  }

  public function mainAction() {
    /*
     * 
     */
  }

  public function loginAction() {
    $this->dialog_name = 'user/login';
    logit ( "{$this->dialog_name}" );
    $user = new Application_Model_DbTable_User();
    $langtag = $this->echecklistNamespace->lang;
    if (!$this->getRequest()->isPost()) {
      if ($this->usertype != '') {
        // logit('redirect');
        $this->_redirector->gotoUrl("/audit/edit/1/" ); 
      }
      // logit('LAB: '. print_r($row, true));
      $this->makeDialog();
    } else {
      $this->collectData();
      $row = $user->getUserByUsername($this->data['userid']);
      if ($this->data['password'] == $row['password']) { // FIXME - goto BCRYPT
        $xuser = array();
        foreach($row as $a => $b) {
          if ($a != 'password') { 
            $xuser [$a] = $b;
            logit ("Added {$a} => {$b}");
          }
        }
        $this->echecklistNamespace->user = $xuser;
        // $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->_redirector->gotoUrl("/audit/edit/1/");
      } else {
        $this->makeDialog();
        $this->echecklistNamespace->flash = "UserId or password incorrect";
        $this->data['password'] = '';
        $this->makeDialog($this->data);
      } /*else {
          $row = $user->getUserByUsername($userid);
          $xuser = array();
          foreach($row as $a => $b) {
          if ($a != 'password') { // FIXME - goto BCRYPT
          $xuser [$a] = $b;
          logit ("Added {$a} => {$b}");
          }
          }*/
    }
  }


  public function logoutAction() {
    /* logout and clear the user entry from the session */
    unset($this->echecklistNamespace->user);
    $this->_redirector->gotoUrl("/user/login");
  }
  
  public function createAction() {
    $this->dialog_name = 'user/create';
    logit ( "{$this->dialog_name}" );
    $user = new Application_Model_DbTable_User();
    // $urldata = $this->getRequest()->getParams();
    $langtag = $this->echecklistNamespace->lang;
    if (!$this->getRequest()->isPost()) {
      // logit('LAB: '. print_r($row, true));
      $this->makeDialog();
    } else {
      $this->collectData();
      if ($this->data['password'] != $this->data['password2']) {
        $this->data['password'] = '';
        $this->data['password2'] = '';
        $this->echecklistNamespace->flash = "Passwords do not match";
        $this->makeDialog($this->data);
      } else {
        //logit('Data: ' . print_r($this->data, true));
        unset($this->data['password2']);
        try {
          $user->insertData($this->data); 
        } catch (Exception $e) {
          $this->data['password'] = '';
          $this->data['password2'] = '';
          $this->echecklistNamespace->flash = "User Id is already in use";
          $this->makeDialog($this->data);
          return;
        }
        // Redirect it from here
      }
    }
  }

  public function editAction() {
    $this->dialog_name = 'user/edit';
    logit ("{$this->dialog_name}" );
    $user = new Application_Model_DbTable_Lab();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int)  $pinfo[3];
    $langtag = $this->echecklistNamespace->lang;
    // $urldata = $this->getRequest()->getParams();
    if (!$this->getRequest()->isPost()) {
      $row = $lab->getLab($id);
      // logit('LAB: '. print_r($row, true));
      $this->makeDialog($row);
    } else {
      // display the form here
      $this->collectData();
      // logit('Data: ' . print_r($this->data));
      $user->updateData($data, $id); 
    }
  }

}
