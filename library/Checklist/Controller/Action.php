<?php

/**
 * This is the super class for our controllers
 */
abstract class Checklist_Controller_Action extends Zend_Controller_Action
{
  public $session;
  public $_redirector = '';
  public $debug = 1;
  public $baseurl = '';
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
  public $poststatchange = '/audit/search';
  public $loginpage = '/user/login';
  public $ISOdtformat = 'Y-m-d H:i:s';
  public $ISOformat = 'Y-m-d';
  public $tlist;
  public $error;
  public $log;
  public $general;
  public $fillout;
  public $val;

  public function init()
  {
    /* initialize here */
    $this->log = new Checklist_Logger();
    $this->general = new Checklist_Modules_General();
    $this->fillout = new Checklist_Modules_Fillout();
    $this->val = new Checklist_Modules_Validation();
    $this->baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $this->_redirector = $this->_helper->getHelper('Redirector');
    $this->setupSession();
    $this->setHeader();
    $this->setHeaderFiles();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    if (! isset($this->session->user) && ! ($pinfo[1] == 'user' && $pinfo[2] == 'login'))
    {
      $this->_redirector->gotoUrl($this->loginpage);
    }
    $this->getTwords();
  }

  public function handleCancel()
  {
    // cancel action: take user to main screen
    $this->log->logit('HC: ' . print_r($this->data, true));
    if ($this->data['submit_button'] == 'Cancel')
    {
      $this->data = array();
      $this->error = array();
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function getTwords()
  {
    // Get the translations for common words
    $this->tlist = $this->general->getTranslatables($this->langtag);
  }

  public function setupSession()
  {
    /* start the session */
    global $userid, $langtag;
    $this->session = new Zend_Session_Namespace('eChecklist');
    if (isset($this->session->user))
    {
      $u = $this->session->user;
      $this->usertype = $u['usertype'];
      $this->username = $u['userid'];
      $this->userfullname = $u['name'];
      $userid = $this->userid = $u['id'];
    }
    if (isset($this->session->lab))
    {

      $this->lab = $this->session->lab;
      $this->labid = $this->lab['id'];
      $this->labname = $this->lab['labname'];
      $this->labnum = $this->general->get_arrval($this->lab, 'labnum', 'No-NUM');
      $this->showlab = "{$this->labnum}/{$this->labname}";
    }
    else
    {
      $this->lab = null;
      $this->labid = 0;
      $this->labname = '';
      $this->labnum = '';
      $this->showlab = '';
    }
    if (! isset($this->session->lang))
    {
      $this->session->lang = 'EN';
    }
    // clear selected audit if labid is not for this audit
    $this->audit = null;
    if (isset($this->session->audit) || $this->session->audit != null)
    {
      $au = new Application_Model_DbTable_Audit();
      $this->audit = $au->getAudit($this->session->audit['audit_id']);
      if ($this->labid != $this->audit['lab_id'])
        $this->session->audit = null;
    }

    if (isset($this->session->audit) || $this->session->audit != null)
    {
      $au = new Application_Model_DbTable_Audit();
      $this->audit = $this->session->audit = $au->getAudit(
                                                          $this->session->audit['audit_id']);
      $this->showaudit = "{$this->audit['tag']} - #{$this->audit['audit_id']}" .
           "- {$this->audit['labname']}";
    }
    else
    {
      $this->audit = null;
      $this->showaudit = '';
    }
    $langtag = $this->view->langtag = $this->langtag = $this->session->lang;
    Zend_Session::start();
  }

  public function setHeaderFiles()
  {
    /* all CSS and js files are set up here */
    $csslist = array(

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
    foreach($csslist as $f)
    {
      $this->view->headLink()->appendStylesheet("{$this->baseurl}{$f}");
    }
    $jslist = array(

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
    );

    foreach($jslist as $f)
    {
      $this->view->headScript()->appendFile("{$this->baseurl}{$f}");
    }
  }

  public function makeIcon($name, $color = '', $size = '')
  {
    return "<span title=\".icon{$size}  .icon-{$color} .icon-{$name} \" class=\"icon{$size} icon-{$color} icon-{$name}\"></span>";
  }

  public function makeMenu($menu)
  {
    /*
     * The input is an array of arrays array(top, array(array(icon, item),))+
     * The top level creates buttons the rest create the menu items
     */
    $out = array();
    $out[] = "<div class=\"btn-group pull-left\">";

    foreach($menu as $mx)
    {
      $i = 0;
      foreach($mx as $m)
      {
        $i ++;
        switch ($i)
        {
          case 1 :
            $icon = $m['icon'];
            $out[] = "<a class=\"btn dropdown-toggle\" data-toggle=\"dropdown\" href=\"{$m['url']}\">";
            $out[] = $this->makeIcon($icon[0], $icon[1]);
            $out[] = "<span class=\"hidden-phone\">{$m['text']}</span><span class=\"caret\"></span></a>";
            $out[] = "<ul class=\"dropdown-menu\">";
            break;
          default :
            foreach($m as $mi)
            {
              if (in_array('divider', $mi))
              {
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

  function calculate_dialog($drows, $value, $title, $langtag, $formtype = 'table')
  {
    /**
     * Given the dialog rows, create the dialog
     * - using field templates to create individual rows
     */
    $tlist = $this->general->getTranslatables($langtag);

    $tout = array();
    $this->importall = "{$this->baseurl}/audit/importall";
    $this->import2lab = "{$this->baseurl}/audit/import2lab";
    $this->cancelimport = "{$this->baseurl}/audit/cancelimport";
    $title = $drows[0]['title'];
    $tout[] = <<<"END"
<div style="margin-left:200px;"><h1 style="margin-bottom:10px;">{$title}
<button id="helpbutton" onclick="return toggleHelp();">Help</button> </h1> </div>
<div style="margin:15px 0;">
END;
    $tout[] = '<table border=0 style="width:900px;">';

    $hid = array();
    //$this->log->logit('dr: '.  print_r($drows, true));
    foreach($drows as $row)
    {
      $pos = $row['position'];
      $type = $row['field_type'];
      $arow = array();

      $field_label = $this->general->get_lang_text($row['field_label'], '', '');
      $arow['field_label'] = $field_label . ':';
      $arow['varname'] = $row['field_name'];
      $varname = $arow['varname'];
      $arow['baseurl'] = $this->baseurl;
      $arow['field_length'] = 0;
      $info = $row['info'];
      $fillout = new Checklist_Modules_Fillout();
      switch ($type)
      {
        case '' :
          $this->log->logit("ROW: " . print_r($row, true));
          break;
        case 'hidden' :
          $val = $this->general->get_arrval($value, $varname, '');
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
          // $this->log->logit("TEXT: {$info}");
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
          $arow['homeurl'] = "{$this->baseurl}/audit/main";
          $field_label = '';
        default :
          $func = "dialog_{$type}";
          $inp = $fillout->$func($arow, $value, $tlist);
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
    return implode("\n", $tout);
  }

  public function getDialogLines()
  {
    /*
     * Get the Dialog data
     */
    $dialog = new Application_Model_DbTable_Dialog();
    $this->drows = $dialog->getDialogRows($this->dialog_name);
  }

  public function makeDialog($value = array(''=>''), $morelines = '')
  {
    /*
     * Create the dialog
     */
    $this->getDialogLines();
    $this->view->outlines = $this->calculate_dialog($this->drows, $value, $this->title,
                                                    $this->view->langtag);
    if (is_array($this->error))
    {
      $this->log->logit('Error: ' . print_r($this->error, true));
      $this->view->errorLines = implode("\n", $this->error);
    }
    else
    {
      $this->view->errorLines = '';
    }
    $this->view->title = $this->title;
    if (isset($this->session->flash))
    {
      $this->view->flash = $this->session->flash;
      $this->session->flash = '';
    }
    $this->view->outlines .= $morelines;
    $this->_helper->layout->setLayout('overall');
  }

  public function collectData()
  {
    /*
     * Collect all the post data
     */
    $dialog = new Application_Model_DbTable_Dialog();
    $valid = new Checklist_Modules_Validation();
    $this->getDialogLines();
    $ignore_list = array(

        '',
        ''
    );
    $this->error = array();
    $this->error[] = "<table><tr><th colspan='2'>Fix the following errors and retry</td></tr>";
    $errorct = 0;
    $this->data = array();
    $formData = $this->getRequest();
    foreach($this->drows as $row)
    {
      if ($row['position'] == 0)
        continue;
      $type = $row['field_type'];
      $varname = $row['field_name'];
      $field_label = $row['field_label'];
      $validate = $row['validate'];
      if (in_array($type, $ignore_list))
      {
        continue;
      }
      $this->data[$varname] = $formData->getPost($varname, '');
      // VALIDATION HERE
      $this->log->logit("VAL: {$validate}");
      if ($validate)
      {
        //$msg = call_user_func($validate, $this->data[$varname]);
        $msg = $valid->$validate($this->data[$varname]);
        $this->log->logit("VAL: {$varname} {$validate} {$this->data[$varname]} -- {$msg}");

        if ($msg)
        {
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
      $this->log->logit('COLL: '.print_r($this->data, true));
    $this->handleCancel();
    $this->log->logit("ECT: " . count($this->error));
    if (count($this->error) > 0)
    {
      $this->session->flash = 'Correct errors and retry';
      $this->makeDialog($this->data);
      return true;
    }
  }

  public function collectExtraData($prefix = '')
  {
    /*
     * Collect all the post data
     */
    // $this->log->logit('REST: ' . print_r($this->getRequest()->getPost(),
    // true));
    $vars = $this->getRequest()->getPost();
    $lprefix = strlen($prefix);
    $out = array();
    foreach($vars as $n => $v)
    {
      if (substr($n, 0, $lprefix) == $prefix)
      {
        // $this->log->logit("MATCH: {$n} => {$v}");
        $out[$n] = $v;
      }
    }
    $this->extra = $out;
  }

  public function makeLabLines($rows, $cb = false)
  {
    // Given lab rows - show in a table
    $rev_level = $this->general->rev('getLevels', $this->tlist);
    $rev_affil = $this->general->rev('getAffiliations', $this->tlist);
    $rev_type = $this->general->rev('getLTypes', $this->tlist);
    $edit_users = array(

        'ADMIN',
        'USER',
        'APPROVER'
    );
    $ct = 0;
    $tout = array();
    $tout[] = '<table style="margin-left:50px;color:black;">';
    $tout[] = "<tr class='even'>";
    if ($cb)
    {
      $first = "<td style='width:55px;'>Select/<br />Deselect All</td>";
    }
    else
    {
      $first = "<td style='width:55px;'></td>";
    }
    $etop = '';
    // f (in_array($this->usertype, $edit_users)) {
    // $etop = "<td style='width:50px;'></td>";
    //
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
    foreach($rows as $row)
    {
      // $this->log->logit("LAB: " . print_r($row, true));
      $ct ++;
      $cls = ($ct % 2 == 0) ? 'even' : 'odd';
      $edit = '';
      // f (in_array($this->usertype, $edit_users)) {
      // $edit = "<a href=\"{$this->baseurl}/lab/edit/{$row['id']}\"" .
      // " class=\"btn btn-mini btn-warning\">Edit</a>";
      //
      $line = '';
      if ($cb)
      {
        $name = "cb_{$row['id']}";
        $line .= "<td style='width:40px;padding:2px 0;'>" .
             "<input type='checkbox' name='{$name}' id='{$name}'></td>";
      }
      else if ($this->labnum == $row['labnum'])
      {
        $cls = 'hilight';
        $line .= "<td><div style=\"color:red;\">Selected</div></td>";
      }
      else
      {
        $butt = "<a href=\"{$this->baseurl}/lab/choose/{$row['id']}\"" .
             " class=\"btn btn-mini btn-success\">Select</a>";
        $line .= "<td style='width:55px;padding:2px 0;'>{$butt}</td>";
      }
      // $tout[] = "<tr class='{$cls}'>";

      $tout[] = <<<"END"
<tr class="{$cls}">
{$line}
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
    if (!$rows)
    {
      $tout =array('<table style="margin-left:50px;color:black;font-size:17px;">' .
          '<tr><td>No matching Labs found.</td></tr></table>');
    }
    $this->view->showlines = implode("\n", $tout);
  }

  public function makeAuditLines($rows, $options = array())
  {
    // Given audit rows - show in a table
    $cb = false;
    $addsel = false;
    $cb = ($this->general->get_arrval($options, 'cb', false)) ? true : false;
    $addsel = ($this->general->get_arrval($options, 'addsel', false)) ? true : false;
    $rev_level = $this->general->rev('getLevels', $this->tlist);
    $rev_affil = $this->general->rev('getAffiliations', $this->tlist);
    $tout = array();

    $ct = 0;
    if ($cb)
    {
      /*
       * $tout[] = <<<"END" <form method="post"
       * action="{$this->baseurl}/output/process" enctype="multipart/form-data"
       * name="action" id="action"> END;
       */
    }
    $tout[] = '<table style="margin-left:50px;color:black;">';
    $tout[] = "<tr class='even'>";

    if ($cb)
    {
      $tout[] = "<td style='width:55px;'><center>Select/<br />Deselect<br />All<br /><input type='checkbox' name='allcb' id='allcb'></center></td>";
    }
    else
    {
      $tout[] = "<td style='width:55px;'></td>";
    }
    $tout[] = <<<"END"
<td style='width:43px;font-weight:bold;padding:2px 0;'>Audit<br />Id</td>
<td style='width:55px;font-weight:bold;'>Type</td>
<td style='width:55px;font-weight:bold;'>SLMTA<br />Type</td>
<td style='width:55px;font-weight:bold;'>Slipta<br />Off.</td>
<td style='width:130px;font-weight:bold;'>Date</td>
<td style='width:100px;font-weight:bold;'>Labnum</td>
<td style='width:150px;font-weight:bold;'>Labname</td>
<td style='width:85px;font-weight:bold;'>Country</td>
<td style='width:100px;font-weight:bold;'>Level</td>
<td style='width:145px;font-weight:bold;'>Affiliation</td>
<td style='width:45px;font-weight:bold;'>Co-<br />hort<br />Id</td>
<td style='width:92px;font-weight:bold;'>Status</td>
END;
    if (! $cb)
    {
      $tout[] = "<td></td></tr>";
    }
    else
    {
      $tout[] = "</tr>";
    }
    foreach($rows as $row)
    {
      // $this->log->logit('Audit: ' . print_r($row, true));
      if (! $row['owner'] && $row['status'] == 'INCOMPLETE')
        continue;
        // if ($)
      $ct ++;
      $cls = ($ct % 2 == 0) ? 'even' : 'odd';

      $adduser = '';
      if ($this->audit['audit_id'] != $row["audit_id"])
      {
        $selx = "<a href=\"{$this->baseurl}/audit/choose/{$row["audit_id"]}\" class=\"btn btn-mini btn-info\">Select</a>";
      }
      else
      {
        $cls = 'hilight';
        $selx = "<div style=\"color:red;\">Selected</div>";
      }
      $tout[] = "<tr class='{$cls} ' style=\"height:24px;\">";
      if ($cb)
      {
        $name = "cb_{$row['audit_id']}";
        $tout[] = "<td style='width:40px;padding:4px 0;'>" .
             "<center><input type='checkbox' name='{$name}' id='{$name}'></center></td>";
      }
      else if ($addsel)
      {
        $butt = "<a href=\"{$this->baseurl}/lab/choose/{$row['audit_id']}\"" .
             " class=\"btn btn-mini btn-success\">Select</a>";
        $tout[] = "<td style='width:40px;padding:2px 4px;'>{$butt}</td>";
      }
      else
      {
        $tout[] = "<td style='width:40px;padding:2px 4px;'>{$selx}</td>";
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
      if (! $cb)
      {
        $tout[] = "<td></td></tr>";
        // {$export} {$delete}
      }
      else
      {
        $tout[] = "</tr>";
      }
    }
    if ($cb)
    {
      $tout[] = '<tr><td colspan=13 style="text-align:right;" ><input class="input-xlarge submit" type="submit" name="doit" value="Process Request">';
    }
    $tout[] = '</table><div style="height: 65px;">&nbsp;</div>' .
         '<div style="clear: both;"></div>';
    /*
     * if ($cb) { $tour[] = '</form>'; }
     */
    $lines = implode("\n", $tout);
    if (! $rows)
    {
      $lines = '<table style="margin-left:50px;color:black;font-size:17px;"><tr><td>No matching audits found.</td</tr></table>';
    }
    return $lines;
  }

  public function makeUserLines($rows, $cb = false)
  {
    // Given User rows - show in a table
    global $user;
    $this->log->logit("US: {$this->usertype}");
    $rev_ut = $this->general->rev('getUserTypes', $this->tlist);
    $ct = 0;
    $user_users = array(

        'ADMIN',
        'USER',
        'APPROVER'
    );
    $ao = new Application_Model_DbTable_AuditOwner();
    $owner = $ao->isOwned($this->audit['audit_id'], $this->userid);
    $tout = array();
    /*
     * if (in_array($this->usertype, array('ADMIN', 'USER', 'APPROVER'))) {
     * //$tout[] = "<div style=\"margin-left:200px\"><h1
     * style=\"margin-bottom:10px;\">Add owner to the current Audit</h1></div>";
     * }
     */
    /*if ($this->usertype == 'ADMIN') {
      $tout[] = "<h1 style=\"margin-bottom:10px;\">Edit User</h1>";
    } else {
      $tout[] = "<h1 style=\"margin-bottom:10px;\"></h1>";
    }*/
    $tout[] = '<table style="margin-left:200px;color:black;">';
    $tout[] = "<tr class='even'>";
    if ($cb)
    {
      $tout[] = "<td style='width:110px;'>Select/<br />Deselect All</td>";
    }
    else
    {
      $tout[] = "<td style='width:110px;'></td>";
    }
    $tout[] = <<<"END"
<td style='width:100px;font-weight:bold;'>UserId</td>
<td style='width:200px;font-weight:bold;'>Name</td>
<td style='width:85px;font-weight:bold;'>UserType</td>
END;
    foreach($rows as $row)
    {
      $ct ++;
      $cls = ($ct % 2 == 0) ? 'even' : 'odd';
      $tout[] = "<tr class='{$cls}'>";
      $firstcol = '';
      if ($cb)
      {
        $name = "cb_{$row['id']}";
        $firstcol = "<td style='width:40px;padding:2px 0;'>" .
             "<input type='checkbox' name='{$name}' id='{$name}'></td>";
      }
      else
      {

        $sel = "<a href=\"{$this->baseurl}/user/edit/{$row['id']}\"" .
             " class=\"btn btn-mini btn-success\">Edit</a>";
        $addo = '';
        if (in_array($row['usertype'], $user_users))
        {
          $addo = "<a href=\"{$this->baseurl}/user/addowner/{$row['id']}\" onclick=\"return confirm('Add {$row['name']} to owners of selected audit?');\" class=\"btn btn-mini btn-warning\">Add Owner</a>";
        }
        $buttons = '';
        // $this->log->logit("UT: {$this->usertype}, {$this->audit['status']}");
        if ($this->audit['status'] == 'INCOMPLETE')
        {
          $buttons .= $addo;
          // $firstcol .= "<td style='width:40px;padding:2px 0;'>{$addo}</td>";
        }
        if ($this->usertype == 'ADMIN')
        {
          $buttons .= $sel;
          // $firstcol .= "<td style='width:40px;padding:2px 0;'>{$sel}</td>";
        }
        if ($this->usertype == 'APPROVER')
        {
          $firstcol .= '<td></td>';
        }
        $firstcol = "<td style='padding:2px 0;'>{$buttons}</td>";
      }
      // $sl = ($row['slmta'] == 't') ? 'Yes' : 'No';
      $tout[] = <<<"END"
{$firstcol}
<td>{$row['userid']}</td>
<td>{$row['name']}</td>
<td>{$rev_ut[$row['usertype']]}</td>
END;
      // "<td style='width:45px;font-weight:bold;'>SLMTA</td>" .
      // <td>{$sl}</td>
    }
    $tout[] = '</table>';
    if (! $rows)
    {
      $tout = array('<table style="margin-left:50px;color:black;font-size:17px;">'.
          '<tr><td>No matching users found</td>,</tr></table>');
    }
    $this->view->showlines = implode("\n", $tout);
  }

  public function getButtons($page)
  {
    /*
     * Given the page to paint, get buttons markup
     */
    $display = ($page['display_only'] == 't');
    $buttons = '';
    $thispage = $page['page_num'];
    $this->view->thispage = $thispage;
    $nextpage = $page['next_page_num'];
    if ($display)
    {
      $buttons = <<<"END"
<div style="width:825px;">
  <input type="hidden" name="nextpage" value="{$nextpage}" />
  <div style="float:right;">
    <input type="submit" value="Next" id="nextbutton" name="sbname">
</div></div>
END;
    }
    else
    {
      $buttons = <<<"END"
<div style="width:825px;">
  <input type="hidden" name="nextpage" value="{$nextpage}" />
  <div style="float:right;">
    <input type="submit" value="Cancel" id="cancelbutton" name="sbname">
    <input type="submit" tabindex="-1" value="Save" id="savebutton" name="sbname">
    <input type="submit" value="Save & Continue" id="continuebutton" name="sbname">
</div></div>
END;
    }
    $buttons .= <<<"END"
<script>
$(function() {
  d.closeAll();
  d.openTo({$thispage}, true);
});
</script>
END;
    $this->view->buttons = $buttons;
  }

  public function iscomplete($audit_id)
  {
    // check if this slipta audit is complete
    $sec_div = '<div style="border:1px solid #ccc;background-color:#ffc;padding:3px;margin: 2px;display:inline-block;">';
    $ncnote_div = '<div style="border:1px solid #ccc;background-color:#ccf;padding:3px;margin: 2px;display:inline-block;">';
    $comm_div = '<div style="border:1px solid #ccc;background-color:#ccc;padding:3px;margin: 2px;display:inline-block;">';
    $inc_div = '<div style="border:1px solid #cfc;background-color:#ccc;padding:3px;margin: 2px;display:inline-block;">';
    $tmplr = new Application_Model_DbTable_TemplateRows();
    $auditd = new Application_Model_DbTable_AuditData();
    $page = new Application_Model_DbTable_Page();
    $tmpl_id = 1; // for SLIPTA FIX ME to pick the template_id for this audit
    $pagelist = $page->getSectionPages($tmpl_id);
    $tag2page = array();
    foreach($pagelist as $p)
    {
      $tag2page[$p['tag']] = $p['page_num'];
    }
    // $this->log->logit("Pagelist: " . print_r($tag2page, true));
    $tmplrows = $tmplr->getAllRowsNotext($tmpl_id, $this->langtag);
    $adrows = $auditd->getAllData($audit_id);
    $tracker = array();
    $line = '';
    $secbegin = '';
    foreach($tmplrows as $tr)
    {
      // $this->log->logit("TR: ". print_r($tr, true));
      if ($tr['required'] == 'F')
        continue;
      $ect = $tr['element_count'];
      $vname = $tr['varname'];
      switch ($tr['row_type'])
      {
        case 'sec_head' :
          if ($line != '')
          {
            $this->log->logit('NEW:');
            $tracker[] = $secbegin . $line;
          }
          $line = "";
          // $name = $vname;
          // $this->log->logit("sec: {$vname} - ".
          // $this->general->get_arrval($adrows,
          // "{$vname}_secinc", 100));
          $secnum = (int) substr($vname, 1);
          $secname = "Section {$secnum}";
          $url = "{$this->baseurl}/audit/edit/{$tag2page[$secname]}";
          $secbegin = $sec_div . "<a href=\"{$url}\">Section {$secnum}</a></div>";
          if ($this->general->get_arrval($adrows, "{$vname}_secinc", 999) != 0)
          {
            // $secbegin = $sec_div . 'Section ' . (int) substr($vname, 1).
            // '</div>';
            $line .= "$inc_div {$secnum}: Inc </div>";
          }
          else
          {
            $line .= '';
          }
          break;
        case 'sub_sec_head_ro' :
          // multiple elements
          $name = $vname;
          // $this->log->logit("RO {$name}");
          // $this->log->logit("EXT: {$name} " .
          // $this->general->get_arrval($adrows, $name,
          // ''));
          $ssinc = $this->general->get_arrval($adrows, "{$name}_inc", 9);
          // $this->log->logit("ssinc: {$ssinc}");
          $ss = (int) substr($name, 1, 2) . '.' . (int) substr($name, 3, 2);
          if ($ssinc > 0)
          {
            // $tracker[] = "SubSection {$ss} incomplete";
            $line .= "{$inc_div} {$ss}: Inc </div>";
          }
          $val = $this->general->get_arrval(
                                                                                                    $adrows,
                                                                                                    "{$name}_yn",
                                                                                                    '') .
               $this->general->get_arrval($adrows, "{$name}_yna", '');
          // $this->log->logit("ECT: {$name} ==> {$val} :" .
          // $adrows['{$name}_inc']);
          // $this->log->logit("rVAL: {$name} {$val}");
          $s1 = (int) substr($name, 1, 2);
          $s2 = (int) substr($name, 3, 2);
          $sse = "{$s1}.{$s2}";
          if ($val && $val != 'YES' and $val != '-')
          {
            $this->log->logit("r1VAL: {$name} {$val}");
            // check for comment
            if ($adrows["{$name}_comment"] == '')
            {
              // $tracker[] = "Missing comment: {$name}";
              $line .= "{$comm_div}{$sse}: Comm </div>";
            }
          }
          $nc = $this->general->get_arrval($adrows, "{$name}_nc", '');
          $note = $this->general->get_arrval($adrows, "{$name}_note", '');
          // $this->log->logit("{$name} NC: {$nc} - {$note}");
          if ($nc == 'T' && $note == '')
          {
            // there should be a note - non compliant note - it is missing
            // $tracker[] = "Missing Non Compliant note: {$name}";
            $line .= "{$ncnote_div}{$sse}: nc note </div>";
          }
          for($i = 1; $i <= $ect; $i ++)
          {
            $name = ($i < 10) ? "{$vname}0{$i}" : "{$vname}{$i}";
            $val = $this->general->get_arrval(
                                                                                                      $adrows,
                                                                                                      "{$name}_yn",
                                                                                                      '') .
                 $this->general->get_arrval($adrows, "{$name}_yna", '');
            // $this->log->logit("ECT: {$name} ==> {$val} :" .
            // $adrows['{$name}_inc']);
            // $this->log->logit("rVAL: {$name} {$val}");
            $s1 = (int) substr($name, 1, 2);
            $s2 = (int) substr($name, 3, 2);
            $s3 = (int) substr($name, 5, 2);
            $sse = "{$s1}.{$s2}.{$s3}";
            if ($val && $val != 'YES' and $val != '-')
            {
              // $this->log->logit("r1VAL: {$name} {$val}");
              // check for comment
              if ($adrows["{$name}_comment"] == '')
              {
                // $tracker[] = "Missing comment: {$name}";
                $line .= "{$comm_div}{$sse}: Comm </div>";
              }
            }
            $nc = $this->general->get_arrval($adrows, "{$name}_nc", '');
            $note = $this->general->get_arrval($adrows, "{$name}_note", '');
            // $this->log->logit("{$name} NC: {$nc} - {$note}");
            if ($nc == 'T' && $note == '')
            {
              // there should be a note - non compliant note - it is missing
              // $tracker[] = "Missing Non Compliant note: {$name}";
              $line .= "{$ncnote_div}{$sse}: nc note </div>";
            }
          }

          break;
        case 'sub_sec_head' :
          $name = $vname;
          // $this->log->logit("Non-RO {$name}");
          $ssinc = $this->general->get_arrval($adrows, "{$name}_inc", 9);
          // $this->log->logit("ssinc: {$ssinc}");
          $ss = (int) substr($name, 1, 2) . '.' . (int) substr($name, 3, 2);
          if ($ssinc > 0)
          {
            $line .= "{$inc_div} {$ss}: Inc </div>";
          }
          $val = $this->general->get_arrval($adrows, "{$name}", '-');
          // $this->log->logit("ECT: {$name} ==> {$val} : " .
          // $this->general->get_arrval($adrows, "{$name}_inc", ''));
          // $this->log->logit("nVAL: {$name} {$val}");
          if ($val && $val != 'YES' && $val != '-')
          {
            // $this->log->logit("n1VAL: {$name} {$val}");
            // check for comment
            if ($this->general->get_arrval($adrows, "{$name}_comment", '') == '')
            {
              $line .= "<div style=\"border:1px solid #ccc;background-color:#ddd;padding:3px;margin:2px;display:inline-block;\">{$ss}: Comm</div> ";
            }
          }
          $nc = $this->general->get_arrval($adrows, "{$name}_nc", '');
          $note = $this->general->get_arrval($adrows, "{$name}_note", '');
          // $this->log->logit("{$name} NC: {$nc} - {$note}");
          if ($nc == 'T' && $note == '')
          {
            // there should be a note - non compliant note - it is missing
            // $this->log->logit("NC: {$ss}");
            $line .= "<div style=\"border:1px solid #ccc;background-color:#ccf;padding:3px;margin: 2px;display:inline-block;\">{$ss}: nc note</div> ";
          }
          break;
        default :
      }
    }
    if ($line != '')
    {
      // $this->log->logit('end');
      $tracker[] = "{$secbegin} {$line}";
    }
    // $this->log->logit("IC: ". print_r($tracker, true));
    if ($tracker)
    {
      array_unshift($tracker, "<h1>Missing items in Audit</h1>");
      return implode("<br />\n", $tracker);
    }
    return null;
  }

  public function updateDocs($labid) {
    // update all INCOMPLETE docs with this labid and set all the lab details accurately
    // This is necessary because audit_data table stores one value per row
    $this->log->logit("LABID: {$labid}");
    $lab = new Application_Model_DbTable_Lab();
    $audit = new Application_Model_DbTable_Audit();
    $tr =new Application_Model_DbTable_Template();
    $tmplr = new Application_Model_DbTable_TemplateRows();
    $auditd = new Application_Model_DbTable_AuditData();
    // fetch the labrow
    $labrow = $lab->get($labid);
    $this->log->logit("LABROW: {$labid} ".print_r($labrow, true));
    // get audits with this labid
    $arows = $audit->getIncompleteAuditsByLabid($labid);
    foreach($arows as $a) {
      $aid = $a['id'];
      $trow = $tr->getByTag($a['audit_type']);
      $tid = $trow['id'];
      // update audit with id =aid info with labrow
      $dummy = array('labhead' => 1);
      // this will trigger the lab data copy into audit data
      $varname = 'labhead';
      $page_id = $tmplr->findPageId($tid, $varname);
      $auditd->handleLabData($dummy, $aid, $page_id, $labrow);
      // update slmta data
      $dummy = array('slmta_labtype' => 1);
      $varname = 'slmta_labtype';
      $page_id = $tmplr->findPageId($tid, $varname);
      $this->log->logit("SMLTA UPdate: {$dummy}-{$aid}-{$page_id}" . print_r($labrow, true));
      $auditd->handleSLMTAData($dummy, $aid, $page_id, $labrow);
      $this->log->logit("UPdated: ADid {$aid}-T {$tid}-V {$varname}-P {$page_id}". print_r($labrow, true));
    }
  }

  public function setHeader()
  {
    /* Create the top line */
    $dt = date('j M, Y');
    $name_header = '';
    $ao = new Application_Model_DbTable_AuditOwner();
    if ($this->usertype != '')
    {
      $name_header = "&nbsp {$this->userfullname}";
    }
    $complete_user = array(

        'ADMIN',
        'USER',
        'APPROVER'
    );
    $complete_audit = '';
    $audit_id = $this->audit['audit_id'];
    $this->log->logit("audit: " . print_r($this->audit, true));
    $complete_audit .= <<<"END"
<li class="divider"></li>
<li class="tri"><span style="color:black;padding-left: 15px;"> With Selected Audit:</span></li>
      <li><a href="{$this->baseurl}/audit/showowners"><span title=".icon  .icon-color  .icon-profile " class="icon icon-color icon-profile"></span> Show Owners</a></li>
END;

      // incomplete and owned audit OR not incomplete audit can be viewed
    if ($this->audit &&
         (($this->audit['status'] == 'INCOMPLETE' && $this->audit['owner']) ||
         $this->audit['status'] != 'INCOMPLETE'))
    {
      $complete_audit .= <<<"END"
<li><a href="{$this->baseurl}/audit/view"><span title=".icon  .icon-color  .icon-book " class="icon icon-color icon-book"></span> View Audit</a></li>
END;
    }
  // only incomplete and owned audit can be edited
if ($this->audit['status'] == 'INCOMPLETE' && $this->audit['owner'])
{
  $complete_audit .= <<<"END"
<!--li class="divider"></li-->
<li><a href="{$this->baseurl}/audit/edit/"><span title=".icon  .icon-color  .icon-edit " class="icon icon-color icon-edit"></span> Edit Audit</a></li>
END;
}
if ($this->audit && in_array($this->usertype, $complete_user))
{
  $complete_audit .= <<<"END"
<!--li class="divider"></li-->
<li><a href="{$this->baseurl}/audit/exportdata"><span title=".icon  .icon-color  .icon-extlink " class="icon icon-color icon-extlink"></span> Export Audit Data</a></li>
END;
  if ($this->audit['owner'])
  {
    $complete_audit .= <<<"END"
<li><a href="{$this->baseurl}/audit/delete"
       onclick=" return confirm('Do you want to delete Selected Audit?');">
    <span title=".icon  .icon-color .icon-trash " class="icon icon-color icon-trash"></span>
    Delete Audit</a></li>
END;
  }
  $complete_audit .= <<<"END"
<li class="divider"></li>
<li class="tri"><span style="color:black;padding-left: 15px;"> Change Audit State:</span></li>
END;
  if ($this->audit['status'] == 'INCOMPLETE' && $ao->isOwned($audit_id, $this->userid))
  {
    $complete_audit .= <<<"END"
<li><a href="{$this->baseurl}/audit/complete">
<span title=".icon  .icon-color  .icon-locked " class="icon icon-color icon-locked"></span> Mark Audit Complete</a></li>
END;
  }
  if ($this->audit['status'] == 'COMPLETE' && $this->audit['owner'])
  {
    $complete_audit .= <<<"END"
<li><a href="{$this->baseurl}/audit/incomplete">
<span title=".icon  .icon-color  .icon-unlocked " class="icon icon-color icon-unlocked"></span> Mark Audit Incomplete</a></li>
END;
  }
  if ($this->audit['status'] == 'COMPLETE' && $this->usertype == 'APPROVER')
  {
    $complete_audit .= <<<"END"
<li><a href="{$this->baseurl}/audit/finalize"
onclick=" return confirm('Do you want to finalize Audit (#{$this->audit['audit_id']}-{$this->audit['tag']})?');"><span title=".icon  .icon-color  .icon-sent " class="icon icon-color icon-sent"></span> Mark Audit Finalized</a></li>
<li><a href="{$this->baseurl}/audit/reject"
    onclick=" return confirm('Do you want to reject Audit (#{$this->audit['audit_id']}-{$this->audit['tag']})?');"><span title=".icon  .icon-color  .icon-cross " class="icon icon-color icon-cross"></span> Mark Audit Rejected</a></li>
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
if ($this->usertype != '')
{
  if ($this->usertype == 'ADMIN')
  {
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
END;
  if ($this->lab && in_array($this->usertype, $complete_user))
  {
    $this->header .= <<<"END"
  <li><a href="{$this->baseurl}/audit/create"><span title=".icon  .icon-green .icon-clipboard " class="icon icon-green icon-clipboard"></span> New Audit</a></li>
END;
  }
  $this->header .= <<<"END"
  <li><a href="{$this->baseurl}/audit/search"><span title=".icon  .icon-blue  .icon-search " class="icon icon-blue icon-search"></span> Search for Audit</a></li>
{$complete_audit}
  <li class="divider"></li>
  <li><a href="{$this->baseurl}/audit/runreports"><span title=".icon  .icon-color  .icon-newwin " class="icon icon-color icon-newwin"></span> Run Reports</a></li>
END;

  if (in_array($this->usertype, $complete_user))
  {
    $this->header .= <<<"END"
  <li class="divider"></li>
  <li><a href="{$this->baseurl}/audit/import"><span title=".icon  .icon-blue .icon-import " class="icon icon-blue icon-archive"></span> Import Audit</a></li>
END;
  }
  $editlabline = '';
  if ($this->lab)
  {
    $editlabline = <<<"END"
       <li class="divider"></li>
  <li><a href="{$this->baseurl}/lab/edit"><span title=".icon  .icon-blue  .icon-search " class="icon icon-blue icon-search"></span> Edit Selected Lab</a></li>
END;
  }

  $this->header .= <<<"END"
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
  {$editlabline}
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
  $auditinfo = "<div style=\"margin:6px 0 6px 20px;padding-right:5px;\">Selected Audit: {$this->showaudit}</div>";
  $this->header .= <<<"END"
<div style="display:inline-block;">
  <div style="margin:6px 0px 6px 20px;padding-right:5px;">Selected Lab: {$this->showlab}</div>
    {$auditinfo}
  <div style="clear:both;"></div></div>
END;
}
else
{
  $this->header = $this->header . <<<"END"
<div class="btn-group pull-left" style="margin-left:100px;">
<a class="btn" href="{$this->baseurl}/user/login"><span title=".icon  .icon-blue  .icon-contacts " class="icon icon-blue icon-contacts"></span> Login</a></div>
END;
}
$this->header = $this->header . <<<"END"
   </div>
  </div>
</div>
END;

$this->view->header = $this->header;
}
}