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
    $this->setupSession();
    $this->baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
    /*$this->echecklistNamespace = new Zend_Session_Namespace ( 'eChecklist' );
    
    if (isset ( $this->echecklistNamespace->user )) {
      $u = $this->echecklistNamespace->user;
      $this->usertype = $u ['user_type'];
      $this->username = $u ['username'];
      $this->userfullname = $u ['name'];
      $this->userid = $u ['id'];
      logit("{$this->username}, {$this->usertype}, {$this->userfullname}, {$this->userid}");
    }
    
    Zend_Session::start ();*/
    /* Remove this when sessions work correctly */
    $this->_redirector = $this->_helper->getHelper ( 'Redirector' );
    $this->setHeader();
    $this->setHeaderFiles();
  }
  
  public function setupSession() {
    /* start the session */
    $this->echecklistNamespace = new Zend_Session_Namespace ( 'eChecklist' );
    if (isset ( $this->echecklistNamespace->user )) {
      $u = $this->echecklistNamespace->user;
      $this->usertype = $u ['user_type'];
      $this->username = $u ['username'];
      $this->userfullname = $u ['name'];
      $this->userid = $u ['id'];
      logit ( "{$this->username}, {$this->usertype}, {$this->userfullname}, {$this->userid}" );
    }
    Zend_Session::start ();
    
  }
  
  public function setHeaderFiles() {
    /* all CSS and js files are set up here */
    $csslist = array (
        '/css/styles.css',
        '/css/dtree.css',
        // charisma starts below
        '/charisma/css/bootstrap-responsive.css',
        '/charisma/css/charisma-app.css',
        '/charisma/css/jquery-ui-1.8.21.custom.css',
        '/charisma/css/fullcalendar.css',
        '/charisma/css/chosen.css',
        '/charisma/css/uniform.default.css',
        '/charisma/css/jquery.noty.css',
        '/charisma/css/noty_theme_default.css',
        '/charisma/css/elfinder.min.css',
        '/charisma/css/elfinder.theme.css',
        '/charisma/css/opa-icons.css'
    );
    foreach ( $csslist as $f ) {
      $this->view->headLink ()->appendStylesheet ( "{$this->baseurl}{$f}" );
    }
    $jslist = array (
        // '/js/dtree.js',
        '/charisma/js/jquery-1.7.2.min.js',
        //'/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js',
        '/charisma/js/jquery-ui-1.8.21.custom.min.js',
        '/charisma/js/bootstrap-transition.js',
        '/charisma/js/bootstrap-alert.js',
        '/charisma/js/bootstrap-modal.js',
        '/charisma/js/bootstrap-dropdown.js',
        '/charisma/js/bootstrap-scrollspy.js',
        '/charisma/js/bootstrap-tab.js',
        '/charisma/js/bootstrap-tooltip.js',
        '/charisma/js/bootstrap-popover.js',
        '/charisma/js/bootstrap-button.js',
        '/charisma/js/bootstrap-collapse.js',
        '/charisma/js/bootstrap-carousel.js',
        '/charisma/js/bootstrap-typeahead.js',
        '/charisma/js/jquery.cookie.js',
        '/charisma/js/fullcalendar.min.js',
        '/charisma/js/jquery.dataTables.min.js',
        '/charisma/js/jquery.chosen.min.js',
        '/charisma/js/jquery.uniform.min.js',
        '/charisma/js/jquery.colorbox.min.js',
        '/charisma/js/jquery.noty.js',
        '/charisma/js/jquery.elfinder.min.js',
        '/charisma/js/jquery.raty.min.js',
        '/charisma/js/jquery.autogrow-textarea.js',
        '/charisma/js/jquery.history.js',
        '/charisma/js/charisma.js'  // ,
        //'/js/helpers.js'
        // '/js/helpers.js'
    );
    foreach ( $jslist as $f ) {
      $this->view->headScript ()->appendFile ( "{$this->baseurl}{$f}" );
    }
    logit ( "Links: {$this->view->headLink()}" );
    logit ( "Scripts: {$this->view->headScript()}" );
  
  }
  public function setHeader() {
    /* Create the top line */
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