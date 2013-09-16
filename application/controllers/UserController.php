<?php

require_once 'modules/Checklist/logger.php';
require_once 'modules/Checklist/htmlhelp.php';
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
        $this->_redirector->gotoUrl($this->mainpage);
      }
      // logit('LAB: '. print_r($row, true));
      $this->makeDialog();
    } else {
      if ($this->collectData()) return;
      $row = $user->getUserByUsername($this->data['userid']);
      if (! $row) {
        unset($this->data['password']);
        $this->echecklistNamespace->flash = 'Either userid or password incorrect';
        $this->makeDialog($this->data);
        return;
      }
      if ($this->data['password'] == $row['password']) { // FIXME - goto BCRYPT
        $xuser = array();
        foreach($row as $a => $b) {
          if ($a != 'password') {
            $xuser [$a] = $b;
            logit ("Added {$a} => {$b}");
          }
        }
        $this->echecklistNamespace->user = $xuser;
        $this->echecklistNamespace->audit = null;
        // $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->_redirector->gotoUrl($this->mainpage);
      } else {
        $this->makeDialog();
        $this->echecklistNamespace->flash = "UserId or password incorrect";
        $this->data['password'] = '';
        $this->makeDialog($this->data);
      }
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
      if ($this->collectData()) return;
      unset($this->data['submit_button']);
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
          $this->_redirector->gotoUrl($this->mainpage);
        } catch (Exception $e) {
          logit("Excep: {$e}". print_r($e, true));
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
    if ($this->usertype != 'ADMIN') {
      $this->echecklistNamespace->flash = 'Unexpected Action';
      $this->_redirector->gotoUrl("/audit/main");
    }
    logit ("{$this->dialog_name}" );
    $user = new Application_Model_DbTable_User();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int)  $pinfo[3];
    $langtag = $this->echecklistNamespace->lang;
    // $urldata = $this->getRequest()->getParams();
    if (!$this->getRequest()->isPost()) {
      $row = $user->getUser($id);
      // logit('LAB: '. print_r($row, true));
      unset($row['password']);
      $this->makeDialog($row);
    } else {
      if ($this->collectData()) return;
      unset($this->data['submit_button']);
      if ($this->data['password'] != $this->data['password2']) {
        $this->data['password'] = '';
        $this->data['password2'] = '';
        $this->echecklistNamespace->flash = "Passwords do not match";
        $this->makeDialog($this->data);
      } else {
        //logit('Data: ' . print_r($this->data, true));
        unset($this->data['password2']);
        if ($this->data['password'] == '')
            unset($this->data['password']);
        try {
          $user->updateData($this->data, $id);
          $this->_redirector->gotoUrl($this->mainpage);
        } catch (Exception $e) {
          logit("Excep: {$e}". print_r($e->getMessage(), true));
          $this->data['password'] = '';
          $this->data['password2'] = '';
          $this->echecklistNamespace->flash = "User Id is already in use";
          $this->makeDialog($this->data);
          return;
        }
      }
    }
  }

  public function findAction() {
    $this->dialog_name = 'user/find';
    logit("{$this->dialog_name}");
    $user = new Application_Model_DbTable_User();
    // $urldata = $this->getRequest()->getParams();
    $langtag = $this->echecklistNamespace->lang;
    if (! $this->getRequest()->isPost()) {
      // logit('LAB: '. print_r($row, true));
      $this->makeDialog();
    } else {
      if ($this->collectData())
        return;
      $rows = $user->getUsersByUsername($this->data['name']);
      logit('Users: ' . print_r($rows, true));
      $this->makeDialog($this->data);
      logit("User lines");
      $this->makeUserLines($rows);
    }
  }

  public function addownerAction() {
    $this->dialog_name = 'user/addowner';
    logit("{$this->dialog_name}");
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int)  $pinfo[3];
    $user = new Application_Model_DbTable_User();
    $ao   = new Application_Model_DbTable_AuditOwner();
    // $urldata = $this->getRequest()->getParams();
    $langtag = $this->echecklistNamespace->lang;
    if (! $this->getRequest()->isPost()) {
      // logit('LAB: '. print_r($row, true));
      logit('AU: '. print_r($this->audit, true));
      $aodata = array (
          'audit_id' => $this->audit['audit_id'],
          'owner' => $id
      );
      try {
        $ao->insertData($aodata);
      } catch (Exception $e) {
        // probably alreay exists
      }
      $this->echecklistNamespace->flash = 'User added a owner to current audit';
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function profileAction() {
    $this->dialog_name = 'user/profile';
    logit ("{$this->dialog_name}" );
    $user = new Application_Model_DbTable_User();
    //$vars = $this->_request->getPathInfo();
    //$pinfo = explode("/", $vars);
    $id = (int)$this->echecklistNamespace->user['id'];
    $langtag = $this->echecklistNamespace->lang;
    // $urldata = $this->getRequest()->getParams();
    if (!$this->getRequest()->isPost()) {
      $row = $user->getUser($id);
      unset($row['password']);
      $this->makeDialog($row);
    } else {
      // display the form here
      if ($this->collectData()) return;
      // logit('Data: ' . print_r($this->data));
      $row = $user->getUser($id);
      if ($this->data['password'] == $row['password']) { // FIXME -use bcrypt
        unset($this->data['password']);
        unset($this->data['id']);
        logit('USER DATA: '. print_r($this->data, true));
        $user->updateData($this->data, $id);
        $this->_redirector->gotoUrl($this->mainpage);
      } else {
        unset($this->data['password']);
        $this->echecklistNamespace->flash = "Incorrect password";
        $this->makeDialog($row);
      }
    }
  }

  public function changepwAction() {
    $this->dialog_name = 'user/changepw';
    logit ("{$this->dialog_name}" );
    $user = new Application_Model_DbTable_User();
    $id = (int)$this->echecklistNamespace->user['id'];
    $langtag = $this->echecklistNamespace->lang;
    // $urldata = $this->getRequest()->getParams();
    if (!$this->getRequest()->isPost()) {

      // logit('LAB: '. print_r($row, true));
      $this->makeDialog();
    } else {
      // display the form here
      if ($this->collectData()) return;
      logit("USERID: {$id}");
      $row = $user->getUser($id);
      if ($this->data['old_pw'] != $row['password']) {
        $this->echecklistNamespace->flash = "Incorrent Password";
        $this->makeDialog();
        return;
      }
      if ($this->data['password'] ==  $this->data['password2']) {

        unset($this->data['password2']);
        unset($this->data['old_pw']);
      // logit('Data: ' . print_r($this->data));
        try {
          $user->updateData($this->data, $id);
          $this->echecklistNamespace->flash = "Password updated";
          $this->_redirector->gotoUrl($this->mainpage);
        } catch (Exception $e) {
          $this->data['password'] = '';
          $this->data['password2'] = '';
          $this->echecklistNamespace->flash = "An unexpected error occurred";
          $this->makeDialog();
          return;
        }
      } else {
        $this->data['password'] = '';
        $this->data['password2'] = '';
        $this->echecklistNamespace->flash = "Passwords do not match";
        $this->makeDialog();
        return;
      }
    }
  }

  /*public function addownerAction() {
    // choose and add ownwer(s) to this audit
    $this->dialog_name = 'user/addowner';
    $vars = $this->_request->getPathInfo();
    //logit("VARS: {$vars}");
    $pinfo = explode("/", $vars);
    //logit('PARTS: '. print_r($pinfo, true));
    $audit_id = (int) $pinfo[3];
    logit("{$this->dialog_name}");
    $user = new Application_Model_DbTable_User();
    // $urldata = $this->getRequest()->getParams();
    $langtag = $this->echecklistNamespace->lang;
    if (! $this->getRequest()->isPost()) {
      // logit('LAB: '. print_r($row, true));
      $this->makeDialog();
    } else {
      if ($this->collectData())
        return;
      $rows = $user->getUsersByUsername($this->data['name']);
      logit('Users: ' . print_r($rows, true));
      $this->makeDialog($this->data);
      $this->makeUserLines($rows);
    }
  }*/

}
