<?php

class UserController extends Checklist_Controller_Action
{

  public function init()
  {
    /* Initialize action controller here */
    parent::init();
  }

  public function loginAction()
  {
    $this->dialog_name = 'user/login';
    $this->log->logit("{$this->dialog_name}");
    $user = new Application_Model_DbTable_User();
    $langtag = $this->session->lang;
    if (! $this->getRequest()->isPost())
    {
      if ($this->usertype != '')
      {
        $this->_redirector->gotoUrl($this->mainpage);
      }
      $this->makeDialog();
    }
    else
    {
      if ($this->collectData())
        return;
      $row = $user->getUserByUsername($this->data['userid']);
      if (! $row)
      {
        unset($this->data['password']);
        $this->session->flash = 'Either userid or password incorrect';
        $this->makeDialog($this->data);
        return;
      }
      if ($this->data['password'] == $row['password'])
      { // FIXME - goto BCRYPT
        $xuser = array();
        foreach($row as $a => $b)
        {
          if ($a != 'password')
          {
            $xuser[$a] = $b;
            $this->log->logit("Added {$a} => {$b}");
          }
        }
        $this->session->user = $xuser;
        $this->session->audit = null;
        $this->_redirector->gotoUrl($this->mainpage);
      }
      else
      {
        $this->makeDialog();
        $this->session->flash = "UserId or password incorrect";
        $this->data['password'] = '';
        $this->makeDialog($this->data);
      }
    }
  }

  public function logoutAction()
  {
    /* logout and clear the user entry from the session */
    unset($this->session->user);
    unset($this->session->lab);
    unset($this->session->audit);
    unset($this->session->lang);

    $this->_redirector->gotoUrl("/user/login");
  }

  public function createAction()
  {
    $this->dialog_name = 'user/create';
    $this->log->logit("{$this->dialog_name}");
    $user = new Application_Model_DbTable_User();
    $langtag = $this->session->lang;
    if (! $this->getRequest()->isPost())
    {
      $this->makeDialog();
    }
    else
    {
      if ($this->collectData())
        return;
      unset($this->data['submit_button']);
      if ($this->data['password'] != $this->data['password2'])
      {
        $this->data['password'] = '';
        $this->data['password2'] = '';
        $this->session->flash = "Passwords do not match";
        $this->makeDialog($this->data);
      }
      else
      {
        unset($this->data['password2']);
        try
        {
          $user->insertData($this->data);
          $this->_redirector->gotoUrl($this->mainpage);
        }
        catch (Exception $e)
        {
          $this->log->logit("Excep: {$e}" . print_r($e, true));
          $this->data['password'] = '';
          $this->data['password2'] = '';
          $this->session->flash = "User Id is already in use";
          $this->makeDialog($this->data);
          return;
        }
      }
    }
  }

