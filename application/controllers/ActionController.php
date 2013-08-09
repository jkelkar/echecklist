<?php

/**
 * This is the super class for our controllers
 *
 * <!--&nbsp;&nbsp;&nbsp;<a class="header" href="/help">Help</a-->
 */
require_once 'modules/Checklist/logger.php';

class Application_Controller_Action extends Zend_Controller_Action {
  
  public $echecklistNamespace;
  public $_redirector = '';
  public $debug = 1;
  public $baseurl = '';
  // user
  public $usertype = '';
  public $username = '';
  public $userfullname = '';
  public $userid = '';

  public function init() {
    /* initialize here */
    $this->echecklistNamespace = new Zend_Session_Namespace ( 'eChecklist' );
    $this->baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
    if (isset ( $this->echecklistNamespace->user )) {
      $u = $this->echecklistNamespace->user;
      $this->usertype = $u ['user_type'];
      $this->username = $u ['username'];
      $this->userfullname = $u ['name'];
      $this->userid = $u ['id'];
      logit("{$this->username}, {$this->usertype}, {$this->userfullname}, {$this->userid}");
    }
    
    Zend_Session::start ();
    /* Remove this when sessions work correctly */
    $this->_redirector = $this->_helper->getHelper ( 'Redirector' );
    
    $dt = date('j M, Y');
    $name_header = '';
    if ($this->usertype != '') {
      $name_header = "Hello, {$this->userfullname}";
    }
    $this->header = <<<"END"
<div id="header">
  <div style="float:left;">APHL Logo </div>
  <div style="font-size:12px;text-decoration:none;float:right;">
    <div style="color:black;display:inline;">
      {$dt} &nbsp;&nbsp;&nbsp;{$name_header}
    </div>&nbsp;&nbsp;&nbsp;

END;
    if ($this->usertype == 'ADMIN') {
      $this->header = $this->header . <<<"END"
			<a class="header" href="{$this->baseurl}/system/admin">Admin</a>&nbsp;&nbsp;&nbsp;

END;
    }
    if ($this->usertype != '') {
      $this->header = $this->header . <<<"END"
			<a class="header" href="{$this->baseurl}/startstop/logout">Logout</a>&nbsp;&nbsp;&nbsp;

END;
    }
    if (in_array ( $this->usertype, array('ADMIN', 'USER', 'APPROVER' ))) {
      $this->header = $this->header . <<<"END"
			<a class="header" href="{$this->baseurl}/io/import">Import</a>&nbsp;&nbsp;&nbsp;

END;
    }
    if ($this->usertype != '') {
      $this->header = $this->header . <<<"END"
			<a class="header" href="{$this->baseurl}/io/export">Export</a>&nbsp;&nbsp;&nbsp;
			<a class="header" href="{$this->baseurl}/lang/languages">Languages</a>&nbsp;&nbsp;&nbsp;
			<a class="header" href="{$this->baseurl}/user/profile">User Profile</a>
END;
    }
    if ($this->usertype == '') {
     $this->header = $this->header . <<<"END"
     <a class="header" href="{$this->baseurl}/startstop/login">Login</a>&nbsp;&nbsp;&nbsp;
END;
    }
    $this->header = $this->header . <<<"END"
			  </div>
  <div style="clear:both;"></div>
</div>
END;

    $this->view->header = $this->header;
    logit("_HEADER: {$this->view->header}");
  }
  
  public function convert2PDF($html)
  {
    require_once 'modules/mpdf56/examples/testmpdf.php';
    html2pdf($html);
    
  }

}