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
        //'/css/styles.css',
        '/css/dtree.css',
        // charisma starts below
        '/charisma/css/bootstrap-cerulean.css',
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
        '/charisma/css/opa-icons.css',
        '/css/echecklist-styles.css'
    );
    foreach ( $csslist as $f ) {
      $this->view->headLink ()->appendStylesheet ( "{$this->baseurl}{$f}" );
    }
    $jslist = array (
        '/js/dtree.js',
        '/charisma/js/jquery-1.7.2.min.js',
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
        '/charisma/js/charisma.js',
        '/js/helpers.js'
        // '/js/helpers.js'
    );
    foreach ( $jslist as $f ) {
      $this->view->headScript ()->appendFile ( "{$this->baseurl}{$f}" );
    }
    /*
    logit ( "Links: {$this->view->headLink()}" );
    logit ( "Scripts: {$this->view->headScript()}" );
    */
  }

  public function makeIcon($name, $color='', $size='') {
    return "<span title=\".icon{$size}  .icon-{$color} .icon-{$name} \" class=\"icon{$size} icon-{$color} icon-{$name}\"></span>";
  }

  public function makeMenu($menu) {
    /*
     * The input is an array of arrays
     * array(top, array(array(icon, item),))+
     *
     * The top level creates buttons the rest create the menu items
     *
     */
    $out = array();
    $out[] = "<div class=\"btn-group pull-left\">";
    
    foreach($menu as $mx)  {
      $i = 0;
      foreach($mx as $m) {
          $i++;
          switch($i) {
          case 1:
            $icon = $m['icon'];
            $out[] = "<a class=\"btn dropdown-toggle\" data-toggle=\"dropdown\" href=\"{$m['url']}\">";
            $out[] = $this->makeIcon($icon[0], $icon[1]);
            $out[] = "<span class=\"hidden-phone\">{$m['text']}</span><span class=\"caret\"></span></a>";
            $out[] = "<ul class=\"dropdown-menu\">";
            break;
          default:
            foreach ($m as $mi) {
              if (in_array('divider', $mi)) { 
                $out[] = '<li class="divider"></li>';
                continue;
              }
              $icon = $mi['icon'];
              $out[] = "<li><a href=\"{$this->baseurl}{$mi['url']}\">".
                $this->makeIcon($icon[0], $icon[1]).
                " {$mi['text']}</a></li>";
            }
          }
          $out[] = "</ul></div>";
        }
    }
    return implode("\n", $out);
  } 

  
  public function setHeader() {
    /* Create the top line */
    $dt = date('j M, Y');
    $name_header = '';
    if ($this->usertype != '') {
      $name_header = "&nbsp {$this->userfullname}";
    }
    $this->header = <<<"END"
<div class="navbar">
  <div class="navbar-inner">
		<div class="container-fluid">
			<a class="brand" href="index.html"> 
        <span title=".icon  .icon-black  .icon-check " class="icon icon-black icon-check"></span> <span>eChecklist</span>
      </a>
    
				   
END;
    if ($this->usertype != '') {
      $this->header = $this->header . <<<"END"
  <div class="btn-group pull-left" style="margin-left:100px;">
<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
<span title=".icon  .icon-blue .icon-clipboard " class="icon icon-blue icon-clipboard"></span>
        <span class="hidden-phone">Audits</span>
<span class="caret"></span></a>
<ul class="dropdown-menu">
        <li><a href="{$this->baseurl}/audit/start"><span title=".icon  .icon-green .icon-clipboard " class="icon icon-green icon-clipboard"></span> New Audit</a></li>
        <li><a href="{$this->baseurl}/audit/find"><span title=".icon  .icon-blue  .icon-search " class="icon icon-blue icon-search"></span> Find</a></li>
        <li class="divider"></li>
<li><a href="{$this->baseurl}/audit/edit/1/EN/"><span title=".icon  .icon-blue  .icon-edit " class="icon icon-blue icon-edit"></span> Edit 1</a></li>
<li><a href="{$this->baseurl}/audit/edit/2/EN/"><span title=".icon  .icon-blue  .icon-edit " class="icon icon-blue icon-edit"></span> Edit 2</a></li>
<li><a href="{$this->baseurl}/audit/edit/3/EN/"><span title=".icon  .icon-blue  .icon-edit " class="icon icon-blue icon-edit"></span> Edit 3</a></li>
        <li class="divider"></li>
        <li><a href="{$this->baseurl}/audit/import"><span title=".icon  .icon-blue .icon-import " class="icon icon-blue icon-archive"></span> Import</a></li>
				</ul>
</div>

  <div class="btn-group pull-left">
<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
  <span title=".icon  .icon-blue  .icon-tag " class="icon icon-blue icon-tag"></span>
  <span class="hidden-phone">Labs</span>
  <span class="caret"></span></a>
<ul class="dropdown-menu">
					<li><a href="{$this->baseurl}/lab/create"><span title=".icon  .icon-green  .icon-tag " class="icon icon-green icon-tag"></span> New Lab</a></li>
					<li><a href="{$this->baseurl}/lab/find"><span title=".icon  .icon-blue  .icon-search " class="icon icon-blue icon-search"></span> Find Lab</a></li>
				</ul>
</div>

  <div class="btn-group pull-left">
<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
<span title=".icon  .icon-blue  .icon-user " class="icon icon-blue icon-user"></span>
<span class="hidden-phone">Users</span>
<span class="caret"></span></a>
<ul class="dropdown-menu">
					<li><a href="{$this->baseurl}/user/create"><span title=".icon  .icon-green  .icon-user " class="icon icon-green icon-user"></span> New User</a></li>
					<li><a href="{$this->baseurl}/user/find"><span title=".icon  .icon-blue  .icon-search " class="icon icon-blue icon-search"></span>Find User</a></li>
				</ul>
</div>
    
<div class="btn-group pull-left">
<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
<span title=".icon  .icon-blue  .icon-flag " class="icon icon-blue icon-flag"></span>
<span class="hidden-phone">Language</span>
<span class="caret"></span></a>
<ul class="dropdown-menu">
					<li><a href="{$this->baseurl}/language/switch/EN"><span title=".icon  .icon-green  .icon-flag " class="icon icon-green icon-flag"></span> English</a></li>
					<li><a href="{$this->baseurl}/language/switch/FR"><span title=".icon  .icon-green  .icon-flag " class="icon icon-green icon-flag"></span> French</a></li>
					<li><a href="{$this->baseurl}/language/switch/VI"><span title=".icon  .icon-green  .icon-flag " class="icon icon-green icon-flag"></span> Vietnamese</a></li>
				</ul>
</div>
END;
    } else {
      $this->header = $this->header . <<<"END"
  <div class="btn-group pull-left" style="margin-left:200px;">
<a class="btn" href="{$this->baseurl}/startstop/login"><span title=".icon  .icon-blue  .icon-contacts " class="icon icon-blue icon-contacts"></span> Login</a></div>

END;
    }
    $this->header = $this->header . <<<"END"

     
    <!-- user dropdown starts -->
			<div class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
          <span title=".icon  .icon-orange  .icon-user " class="icon icon-orange icon-user"></span>
          <span class="hidden-phone"> {$name_header}</span>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li><a href="{$this->baseurl}/user/profile"><span title=".icon  .icon-blue  .icon-contacts " class="icon icon-blue icon-contacts"></span> Profile</a></li>
					<li class="divider"></li>
					<li><a href="{$this->baseurl}/startstop/logout">Logout</a></li>
				</ul>
			</div>
			<!-- user dropdown ends -->
                                                        <div class="btn-group pull-right" style="top:6px;font-size:16px;">
      {$dt}
      </div> 
   </div>
  </div> <!-- style="clear:both;"></div -->
</div>
END;
    
    $this->view->header = $this->header;
    /*
    // logit("_HEADER: {$this->view->header}");
    // this is only a test!
    $menu = array(
                  array(
                        array("icon"=>array('clipboard', 'blue'), 'text'=> 'Audits', 'url'=>'#'
                              ),
                        array(
                              array("icon"=>array('clipboard', 'green'), 'text'=> 'New Audit', 'url'=>'/user/create'),
                              array("icon"=>array('search', 'blue'), 'text'=> 'Find Audit', 'url'=>'/user/find'),
                              array('divider' => true),
                              array("icon"=>array('edit', 'blue'), 'text'=> 'Edit Audit 1', 'url'=>'/audit/edit/1/EN/'),
                              array("icon"=>array('edit', 'blue'), 'text'=> 'Edit Audit 2', 'url'=>'/audit/edit/2/EN/'),
                              array("icon"=>array('edit', 'blue'), 'text'=> 'Edit Audit 3', 'url'=>'/audit/edit/3/EN/')
                              array('divider' => true),
                              array("icon"=>array('import', 'blue'), 'text'=> 'Import', 'url'=>'/audit/import'),
                              )
                        ),
                  array(
                        array("icon"=>array('tag', 'blue'), 'text'=> 'Labs', 'url'=>'#'
                              ),
                        array(
                              array("icon"=>array('tag', 'green'), 'text'=> 'New Lab', 'url'=>'/lab/create'),
                              array("icon"=>array('search', 'blue'), 'text'=> 'Find Lab', 'url'=>'/lab/find')
                              )
                        
                        ),
                  array(
                        array("icon"=>array('user', 'blue'), 'text'=> 'Users', 'url'=>'#'
                              ),
                        array(
                              array("icon"=>array('user', 'green'), 'text'=> 'New User', 'url'=>'/user/create'),
                              array("icon"=>array('search', 'blue'), 'text'=> 'Find User', 'url'=>'/user/find')
                              )
                        )
                  );
    logit('MENU: '. $this->makeMenu($menu)); 
    $lin = array(
                  array(
                        array("icon"=>array('user', 'orange'), 'text'=> 'This user', 'url'=>'#'
                              ),
                        array(
                              array("icon"=>array('contacts', 'green'), 'text'=> 'Profile', 'url'=>'/user/profile'),
                              array('divider' => true),
                              array("icon"=>array('contacts', 'orange'), 'text'=> 'Logout', 'url'=>'/startstop/logout')
                              )
                        )
                 );
    logit('MENU: '. $this->makeMenu($menu)); */
  }

  public function convert2PDF($html)
  {
    require_once 'modules/mpdf56/examples/testmpdf.php';
    html2pdf($html);
    
  }

}