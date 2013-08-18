<?php

/**
 * This is the super class for our controllers
 *
 * <!--&nbsp;&nbsp;&nbsp;<a class="header" href="/help">Help</a-->
 */
require_once 'modules/Checklist/logger.php';
require_once 'modules/Checklist/fillout.php';

class Application_Controller_Action extends Zend_Controller_Action {
  
  public $echecklistNamespace;
  public $_redirector = '';
  public $debug = 1;
  public $baseurl = '';
  // user
  public $langtag;
  public $usertype = '';
  public $username = '';
  public $userfullname = '';
  public $userid = '';
  public $dialog_name;
  public $title;
  public $data;
  public $lab;
  public $labname;
  public $audit;
  public $showaudit;
  public $drows;
  public $mainpage = '/audit/main';
  public $loginpage = '/user/login';
  public $tlist;

  public function init() {
    /* initialize here */
    $this->setupSession();
    $this->baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
    $this->_redirector = $this->_helper->getHelper ( 'Redirector' );
    $this->setHeader();
    $this->setHeaderFiles();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    logit('PINFO: '. print_r($pinfo, true));
    if (!isset($this->echecklistNamespace->user) && !($pinfo[1] == 'user' && $pinfo[2] == 'login')) {
      $this->_redirector->gotoUrl($this->loginpage);
      }
    $this->getTwords();
  }
  
  public function getTwords() {
    // Get the translations for common words
    $this->tlist = getTranslatables ( $this->langtag);
  }

  public function setupSession() {
    /* start the session */
    $this->echecklistNamespace = new Zend_Session_Namespace ( 'eChecklist' );
    if (isset ( $this->echecklistNamespace->user )) {
      $u = $this->echecklistNamespace->user;
      $this->usertype = $u['usertype'];
      $this->username = $u['userid'];
      $this->userfullname = $u['name'];
      $this->userid = $u['id'];
      logit ( "{$this->username}, {$this->usertype}, {$this->userfullname}, {$this->userid}" );
    }
    if (isset ($this->echecklistNamespace->lab)) {
      $this->lab = $this->echecklistNamespace->lab;
      $this->labname = $this->lab['labname'];
    } else {
      $this->lab = null;
      $this->labname = '';
    }
    if (!isset ($this->echecklistNamespace->lang)) {
      $this->echecklistNamespace->lang = 'EN';
    }
    if (isset ($this->echecklistNamespace->audit)) {
      $this->audit = $this->echecklistNamespace->audit;
      $this->showaudit = "{$this->audit['tag']} - #{$this->audit['id']} - {$this->audit['labname']}";
      // / {$this->audit['updated_at']}";
    } else {
      $this->audit = null;
      $this->showaudit = '';
    }
    $this->view->langtag = $this->echecklistNamespace->lang;
    $this->langtag = $this->echecklistNamespace->lang;
    // logit('LT: '. $this->view->langtag);
    Zend_Session::start ();
    
  }
  