  public function editAction()
  {
    $this->dialog_name = 'user/edit';
    if ($this->usertype != 'ADMIN')
    {
      $this->session->flash = 'Unexpected Action';
      $this->_redirector->gotoUrl("/audit/main");
    }
    $this->log->logit("{$this->dialog_name}");
    $user = new Application_Model_DbTable_User();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int) $pinfo[3];
    $langtag = $this->session->lang;
    if (! $this->getRequest()->isPost())
    {
      $row = $user->getUser($id);
      unset($row['password']);
      $this->makeDialog($row);
    }
    else
    {
      if ($this->collectData())
        return;
      unset($this->data['submit_button']);
      if ($this->data['password'] != $this->data['password2'])
      {
        $this->data['password'] = '';
        $this->data['password2'] = '';
        $this->session->flash = "Passwords do not match";
        $this->makeDialog($this->data);
      }
      else
      {
        unset($this->data['password2']);
        if ($this->data['password'] == '')
          unset($this->data['password']);
        try
        {
          $user->updateData($this->data, $id);
          $this->_redirector->gotoUrl($this->mainpage);
        }
        catch (Exception $e)
        {
          $this->log->logit("Excep: {$e}" . print_r($e->getMessage(), true));
          $this->data['password'] = '';
          $this->data['password2'] = '';
          $this->session->flash = "User Id is already in use";
          $this->makeDialog($this->data);
          return;
        }
      }
    }
  }

  public function findAction()
  {
    $this->dialog_name = 'user/find';
    $this->log->logit("{$this->dialog_name}");
    $user = new Application_Model_DbTable_User();
    $langtag = $this->session->lang;
    if (! $this->getRequest()->isPost())
    {
      $this->makeDialog();
    }
    else
    {
      if ($this->collectData())
        return;
      $rows = $user->getUsersByUsername($this->data['name']);
      $this->log->logit('Users: ' . print_r($rows, true));
      $this->makeDialog($this->data);
      $this->log->logit("User lines");
      $this->makeUserLines($rows);
    }
  }

  public function addownerAction()
  {
    $this->dialog_name = 'user/addowner';
    $this->log->logit("{$this->dialog_name}");
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int) $pinfo[3];
    $user = new Application_Model_DbTable_User();
    $ao = new Application_Model_DbTable_AuditOwner();
    if (! $this->getRequest()->isPost())
    {
      $this->log->logit('AU: ' . print_r($this->audit, true));
      $aodata = array(

          'audit_id'=> $this->audit['audit_id'],
          'owner'=> $id
      );
      try
      {
        $ao->insertData($aodata);
      }
      catch (Exception $e)
      {
        // probably alreay exists
      }
      $this->session->flash = 'User added a owner to current audit';
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function profileAction()
  {
    $this->dialog_name = 'user/profile';
    $this->log->logit("{$this->dialog_name}");
    $user = new Application_Model_DbTable_User();
    $id = (int) $this->session->user['id'];
    $langtag = $this->session->lang;
    if (! $this->getRequest()->isPost())
    {
      $row = $user->getUser($id);
      unset($row['password']);
      $this->makeDialog($row);
    }
    else
    {
      // display the form here
      if ($this->collectData())
        return;
      $row = $user->getUser($id);
      if ($this->data['password'] == $row['password'])
      { // FIXME -use bcrypt
        unset($this->data['submit_button']);
        unset($this->data['password']);
        unset($this->data['id']);
        $this->log->logit('USER DATA: ' . print_r($this->data, true));
        $user->updateData($this->data, $id);
        $row = $user->getUserByUsername($this->data['userid']);
        $xuser = array();
        foreach($row as $a => $b)
        {
          if ($a != 'password')
          {
            $xuser[$a] = $b;
            $this->log->logit("Added {$a} => {$b}");
          }
        }
        $this->session->user = $xuser;
        $this->_redirector->gotoUrl($this->mainpage);
      }
      else
      {
        unset($this->data['password']);
        $this->session->flash = "Incorrect password";
        $this->makeDialog($this->data);
      }
    }
  }

  public function changepwAction()
  {
    $this->dialog_name = 'user/changepw';
    $this->log->logit("{$this->dialog_name}");
    $user = new Application_Model_DbTable_User();
    $id = (int) $this->session->user['id'];
    $langtag = $this->session->lang;
    if (! $this->getRequest()->isPost())
    {
      $this->makeDialog();
    }
    else
    {
      // display the form here
      if ($this->collectData())
        return;
      $this->log->logit("USERID: {$id}");
      $row = $user->getUser($id);
      if ($this->data['password'] != $this->data['password2'])
      {
        $this->data['password'] = '';
        $this->data['password2'] = '';
        $this->session->flash = "Passwords do not match";
        $this->makeDialog();
        return;
      }
      if ($this->data['old_pw'] != $row['password'])
      {
        $this->session->flash = "Incorrect Password";
        $this->makeDialog();
        return;
      }
      if ($this->data['password'] == $this->data['password2'])
      {

        unset($this->data['password2']);
        unset($this->data['old_pw']);
        unset($this->data['submit_button']);
        try
        {
          $user->updateData($this->data, $id);
          $this->session->flash = "Password updated";
          $this->_redirector->gotoUrl($this->mainpage);
        }
        catch (Exception $e)
        {
          $this->log->logit('EX TR: ' . var_dump($e->getTrace()));
          $this->data['password'] = '';
          $this->data['password2'] = '';
          $this->session->flash = "An unexpected error occurred";
          $this->makeDialog();
          return;
        }
      }
    }
  }
}
