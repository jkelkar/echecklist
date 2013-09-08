<?php

/**
 * This is the super class for our controllers
 *
 *
 */
require_once 'modules/Checklist/general.php';
require_once 'modules/Checklist/logger.php';
//require_once 'modules/Checklist/fillout.php';
require_once 'modules/Checklist/validation.php';

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
  public $extra;
  public $lab;
  public $labid;
  public $labnum;
  public $labname;
  public $audit;
  public $showaudit;
  public $drows;
  public $mainpage = '/audit/main';
  public $loginpage = '/user/login';
  public $ISOdtformat = 'Y-m-d H:i:s';
  public $ISOformat = 'Y-m-d';
  public $tlist;
  public $error;

  public function init() {
    /* initialize here */
    logit("MT act init: " . microtime(true));
    $this->baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $this->_redirector = $this->_helper->getHelper('Redirector');

    $this->setupSession();
    logit("CHECK: {$this->labid}, {$this->labname}, {$this->labnum}");
    $this->setHeader();
    $this->setHeaderFiles();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    logit('PINFO: ' . print_r($pinfo, true));
    if (! isset($this->echecklistNamespace->user) && ! ($pinfo[1] == 'user' && $pinfo[2] == 'login')) {
      $this->_redirector->gotoUrl($this->loginpage);
    }
    $this->getTwords();
  }

  public function handleCancel() {
    // cancel action: take user to main screen
    logit('HC: ' . print_r($this->data, true));
    if ($this->data['submit_button'] == 'Cancel') {
      $this->data = array();
      $this->error = array();
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function getTwords() {
    // Get the translations for common words
    $this->tlist = getTranslatables($this->langtag);
  }

  public function setupSession() {
    /* start the session */
    $this->echecklistNamespace = new Zend_Session_Namespace('eChecklist');
    logit("test: {$this->echecklistNamespace->lab['labnum']}");
    if (isset($this->echecklistNamespace->user)) {
      $u = $this->echecklistNamespace->user;
      $this->usertype = $u['usertype'];
      $this->username = $u['userid'];
      $this->userfullname = $u['name'];
      $this->userid = $u['id'];
      logit("{$this->username}, {$this->usertype}, {$this->userfullname}, {$this->userid}");
    }
    logit('TIME1: ' . isset($this->echecklistNamespace->lab));
    if (isset($this->echecklistNamespace->lab)) {

      $this->lab = $this->echecklistNamespace->lab;
      logit('ECLAB: ' . print_r($this->lab, true));
      $this->labid = $this->lab['id'];
      $this->labname = $this->lab['labname'];
      $this->labnum = get_arrval($this->lab, 'labnum', 'No-NUM');
    } else {
      $this->lab = null;
      $this->labid = 0;
      $this->labname = '';
      $this->labnum = '';
    }
    if (! isset($this->echecklistNamespace->lang)) {
      $this->echecklistNamespace->lang = 'EN';
    }
    if (isset($this->echecklistNamespace->audit)) {
      // logit('AUEC: '. print_r($this->echecklistNamespace->audit, true));
      $this->audit = $this->echecklistNamespace->audit;
      $this->showaudit = "{$this->audit['tag']} - #{$this->audit['audit_id']}" .
           "- {$this->audit['labname']}";
      logit('ec audit: ' . print_r($this->audit, true));
      // / {$this->audit['updated_at']}";
    } else {
      $this->audit = null;
      $this->showaudit = '';
    }
    $this->view->langtag = $this->echecklistNamespace->lang;
    $this->langtag = $this->echecklistNamespace->lang;
    // logit('LT: '. $this->view->langtag);
    Zend_Session::start();
  }

  public function setHeaderFiles() {
    /* all CSS and js files are set up here */
    $csslist = array('/css/dtree.css',
        // charisma starts below
        '/charisma/css/bootstrap-cerulean.css','/charisma/css/bootstrap-responsive.css',
        '/charisma/css/charisma-app.css','/charisma/css/jquery-ui-1.8.21.custom.css',
        '/charisma/css/fullcalendar.css','/charisma/css/chosen.css',
        '/charisma/css/uniform.default.css','/charisma/css/jquery.noty.css',
        '/charisma/css/noty_theme_default.css','/charisma/css/elfinder.min.css',
        '/charisma/css/elfinder.theme.css','/charisma/css/opa-icons.css',
        '/css/echecklist-styles.css');
    foreach($csslist as $f) {
      $this->view->headLink()->appendStylesheet("{$this->baseurl}{$f}");
    }
    $jslist = array('/js/dtree.js','/charisma/js/jquery-1.7.2.min.js',
        '/charisma/js/jquery-ui-1.8.21.custom.min.js','/charisma/js/bootstrap-transition.js',
        '/charisma/js/bootstrap-alert.js','/charisma/js/bootstrap-modal.js',
        '/charisma/js/bootstrap-dropdown.js','/charisma/js/bootstrap-scrollspy.js',
        '/charisma/js/bootstrap-tab.js','/charisma/js/bootstrap-tooltip.js',
        '/charisma/js/bootstrap-popover.js','/charisma/js/bootstrap-button.js',
        '/charisma/js/bootstrap-collapse.js','/charisma/js/bootstrap-carousel.js',
        '/charisma/js/bootstrap-typeahead.js','/charisma/js/jquery.cookie.js',
        '/charisma/js/fullcalendar.min.js','/charisma/js/jquery.dataTables.min.js',
        '/charisma/js/jquery.chosen.min.js','/charisma/js/jquery.uniform.min.js',
        '/charisma/js/jquery.colorbox.min.js','/charisma/js/jquery.noty.js',
        '/charisma/js/jquery.elfinder.min.js','/charisma/js/jquery.raty.min.js',
        '/charisma/js/jquery.autogrow-textarea.js','/charisma/js/jquery.history.js',
        '/charisma/js/charisma.js','/js/helpers.js');
    // '/js/helpers.js'


    foreach($jslist as $f) {
      $this->view->headScript()->appendFile("{$this->baseurl}{$f}");
    }
    /*
     * logit ( "Links: {$this->view->headLink()}" ); logit ( "Scripts: {$this->view->headScript()}" );
     */
  }

  public function makeIcon($name, $color = '', $size = '') {
    return "<span title=\".icon{$size}  .icon-{$color} .icon-{$name} \" class=\"icon{$size} icon-{$color} icon-{$name}\"></span>";
  }

  public function makeMenu($menu) {
    /*
     * The input is an array of arrays array(top, array(array(icon, item),))+ The top level creates buttons the rest create the menu items
     */
    $out = array();
    $out[] = "<div class=\"btn-group pull-left\">";

    foreach($menu as $mx) {
      $i = 0;
      foreach($mx as $m) {
        $i ++;
        switch ($i) {
          case 1 :
            $icon = $m['icon'];
            $out[] = "<a class=\"btn dropdown-toggle\" data-toggle=\"dropdown\" href=\"{$m['url']}\">";
            $out[] = $this->makeIcon($icon[0], $icon[1]);
            $out[] = "<span class=\"hidden-phone\">{$m['text']}</span><span class=\"caret\"></span></a>";
            $out[] = "<ul class=\"dropdown-menu\">";
            break;
          default :
            foreach($m as $mi) {
              if (in_array('divider', $mi)) {
                $out[] = '<li class="divider"></li>';
                continue;
              }
              $icon = $mi['icon'];
              $out[] = "<li><a href=\"{$this->baseurl}{$mi['url']}\">" .
                   $this->makeIcon($icon[0], $icon[1]) .
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
    $complete_user = array('ADMIN','USER','APPROVER');
    $complete_audit = '';
    if (in_array($this->usertype, $complete_user)) {
      $complete_audit = <<<"END"
<li class="divider"></li>
<li><a href="{$this->baseurl}/audit/exportdata/$this->audit['id']"><span title=".icon  .icon-color  .icon-extlink " class="icon icon-color icon-extlink"></span> Export Data</a></li>
<li><a href="{$this->baseurl}/audit/delete"
       onclick=" return confirm('do you want to delete the current Audit (#{$this->audit['audit_id']}-{$this->audit['tag']})?');">
    <span title=".icon  .icon-color .icon-close " class="icon icon-color icon-close"></span>
    Delete #{$this->audit['audit_id']}</a></li>
END;

      if ($this->audit['status'] == 'INCOMPLETE') {
        $complete_audit .= <<<"END"
<li><a href="{$this->baseurl}/audit/complete/$this->audit['id']"><span title=".icon  .icon-color  .icon-locked " class="icon icon-color icon-locked"></span> Complete</a></li>
END;
      }
      if ($this->audit['status'] == 'COMPLETE' && $this->usertype == 'APPROVER') {
        $complete_audit .= <<<"END"
<li><a href="{$this->baseurl}/audit/finalize/$this->audit['id']"><span title=".icon  .icon-color  .icon-locked " class="icon icon-color icon-locked"></span> Complete</a></li>
END;
      }
    }
    $this->header = <<<"END"
<div class="navbar">
  <div class="navbar-inner">
    <div class="container-fluid">
      <a class="brand" href="{$this->baseurl}{$this->mainpage}">
        <span title=".icon  .icon-black  .icon-check " class="icon icon-black icon-check"></span> <span>eChecklist</span>
      </a>
END;
    $newuser = '';
    if ($this->usertype != '') {
      if ($this->usertype == 'ADMIN') {
        $newuser = <<<"END"
<li><a href="{$this->baseurl}/user/create">
<span title=".icon  .icon-green  .icon-user " class="icon icon-green icon-user"></span> New User</a></li>
END;
      }

      $this->header = $this->header . <<<"END"
<div class="btn-group pull-left" style="margin-left:100px;">
<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
  <span title=".icon  .icon-blue .icon-clipboard " class="icon icon-blue icon-clipboard"></span>
  <span class="hidden-phone">Audit</span>
  <span class="caret"></span></a>
<ul class="dropdown-menu">
  <li><a href="{$this->baseurl}/audit/create"><span title=".icon  .icon-green .icon-clipboard " class="icon icon-green icon-clipboard"></span> New Audit</a></li>
  <!--li><a href="{$this->baseurl}/audit/find"><span title=".icon  .icon-blue  .icon-search " class="icon icon-blue icon-search"></span> Find</a></li-->
{$complete_audit}
  <li class="divider"></li>
  <li><a href="{$this->baseurl}/audit/select"><span title=".icon  .icon-color  .icon-newwin " class="icon icon-color icon-newwin"></span> Process Audits</a></li>
  <li class="divider"></li>
  <li><a href="{$this->baseurl}/audit/import"><span title=".icon  .icon-blue .icon-import " class="icon icon-blue icon-archive"></span> Import</a></li>
</ul>
</div>

<div class="btn-group pull-left">
<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
  <span title=".icon  .icon-blue  .icon-tag " class="icon icon-blue icon-tag"></span>
  <span class="hidden-phone">Lab</span>
  <span class="caret"></span></a>
<ul class="dropdown-menu">
  <li><a href="{$this->baseurl}/lab/create"><span title=".icon  .icon-green  .icon-tag " class="icon icon-green icon-tag"></span> New Lab</a></li>
  <li><a href="{$this->baseurl}/lab/select"><span title=".icon  .icon-blue  .icon-search " class="icon icon-blue icon-search"></span> Select a Lab</a></li>
</ul>
</div>

<div class="btn-group pull-left">
<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
<span title=".icon  .icon-blue  .icon-user " class="icon icon-blue icon-user"></span>
<span class="hidden-phone">User</span>
<span class="caret"></span></a>
<ul class="dropdown-menu">
  {$newuser}
  <li><a href="{$this->baseurl}/user/find"><span title=".icon  .icon-blue  .icon-search " class="icon icon-blue icon-search"></span>Find User</a></li>
</ul>
</div>

<!--div class="btn-group pull-left">
<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
<span title=".icon  .icon-blue  .icon-flag " class="icon icon-blue icon-flag"></span>
<span class="hidden-phone">Language</span>
<span class="caret"></span></a>
<ul class="dropdown-menu">
  <li><a href="{$this->baseurl}/language/switch/EN"><span title=".icon  .icon-green  .icon-flag " class="icon icon-green icon-flag"></span> English</a></li>
  <li><a href="{$this->baseurl}/language/switch/FR"><span title=".icon  .icon-green  .icon-flag " class="icon icon-green icon-flag"></span> French</a></li>
  <li><a href="{$this->baseurl}/language/switch/VI"><span title=".icon  .icon-green  .icon-flag " class="icon icon-green icon-flag"></span> Vietnamese</a></li>
</ul>
</div-->

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
      $auditinfo = '';
      //if ($this->dialog_name == 'audit/edit') {
      $auditinfo = "<div style=\"margin:6px 0 6px 20px;padding-right:5px;\">Selected Audit: {$this->showaudit}</div>";
      //}
      $this->header .= <<<"END"
<div style="display:inline-block;">
  <div style="margin:6px 0px 6px 20px;padding-right:5px;">Selected Lab: {$this->labnum}/{$this->labname}</div>
    {$auditinfo}
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
  }

  function calculate_dialog($drows, $value, $title, $langtag, $formtype = 'table') {
    /**
     * Given the dialog rows, create the dialog
     * - using field templates to create individual rows
     */
    require_once 'modules/Checklist/fillout.php';
    $tlist = getTranslatables($langtag);

    $tout = array();
    $this->importall = "{$this->baseurl}/audit/importall";
    $this->import2lab = "{$this->baseurl}/audit/import2lab";
    $this->cancelimport =  "{$this->baseurl}/audit/cancelimport";
    $title = $drows[0]['title'];
    $tout[] = <<<"END"
<div style="margin-left:200px;"><h1 style="margin-bottom:10px;">{$title}
<button onclick="return toggleHelp();">Help</button> </h1> </div>
<div style="margin:15px 0;">
END;
    $tout[] = '<table border=0 style="width:900px;">';

    $hid = array();
    foreach($drows as $row) {
      $pos = $row['position'];
      // if ($pos ==0) continue;
      $type = $row['field_type'];
      $arow = array();

      $field_label = get_lang_text($row['field_label'], '', ''); // , $row ['ltdefault'], $row ['ltlang'] );
      $arow['field_label'] = $field_label .
           ':';
      $arow['varname'] = $row['field_name'];
      $varname = $arow['varname'];
      $arow['baseurl'] = $this->baseurl;
      $arow['field_length'] = 0; //$row['field_length'];
      $info = $row['info'];

      switch ($type) {
        case '' :
          logit("ROW: " . print_r($row, true));
          break;
        case 'hidden' :
          $val = get_arrval($value, $varname, '');
          $hid[] = "<input type=\"hidden\" name=\"{$varname}\" value=\"{$val}\">";
          break;
        case 'file' :
          $tout[] = <<<"END"
<tr>
<td class="n f right" style="width:200px;">
<td class="n f" style="width:600px;">
  <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
  {$field_label}: <input name="uploadedfile" type="file" />
</td>
</tr>
END;
          break;
        case 'text' :
          $info = str_replace('"', '\"', $info);
          logit("TEXT: {$info}");
          $audit = $this->audit;
          eval("\$lines = \"$info\"; ");
          $tout[] = <<<"END"
<tr>
<td class="n f right" style="width:200px;">
<td class="n f" style="width:600px;"><div id="help2" style="font-size:1.4em;line-height:1.3em;">{$lines}</div></td>
</tr>
END;
          break;
        case 'info' :
          $tout[] = <<<"END"
<tr>
<td class="n f right" style="width:200px;">
<td class="n f" style="width:600px;"><div id="help" style="display:none;">{$info}</div></td>
</tr>
END;
          break;
        case 'info2' :
          $tout[] = <<<"END"
<tr>
<td class="n f right" style="width:200px;">
<td class="n f" style="width:600px;"><div id="help2" style="">{$info}</div></td>
</tr>
END;
          break;
        case 'heading' :
          $tout[] = <<<"END"
<tr>
<td class="n f right" style="width:50px;">
<td class="n f" style="width:750px;"><h3>{$field_label}</h3></td>
</tr>
END;
          break;
        case 'submit_button' :
          $arow['field_label'] = $field_label;
          //$arow['baseurl'] = $baseurl;
          $arow['homeurl'] = "{$this->baseurl}/audit/main";
          logit("HURL: {$arow['homeurl']}");
          $field_label = '';
        default :
          $inp = call_user_func("dialog_{$type}", $arow, $value, $tlist);
          $tout[] = <<<"END"
<tr>
<td class="n f right" style="width:300px;">
<label for="{$varname}" style="" class="inp">{$field_label}</label>
</td><td class="n f" style="width:500px;">{$inp}</td>
</tr>
END;
      }
    }
    $tout[] = '</table></div></div>';
    $tout[] = implode("\n", $hid);
    // logit('dialog: '. print_r($tout, true));
    return implode("\n", $tout);
  }

  public function getDialogLines() {
    /*
     * Get the Dialog data
     */
    $dialog = new Application_Model_DbTable_Dialog();
    $this->drows = $dialog->getDialogRows($this->dialog_name);
    logit('DROWS: ' . print_r($this->drows, true));
  }

  public function makeDialog($value = array(''=>''), $morelines = '') {
    /*
     * Create the dialog
     */
    $this->getDialogLines();
    // logit('makeDialog:');
    $this->view->outlines = $this->calculate_dialog($this->drows, $value, $this->title,
        $this->view->langtag);
    if (is_array($this->error)) {
      logit('Error: ' . print_r($this->error, true));
      $this->view->errorLines = implode("\n", $this->error);
    } else {
      $this->view->errorLines = '';
    }
    $this->view->title = $this->title;
    if (isset($this->echecklistNamespace->flash)) {
      $this->view->flash = $this->echecklistNamespace->flash;
      // logit("FLASH: {$this->view->flash}");
      $this->echecklistNamespace->flash = '';
      // logit("there?: {$this->view->flash}");
    }
    $this->view->outlines .= $morelines;
    $this->_helper->layout->setLayout('overall');
  }

  public function collectData() {
    /*
     * Collect all the post data
     */
    $dialog = new Application_Model_DbTable_Dialog();

    $this->getDialogLines();
    $ignore_list = array('',''
        /*,
        'submit_button'*/
    );
    $this->error = array();
    $this->error[] = "<table><tr><th colspan='2'>Fix the following errors and retry</td></tr>";
    $errorct = 0;
    $this->data = array();
    $formData = $this->getRequest();
    foreach($this->drows as $row) {
      if ($row['position'] == 0)
        continue;
      $type = $row['field_type'];
      $varname = $row['field_name'];
      $field_label = $row['field_label'];
      $validate = $row['validate'];
      if (in_array($type, $ignore_list)) {
        continue;
      }
      // logit('IN: '. $formData->getPost($varname,''));
      $this->data[$varname] = $formData->getPost($varname, '');
      // VALIDATION HERE
      if ($validate) {
        $msg = call_user_func($validate, $this->data[$varname]);
        logit("VAL: {$varname} {$validate} {$this->data[$varname]} -- {$msg}");

        if ($msg) {
          // Enter into error array
          $errorct ++;
          $this->error[] = "<tr><td class=\"rpad\"><b>{$field_label}:</b></td>" .
               "<td> <Span class=\"error\">{$msg}</span></td></tr>";
        }
      }
    }
    $this->error[] = "</table>";
    if ($errorct == 0)
      $this->error = array();
    $this->handleCancel();
    logit("ECT: " . count($this->error));
    if (count($this->error) > 0) {
      $this->echecklistNamespace->flash = 'Correct errors and retry';
      $this->makeDialog($this->data);
      return true;
    }
  }

  public function collectExtraData($prefix = '') {
    /*
     * Collect all the post data
     */
    logit('REST: ' . print_r($this->getRequest()->getPost(), true));
    $vars = $this->getRequest()->getPost();
    $lprefix = strlen($prefix);
    $out = array();
    foreach($vars as $n => $v) {
      if (substr($n, 0, $lprefix) == $prefix) {
        // logit("MATCH: {$n} => {$v}");
        $out[$n] = $v;
      }
    }
    $this->extra = $out;
  }

  public function makeLabLines($rows, $cb = false) {
    // Given lab rows - show in a table
    $rev_level = rev('getLevels', $this->tlist);
    $rev_affil = rev('getAffiliations', $this->tlist);
    $rev_type = rev('getLTypes', $this->tlist);
    $edit_users = array('ADMIN','USER','APPROVER');
    $ct = 0;
    $tout = array();
    $tout[] = '<table style="margin-left:50px;color:black;">';
    $tout[] = "<tr class='even'>";
    if ($cb) {
      $first = "<td style='width:50px;'>Select/<br />Deselect All</td>";
    } else {
      $first = "<td style='width:50px;'></td>";
    }
    $etop = '';
    if (in_array($this->usertype, $edit_users)) {
      $etop = "<td style='width:50px;'></td>";
    }
    $tout[] = <<<"END"
{$first}
<td style='width:100px;font-weight:bold;'>Lab Number</td>
<td style='width:200px;font-weight:bold;'>Labname</td>
<td style='width:85px;font-weight:bold;'>Country</td>
<td style='width:100px;font-weight:bold;'>Level</td>
<td style='width:145px;font-weight:bold;'>Affiliation</td>
<td style='width:145px;font-weight:bold;'>SLMTA Lab Type</td>
{$etop}</tr>
END;
    foreach($rows as $row) {
      $ct ++;
      $cls = ($ct % 2 == 0) ? 'even' : 'odd';
      $edit = '';
      if (in_array($this->usertype, $edit_users)) {
        $edit = "<a href=\"{$this->baseurl}/lab/edit/{$row['id']}\"" .
             " class=\"btn btn-mini btn-warning\">Edit</a>";
      }
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
      // $sl = ($row['slmta'] == 't') ? 'Yes' : 'No';
      $tout[] = <<<"END"
<td>{$row['labnum']}</td>
<td>{$row['labname']}</td>
<td>{$row['country']}</td>
<td><p class="small">{$rev_level[$row['lablevel']]}</p></td>
<td><p class="small">{$rev_affil[$row['labaffil']]}</p></td>
<td><p class="small">{$rev_type[$row['slmta_labtype']]}</p></td>
END;
      if ($edit != '')
        $tout[] = "<td style='width:40px;padding:2px 0;'>{$edit}</td>";
      $tout[] = '</tr>';
      // "<td style='width:45px;font-weight:bold;'>SLMTA</td>" .
      // <td>{$sl}</td>
    }

    $tout[] = '</table>';
    $this->view->showlines = implode("\n", $tout);
  }

  public function makeAuditLines($rows, $options = array()) {
    // Given audit rows - show in a table
    $cb = false;
    $addsel = false;
    $cb = (get_arrval($options, 'cb', false)) ? true : false;
    $addsel = (get_arrval($options, 'addsel', false)) ? true : false;
    $rev_level = rev('getLevels', $this->tlist);
    $rev_affil = rev('getAffiliations', $this->tlist);
    $tout = array();

    $ct = 0;
    if ($cb) {
      /*      $tout[] = <<<"END"
<form method="post" action="{$this->baseurl}/output/process"
      enctype="multipart/form-data" name="action"
      id="action">
END;
*/
    }
    $tout[] = '<table style="margin-left:50px;color:black;">';
    $tout[] = "<tr class='even'>";

    if ($cb) {
      $tout[] = "<td style='width:55px;'><center>Select/<br />Deselect<br />All<br /><input type='checkbox' name='allcb' id='allcb'></center></td>";
    } else {
      $tout[] = "<td style='width:55px;'></td>";
    }
    $tout[] = <<<"END"
<td style='width:43px;font-weight:bold;padding:2px 0;'>Audit<br />Id</td>
<td style='width:55px;font-weight:bold;'>Type</td>
<td style='width:55px;font-weight:bold;'>SLMTA<br />Type</td>
<td style='width:55px;font-weight:bold;'>Slipta<br />Off.</td>
        <td style='width:90px;font-weight:bold;'>Date</td>
<td style='width:100px;font-weight:bold;'>Labnum</td>
<td style='width:150px;font-weight:bold;'>Labname</td>
<td style='width:85px;font-weight:bold;'>Country</td>
<td style='width:100px;font-weight:bold;'>Level</td>
<td style='width:145px;font-weight:bold;'>Affiliation</td>
<td style='width:45px;font-weight:bold;'>Co-<br />hort<br />Id</td>
<td style='width:92px;font-weight:bold;'>Status</td>
END;
    if (! $cb) {
      $tout[] = "<td style='width:90px;font-weight:bold;'></td><td></td></tr>";
    } else {
      $tout[] = "<td></td></tr>";
    }
    foreach($rows as $row) {
      //logit('Audit: ' . print_r($row, true));
      $ct ++;
      $cls = ($ct % 2 == 0) ? 'even' : 'odd';
      $edit = "<a href=\"{$this->baseurl}/audit/edit/{$row['audit_id']}/\"" .
           " class=\"btn btn-mini btn-inverse\">Edit</a>";
      $view = "<a href=\"{$this->baseurl}/audit/view/{$row['audit_id']}\" class=\"btn btn-mini btn-success\">View</a>";
      /*$delete = "<a href=\"#\" class=\"btn btn-mini btn-danger\">Delete</a>";
      $export = "<a href=\"{$this->baseurl}/audit/exportdata/{$row['audit_id']}\"" .
           " class=\"btn btn-mini btn-warning\">Data Export</a>";*/
      $adduser = '';
      if ($row['status'] == 'INCOMPLETE') {
        $adduser = "<a href=\"{$this->baseurl}/audit/choose/{$row['audit_id']}\"" . " class=\"btn btn-mini btn-info\">Select</a>";
  }
  $tout[] = "<tr class='{$cls}' style=\"height:24px;\">";
  if ($cb) {
    $name = "cb_{$row['audit_id']}";
    $tout[] = "<td style='width:40px;padding:4px 0;'>" .
         "<center><input type='checkbox' name='{$name}' id='{$name}'></center></td>";
  } else if ($addsel) {
    $butt = "<a href=\"{$this->baseurl}/lab/choose/{$row['audit_id']}\"" .
         " class=\"btn btn-mini btn-success\">Select</a>";
    $tout[] = "<td style='width:40px;padding:2px 4px;'>{$butt}</td>";
  } else {
    $tout[] = "<td style='width:40px;padding:2px 4px;'>{$adduser}</td>";
  }
  $tout[] = <<<"END"
<td>{$row['audit_id']}</td>
<td>{$row['audit_type']}</td><td>{$row['slmta_type']}</td><td>{$row['slipta_official']}</td>
<td>{$row['end_date']}</td>
<td>{$row['labnum']}</td>
<td>{$row['labname']}</td><td>{$row['country']}</td>
<td><p class="small">{$rev_level[$row['lablevel']]}</p></td>
<td><p class="small">{$rev_affil[$row['labaffil']]}</p></td>
<td>{$row['cohort_id']}</td><td>{$row['status']}</td>

END;
  if (! $cb) {
    $tout[] = "<td>{$view} {$edit}</td><td></td></tr>";
    //{$export} {$delete}
  } else {
    $tout[] = "<td></td></tr>";
  }
}
if ($cb) {
  $tout[] = '<tr><td colspan=13 style="text-align:right;" ><input class="input-xlarge submit" type="submit" name="doit" value="Process Request">';
}
$tout[] = '</table><div style="height: 65px;">&nbsp;</div>' . '<div style="clear: both;"></div>';
/* if ($cb) {
      $tour[] = '</form>';
    }*/
$lines = implode("\n", $tout);
return $lines;
}

public function makeUserLines($rows, $cb = false) {
// Given User rows - show in a table
$rev_ut = rev('getUserTypes', $this->tlist);
$ct = 0;
$tout = array();
if (in_array($this->usertype, array('USER','APPROVER'))) {
  $tout[] = "<div style=\"margin-left:200px\"><h1 style=\"margin-bottom:10px;\">Add owner to the current Audit</h1></div>";
}
if ($this->usertype == 'ADMIN') {
  $tout[] = "<h1 style=\"margin-bottom:10px;\">Edit User</h1>";
}
$tout[] = '<table style="margin-left:250px;color:black;">';
$tout[] = "<tr class='even'>";
if ($cb) {
  $tout[] = "<td style='width:80px;'>Select/<br />Deselect All</td>";
} else {
  $tout[] = "<td style='width:80px;'></td>";
}
$tout[] = <<<"END"
<td style='width:100px;font-weight:bold;'>UserId</td>
<td style='width:200px;font-weight:bold;'>Name</td>
<td style='width:85px;font-weight:bold;'>UserType</td>
END;
foreach($rows as $row) {
  $ct ++;
  $cls = ($ct % 2 == 0) ? 'even' : 'odd';

  $tout[] = "<tr class='{$cls}'>";
  if ($cb) {
    $name = "cb_{$row['id']}";
    $tout[] = "<td style='width:40px;padding:2px 0;'>" .
         "<input type='checkbox' name='{$name}' id='{$name}'></td>";
  } else {
    $sel = "<a href=\"{$this->baseurl}/user/edit/{$row['id']}\"" .
         " class=\"btn btn-mini btn-success\">Edit</a>";
    $addo = '';
    if ($row['usertype'] != 'ADMIN' && $row['usertype'] != 'ANALYST') {
      $addo = "<a href=\"{$this->baseurl}/user/addowner/{$row['id']}\"" . " class=\"btn btn-mini btn-warning\">Add Owner</a>";
}
logit("UT: {$this->usertype}, {$this->audit['status']}");
if ($this->usertype == 'USER' && $this->audit['status'] == 'INCOMPLETE') {
  $tout[] = "<td style='width:40px;padding:2px 0;'>{$addo}</td>";
}
if ($this->usertype == 'ADMIN') {
  $tout[] = "<td style='width:40px;padding:2px 0;'>{$sel}</td>";
}
}
// $sl = ($row['slmta'] == 't') ? 'Yes' : 'No';
$tout[] = <<<"END"
<td>{$row['userid']}</td>
<td>{$row['name']}</td>
<td>{$rev_ut[$row['usertype']]}</td>
END;
  // "<td style='width:45px;font-weight:bold;'>SLMTA</td>" .
  // <td>{$sl}</td>
}
$tout[] = '</table>';
$this->view->showlines = implode("\n", $tout);
}
}