  public function setHeaderFiles() {
    /* all CSS and js files are set up here */
    $csslist = array (
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
      $this->view->headLink()->appendStylesheet ("{$this->baseurl}{$f}");
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
      $this->view->headScript()->appendFile ("{$this->baseurl}{$f}");
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
			<a class="brand" href="{$this->baseurl}{$this->mainpage}"> 
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
        <!--li class="divider"></li>
<li><a href="{$this->baseurl}/audit/edit/1/"><span title=".icon  .icon-blue  .icon-edit " class="icon icon-blue icon-edit"></span> Edit 1</a></li>
<li><a href="{$this->baseurl}/audit/edit/2/"><span title=".icon  .icon-blue  .icon-edit " class="icon icon-blue icon-edit"></span> Edit 2</a></li>
<li><a href="{$this->baseurl}/audit/edit/3/"><span title=".icon  .icon-blue  .icon-edit " class="icon icon-blue icon-edit"></span> Edit 3</a></li-->
        <li class="divider"></li>
        <li><a href="{$this->baseurl}/audit/select"><span title=".icon  .icon-blue  .icon-search " class="icon icon-blue icon-search"></span> Export to Excel</a></li>
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

<!-- user dropdown starts -->
			<div class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
          <span title=".icon  .icon-orange  .icon-user " class="icon icon-orange icon-user"></span>
          <span class="hidden-phone"> {$name_header}</span>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li><a href="{$this->baseurl}/user/profile"><span title=".icon  .icon-blue  .icon-contacts " class="icon icon-blue icon-contacts"></span> Profile</a></li>
          <li><a href="{$this->baseurl}/user/changepw"><span title=".icon  .icon-blue  .icon-contacts " class="icon icon-blue icon-contacts"></span> Change Password</a></li>
					<li class="divider"></li>
					<li><a href="{$this->baseurl}/user/logout">Logout</a></li>
				</ul>
			</div>
			<!-- user dropdown ends -->
END;
      $this->header .= <<<"END"
<div style="display:inline-block;">
  <div style="margin:6px 0 6px 20px;"><b>Audit:</b> {$this->showaudit}</div>
  <div style="margin:6px 0px 6px 20px;padding-right:5px;"><b>Lab:</b> {$this->labname}</div>
  <div style="clear:both;"></div></div>
END;
    } else {
      $this->header = $this->header . <<<"END"
  <div class="btn-group pull-left" style="margin-left:100px;">
<a class="btn" href="{$this->baseurl}/user/login"><span title=".icon  .icon-blue  .icon-contacts " class="icon icon-blue icon-contacts"></span> Login</a></div>

END;
    }
    $this->header = $this->header . <<<"END"
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

  function calculate_dialog($drows, $value, $title, $langtag, $formtype='table') { 
    /**
     * Given the dialog rows, create the dialog
     * - using field templates to create individual rows
     */
    
    $tlist = getTranslatables ( $langtag); 
    
    $tout = array ();
    $baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
    $title = $drows[0]['title'];
    $tout[] = <<<"END"
<div style="margin-left:200px;"><h1 style="margin-bottom:10px;">{$title}
<button onclick="return toggleHelp();">Help</button> </h1> </div>
<div style="margin:15px 0;">
END;
    $tout [] = '<table border=0 style="width:900px;">';
    
    $hid = array();
    foreach ( $drows as $row ) {
      $pos = $row['position'];
      //if ($pos ==0) continue;
      $type = $row ['field_type'];
      $arow = array ();
      
      $field_label = get_lang_text($row['field_label'], '', '') ; //, $row ['ltdefault'], $row ['ltlang'] );
      $arow['field_label'] = $field_label . ':';
      $arow['varname'] = $row ['field_name'];
      $varname = $arow['varname'];
      $arow['baseurl'] = $baseurl;
      $arow['field_length'] = $row['field_length'];
      $info = $row['info'];
      
      switch ($type) {
      case '':
        logit("ROW: ".print_r($row, true));
        break;
      case 'hidden':
        $val = get_arrval($value, $varname, '');
        $hid[] = "<input type=\"hidden\" name=\"{$varname}\" value=\"{$val}\">";
        break;
      case 'info':
        $tout[] = <<<"END"
<tr>
<td class="n f right" style=width:200px;">
<td class="n f" style=width:600px;"><div id="help">{$info}</div></td>
</tr>
END;
        break;
      case 'heading':
        $tout[] = <<<"END"
<tr>
<td class="n f right" style=width:50px;">
<td class="n f" style=width:750px;"><h3>{$field_label}</h3></td>
</tr>
END;
        break;
      case 'submit_button':
        $arow['field_label'] = $field_label;
        $field_label = '';
      default:
        $inp = call_user_func("dialog_{$type}", $arow, $value, $tlist);
        $tout[] = <<<"END"
<tr>
<td class="n f right" style=width:300px;">
<label for="{$varname}" style="" class="inp">{$field_label}</label>
</td><td class="n f" style=width:500px;">{$inp}</td>
</tr>
END;
      }
    }
    $tout[] = '</table></div></div>';
    $tout[] = implode("\n", $hid);
    //logit('dialog: '. print_r($tout, true));
    return implode("\n", $tout);
  }


  public function getDialogLines() {
    /*
     * Get the DialogRow data
     */
    $dialog = new Application_Model_DbTable_DialogRow();
    $this->drows = $dialog->getDialogRows($this->dialog_name);
  }

  public function makeDialog($value=array(''=>'')) {
    /*
     * Create the dialog
     */
    $this->getDialogLines();
    // logit('makeDialog:');
    $this->view->outlines = $this->calculate_dialog($this->drows, $value, $this->title, $this->view->langtag);
    $this->view->title = $this->title;
    $this->_helper->layout->setLayout('overall');
    $this->view->flash = $this->echecklistNamespace->flash;
    $this->echecklistNamespace->flash = '';
  }

  public function collectData() {
    /*
     * Collect all the post data
     */
    $dialog = new Application_Model_DbTable_DialogRow();

    $this->getDialogLines();
    $ignore_list = array('', 'submit_button');
    $this->data = array();
    $formData = $this->getRequest();
    //logit('POST: '. print_r($formData->getPost(), true));
    //logit('GET : '. print_r($formData->getQuery(), true));
    //logit('PARA: '. print_r($formData->getParam('country'), true));
    //logit('FORM: '. print_r($this->getRequest()->getPost(), true));
    foreach ($this->drows as $row) {
      if ($row['position'] == 0) continue;
      $type = $row['field_type'];
      $varname = $row['field_name'];
      
      if (in_array($type, $ignore_list)) {
        continue;
      }
      //logit('IN: '. $formData->getPost($varname,''));
      $this->data[$varname] = $formData->getPost($varname,'');
      /*
        if (key_exists($varname, $this->data)) {
        logit('Keyexists: '. $varname . ' ' . print_r($this->data, true));
          if (!is_array($this->data[$varname])) {
            $this->data[$varname] = array($this->data[$varname]);
            logit('arr: '.print_r($this->data[$varname], true));
          }
          $this->data[$varname][] = $formData->getPost($varname,'');
          logit('arr: '.print_r($this->data[$varname], true));
        } else {
          $this->data[$varname] = $formData->getPost($varname,'');
          logit('one: '. $formData->getPost($varname,''));
        }
      */
    }
  }

  public function makeLabLines($rows, $cb=false) {
    // Given lab rows - show in a table
    $rev_level = rev('getLevels', $this->tlist);
    $rev_affil = rev('getAffiliations', $this->tlist);
    $ct = 0;
    $tout = array();
    $tout[] = '<table style="width:900px;margin-left:200px;">';
    $tout[] = "<tr class='even'>";
    if ($cb) {
      $tout[] = "<td style='width:40px;'>Select</td>";
    } else {
      $tout[] = "<td style='width:40px;'></td>";
    }
    $tout[] = "<td style='width:150px;font-weight:bold;'>Labname</td>" .
      "<td style='width:80px;font-weight:bold;'>Lab Number</td>" .
      "<td style='width:85px;font-weight:bold;'>Country</td>" .
      "<td style='width:45px;font-weight:bold;'>SLMTA</td>" .
      "<td style='width:100px;font-weight:bold;'>Level</td>" .
      "<td style='width:125px;font-weight:bold;'>Affiliation</td></tr>";
    foreach($rows as $row) {
      $ct++;
      $cls = ($ct%2 == 0) ? 'even' : 'odd';
      
      $tout[] = "<tr class='{$cls}'>";
      if ($cb) {
        $name = "cb_{$row['id']}";
        $tout[] = "<td style='width:40px;padding:2px 0;'>" .
          "<input type='checkbox' name='{$name}' id='{$name}'></td>";
      } else {
        $butt = "<a href=\"{$this->baseurl}/lab/choose/{$row['id']}\"" .
        " class=\"btn btn-mini btn-success\">Select</a>";
        $tout[] = "<td style='width:40px;padding:2px 0;'>{$butt}</td>";
      }
      $sl = ($row['slmta'] == 't') ? 'Yes' : 'No';
      $tout[] = "<td>{$row['labname']}</td><td>{$row['labnum']}</td>" . 
        "<td>{$row['country']}</td><td>{$sl}</td>" .
        "<td>{$rev_level[$row['lablevel']]}</td><td>{$rev_affil[$row['labaffil']]}</td></tr>";
    }
    $tout[] = '</table>';
    $this->view->outlines .= implode("\n", $tout);
  }

  public function makeAuditLines($rows, $cb=false) {
    // Given audit rows - show in a table
    $tout = array();
    $ct = 0;
    $tout[] = '<table style="width:900px;margin-left:200px;">';
    $tout[] = "<tr class='even'>";
    
    if ($cb) {
      $tout[] = "<td style='width:40px;'>Select</td>";
    } else {
      /*$tout[] = "<td style='width:40px;'></td>";*/
    }
    $tout[] = "<td style='width:50px;font-weight:bold;'>Id</td>" .
      "<td style='width:80px;font-weight:bold;'>Type</td>" .
      "<td style='width:120px;font-weight:bold;'>Updated</td>" .
      "<td style='width:100px;font-weight:bold;'>Labnum</td>" .
      "<td style='width:150px;font-weight:bold;'>Labname</td>" .
      "<td style='width:300px;font-weight:bold;'></td></tr>";
    foreach($rows as $row) {
      $ct++;
      $cls = ($ct%2 == 0) ? 'even' : 'odd';
      $edit = "<a href=\"{$this->baseurl}/audit/edit/{$row['audit_id']}/\"" .
        " class=\"btn btn-mini btn-inverse\">Edit</a>";
      $view = "<a href=\"#\" class=\"btn btn-mini btn-success\">View</a>";
      $delete = "<a href=\"#\" class=\"btn btn-mini btn-danger\">Delete</a>";
      $export = "<a href=\"#\" class=\"btn btn-mini btn-warning\">Data Export</a>";

      $tout[] = "<tr class='{$cls}'>";
      if ($cb) {
        $name = "cb_{$row['audit_id']}";
        $tout[] = "<td style='width:40px;padding:2px 0;'>" .
          "<input type='checkbox' name='{$name}' id='{$name}'></td>";
      } else {
        /*$butt = "<a href=\"{$this->baseurl}/lab/choose/{$row['audit_id']}\"" .
        " class=\"btn btn-mini btn-success\">Select</a>";
        $tout[] = "<td style='width:40px;padding:2px 0;'>{$butt}</td>";
        */
      }
      $tout[] = "<td>{$row['audit_id']}</td><td>{$row['tag']}</td>" . 
        "<td>{$row['updated_at']}</td><td>{$row['labnum']}</td>" .
        "<td>{$row['labname']}</td><td>{$view} {$edit} {$delete} {$export}</td></tr>";
    }
    $tout[] = '</table>';
    $this->view->outlines .= implode("\n", $tout);
  }
  /*
    public function convert2PDF($html)
    {
    require_once 'modules/mpdf56/examples/testmpdf.php';
    html2pdf($html);
    
    }
  */

}