<?php

require_once 'modules/Checklist/logger.php';
require_once 'modules/Checklist/export.php';
require_once 'modules/Checklist/datefns.php';
require_once 'modules/Checklist/general.php';
require_once '../application/controllers/ActionController.php';

class AuditController extends Application_Controller_Action {
  public $debug = 0;
  // private $mainpage = '';
  public function init() {
    /* Initialize action controller here */
    logit("MT aud init: ". microtime(true));
    parent::init();
  }

  public function indexAction() {
  }

  public function getButtons($page) {
    /*
     * Given the page to paint, get buttons markup
     */
    $display = ($page['display_only'] == 't');
    $buttons = '';
    $thispage = $page['page_num'];
    $this->view->thispage = $thispage;
    $nextpage = $page['next_page_num'];
    if ($display) {
      $buttons = <<<"END"
<div style="width:825px;">
  <input type="hidden" name="nextpage" value="{$nextpage}" />
  <div style="float:right;">
    <input type="submit" value="Next" id="nextbutton" name="sbname">
</div></div>
END;
    } else {
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

  public function editAction() {
    require_once 'modules/Checklist/fillout.php';
    $lang_default = 'EN';
    $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $prof = false;
    if ($prof) {
      $mt = microtime(true);
      logit("Start: {$mt}");
    }
    //$this->init();
    $tmplr = new Application_Model_DbTable_TemplateRows();
    $tmpl = new Application_Model_DbTable_Template();
    $data = new Application_Model_DbTable_AuditData();
    $aud = new Application_Model_DbTable_Audit();
    $lab = new Application_Model_DbTable_Lab();
    $page = new Application_Model_DbTable_Page();
    $lang = new Application_Model_DbTable_Language();
    $lang_word = new Application_Model_DbTable_langword();
    $this->dialog_name = 'audit/edit';
    $vars = $this->_request->getPathInfo();
    //logit("VARS: {$vars}");
    $pinfo = explode("/", $vars);
    //logit('PARTS: '. print_r($pinfo, true));
    //$audit_id = (int) $pinfo[3];
    $audit_id = $this->audit['audit_id'];
    $template_id = $data->getTemplateId($audit_id);
    $thistmpl = $tmpl->get($template_id);
    $tmpl_type = $thistmpl['tag'];
    $langtag = $this->echecklistNamespace->lang;
    $thispage = '';
    if (count($pinfo) > 3)
      $thispage = (int) $pinfo[3];
    $auditrow = $aud->getAudit($audit_id);
    $this->echecklistNamespace->audit = $auditrow;
    $this->echecklistNamespace->lab = array (
        'id' => $auditrow['lab_id'],
        'labname' => $auditrow['labname'],
        'labnum' => $auditrow['labnum']
    );
    //$this->setupSession();
    //$this->setHeader();

    if ($thispage == '') {
      $fp = $page->getStartPage($template_id); //1 is the template id
      $thispage = $fp; // this is the first page of slipta template
    } else {
      $thispage = (int) $thispage;
    }
    //logit ( 'In slipta beginning' );
    $nav = $page->getNav($template_id, $thispage); // 1 is the template_id
    $page_row = $nav['row'];
    $display_only = $page_row['display_only'];
    $pageid = $page_row['page_id'];
    $nrows = $nav['rows'];
    if (! $this->getRequest()->isPost()) {
      // write out the page
      //$tword = $lang_word->getWords ( $langtag );
      if ($this->debug) {
        logit("Got showpage value: {$thispage}");
        logit("Got language value: {$langtag}");
      }

      $rows = $tmplr->getRows($template_id, $thispage, $langtag); // 1 is the template_id
      // if this page is display only we load values for page 0
      // page 0 has global data on it
      logit("DISPO: {$display_only}");
      if ($display_only == 't') {
        $value = $data->getData($audit_id, 0); // page 0 has all global items for this audit
      } else {
        $value = $data->getData($audit_id, $pageid); // 1 is the audit_id
      }

      if ($this->debug) {
        foreach($page_row as $a => $p) {
          logit("Page data: {$a} => {$p}\n");
        }
      }
      // logit("Page Tag {$page_row['tag']}\n");
      // Generate the entries to make a tree - using dtree
      $jsrows = array ();
      $page_url = "{$baseurl}/audit/edit"; #{$audit_id}";
      if ($prof) {
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        logit("D1: {$mtx}");
        $mt = $mt2;
      }
      $secinc = array();
      if ($tmpl_type == 'SLIPTA') {
        // get incomplete counts
        $secdata = $data->getSecIncCounts($audit_id);
        foreach($secdata as $s => $v) {
          $secno = (int) substr($s, 1, 2);
            $secinc[$secno] = $v;
        }
      }
      foreach($nrows as $r) {
        if ($this->debug) {
          foreach($r as $x => $y) {
            logit("{$x} -- {$y}");
          }
          logit("{$r['parent']} -> {$r['page_num']}");
        }
        $purl = "{$page_url}/{$r['page_num']}";
        # $purl = "{$r['page_num']}";
        $ptag = $r['tag'];
        // logit("Tag: {$ptag}"); // this is tag data from the page table
        $pint = (int) substr($ptag, 7);
        if ($tmpl_type == 'SLIPTA' && strtolower(substr($ptag, 0, 7)) == 'section') {
          $incval = get_arrval($secinc, $pint, 1);
        } else {
          $incval = 0;
        }
        // logit("Secinc: {$ptag} {$incval}");
        $inc = '';
        if ($incval > 0) {
          $inc = "<img src=\"{$this->baseurl}/cancel-on.png\" />";
        }
        $line = "d.add({$r['page_num']},{$r['parent']}, '{$r['tag']}{$inc}'";
        if ($r['leaf'] == 't') { // draw a URL for a leaf node not otherwise
          $line = $line . ", '{$purl}'";
        }
        $line = $line . ");";
        $jsrows[] = $line;
        if ($this->debug) {
          logit("Line: {$line}");
        }
      }
      if ($prof) {
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        logit("D2: {$mtx}");
        $mt = $mt2;
      }
      if ($this->debug) {
        logit('Dumping J');
        foreach($jsrows as $j) {
          logit("J: {$j}");
        }
      }
      $tout = calculate_page($rows, $value, $langtag); //$tword );
      // logit('VALUE: ' . print_r($value, true));

      if ($prof) {
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        logit("D3: {$mtx}");
        $mt = $mt2;
      }
      $next = $thispage + 1;
      $this->view->thispage = $thispage;
      $this->view->treelines = implode("\n", $jsrows);
      $olines = implode("\n", $tout);
      logit('VALUE: '. print_r($value, true));
      if ($display_only == 't') {
        //logit("OUT:\n$olines");
        $olines = str_replace('"', '\"', $olines);
        eval("\$olines = \"$olines\"; ");
      }
      $this->view->outlines = $olines;
      $this->getButtons($page_row);
      $this->view->hidden = implode("\n",
          array (
              "<input type=\"hidden\" name=\"audit_id\" value=\"{$audit_id}\">"
          ));
      // logit("HEADER: {$this->view->header}");
      $this->view->flash = $this->echecklistNamespace->flash;
      unset($this->echecklistNamespace->flash);
      $this->_helper->layout->setLayout('template');
    } else { // Handle the POST request here
      logit('In post for Audit');
      if ($prof) {
        $mt = microtime(true);
        logit("P1: {$mt}");
      }
      if ($prof) $mt = $mt2;
      $formData = $this->getRequest()->getPost();
      $dvalue = array ();
      $not_include = array (
          'sbname',
          'audit_id',
          'id'
      );
      //$thispage = 0;
      foreach($formData as $a => $b) {
        //logit ( "FD: {$a} -- {$b}" );
        //f ($a == 'thispage') {$thispage = (int)$b; continue;}
        if (in_array($a, $not_include)) {
          continue;
        }
        if ($a == 'nextpage') {
          $nextpage = (int) $b;
        }
        $dvalue[$a] = $b;
      }
      $sbname = $formData['sbname'];
      logit("action: {$sbname}");
      $uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      // logit("URI: {$uri}");
      $u = preg_split("/\//", $uri);
      if ($this->debug) {
        foreach($u as $un) {
          logit("U: {$un}");
        }
      }
      if ($prof) {
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        logit("P2: {$mtx}");
        $mt = $mt2;
      }
      $newuri = implode('/', array_slice($u, 3));
      $pagerow = $page->getPage($template_id, $thispage);
      $pageid = $pagerow['page_id'];
      $nextpage = $pagerow['next_page_num'];

      //$page_url = "/audit/edit/{$audit_id}/{$nextpage}";
      $page_url = "/audit/edit/{$nextpage}";
      // logit("URINEW: {$newuri}");
      switch ($sbname) {
        case 'Cancel' :
          logit("Sbname: {$sbname} switch");
          // refresh the page
          $this->redirect($newuri);
          break;
        case 'Save' :
        // save the data and goto main page
        // break;
        case 'Save & Continue' :
          // save data and go the next logical page
          // for now just save the data
          if ($prof) {
            $mt2 = microtime(true);
            $mtx = $mt2 - $mt;
            logit("P3: {$mtx}");
            $mt = $mt2;
          }
          $did = $formData['audit_id'];
          // logit("LABID: {$this->labid}");
          $labrow = $lab->get($this->labid);
          // logit('UPLAB: ' . print_r($labrow, true));
          $data->updateData($dvalue, $did, $pageid, $labrow);
          $srows = $data->get($did, 'slmta_status');
          // logit('AData: ' . print_r($srows, true));
          /**
           *  // $aud->updateTS_SLMTA($did);
           *  // Pulls latest lab data into audit
           *  $this->updateFromLab($did);
           *  // saves latest slmta_status to Audit
           * $this->updateToAudit($did);
           */
          if ($prof) {
            $mt2 = microtime(true);
            $mtx = $mt2 - $mt;
            logit("P4: {$mtx}");
            $mt = $mt2;
          }
          if ($sbname == 'Save') {
            $this->redirect($newuri);
          } else {
            if ($nextpage == 999) {
              $this->redirect($this->mainpage);
            }
            $this->redirect($page_url);
          }
          break;
        case 'Next':
        /* just go to the next page - nothing to save */
        if ($nextpage == 999) {
            $this->redirect($this->mainpage);
          }
          $this->redirect($page_url);
        default :
      }
    }
  }

  public function viewAction() {
    // build the html that represents the completed audit
    // and display
    require_once 'modules/Checklist/htmlout.php';
    $this->dialog_name = 'audit/view';
    $tmplr = new Application_Model_DbTable_TemplateRows();
    $data = new Application_Model_DbTable_AuditData();
    $aud = new Application_Model_DbTable_Audit();
    $lab = new Application_Model_DbTable_Lab();
    $page = new Application_Model_DbTable_Page();
    $lang = new Application_Model_DbTable_Language();
    $lang_word = new Application_Model_DbTable_langword();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    //logit('PARTS: '. print_r($pinfo, true));
    // $audit_id = (int) $pinfo[3];
    $audit_id = $this->audit['audit_id'];
    $template_id = $data->getTemplateId($audit_id);
    $langtag = $this->echecklistNamespace->lang;
    //$thispage = '';
    //if (count($pinfo) > 4)
    //  $thispage = (int) $pinfo[4];
    $auditrow = $aud->getAudit($audit_id);
    $this->echecklistNamespace->audit = $auditrow;
    $this->echecklistNamespace->lab = array (
        'id' => $auditrow['lab_id'],
        'labname' => $auditrow['labname'],
        'labnum' => $auditrow['labnum']
    );

    $rows = $tmplr->getAllRows($template_id, $langtag);
    $value = $data->getAllData($audit_id);
    $audit_type = $auditrow['tag'];
    // logit("AUDIT_ID: {$audit_type}");
    $tout = calculate_view($rows, $value, $langtag, $audit_type); //$tword );
    $this->view->outlines = implode("\n", $tout);
    $this->_helper->layout->setLayout('mainview');
  }

  public function mainAction() {
    /*
     * This is the first main page presented to a user
     * - we may have to adjust for other users
     */
    //$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $this->dialog_name = 'audit/main';
    $format = 'Y-m-d H:i:s';
    // logit("{$this->dialog_name}");
    $audit = new Application_Model_DbTable_Audit();
    //$vars = $this->_request->getPathInfo();
    //$pinfo = explode("/", $vars);
    $id = (int) $this->echecklistNamespace->user['id'];
    $langtag = $this->echecklistNamespace->lang;
    //if (! $this->getRequest()->isPost()) {
    $rows = $audit->getIncompleteAudits($id);
    // logit('AROWS: ' . print_r($rows, true));
    $auditlines = $this->makeAuditLines($rows);
    $this->makeDialog(null, $auditlines);

  }

  public function createAction() {
    /*
     * either show open audits OR show select audit type + lab
     */
    $this->dialog_name = 'audit/create';
    logit("{$this->dialog_name}");
    $audit = new Application_Model_DbTable_Audit();
    $adata = new Application_Model_DbTable_AuditData();
    $ao = new Application_Model_DbTable_AuditOwner();
    $lab = new Application_Model_DbTable_Lab();
    $tmpl = new Application_Model_DbTable_Template();
    $tmplr = new Application_Model_DbTable_TemplateRows();

    //$vars = $this->_request->getPathInfo();
    //$pinfo = explode("/", $vars);
    // // $id = (int)  $pinfo[3];
    $langtag = $this->echecklistNamespace->lang;
    // $urldata = $this->getRequest()->getParams();
    if (! $this->getRequest()->isPost()) {
      $this->makeDialog();
    } else {
      // display the form here
      if ($this->collectData()) return;
      if ($this->data['audit_type'] == '-') {
        $this->echecklistNamespace->flash = 'Choose a "Type of Audit" and continue';
        //$this->makeDialog();
        $this->_redirector->gotoUrl('audit/create');
      }
      //logit('Data: ' . print_r($this->data));

      $trow = $tmpl->getByTag($this->data['audit_type']);
      // get page_id for 'labhead'
      $varname = 'labhead';
      $page_id = $tmplr->findPageId($trow['id'], $varname);
      logit("PAGE_ID: {$page_id}");
      //logit("LABID: {$this->labid} TR:" . print_r($trow, true));
      $now = new DateTime();
      $nowiso = $now->format($this->ISOdtformat);
      // this will become the audit row
      $arow = array (
          'template_id' => $trow['id'],
          'created_at' => $nowiso,
          'updated_at' => $nowiso,
          'updated_by' => $this->userid,
          'audit_type' => $this->data['audit_type'],
          //'start_date' => convert_ISO($this->data['start_date'])->format($this->ISOformat),
          //'end_date' => convert_ISO($this->data['end_date'])->format($this->ISOformat),
          'lab_id' => $this->labid,
          'status' => 'INCOMPLETE'
      );
      $labrow = $lab->get($this->labid);
      logit("LABROW: ". print_r($labrow, true));
      // insert audit row
      $newauditid = $audit->insertData($arow);
      $aorow = array (
          'audit_id' => $newauditid,
          'owner' => $this->userid
      );
      $ao->insertData($aorow);
      // insert lab data into audit data
      $ad = array('labhead' => 1);
      // 'ad' data is used to trigger copying lab data into audit_data
      $adata->handleLabData($ad, $newauditid, $page_id, $labrow);
      $url = "/audit/edit/";
      $this->echecklistNamespace->flash = "New {$this->data['audit_type']} audit #{$newauditid} created";
      $arow = $audit->getAudit($newauditid);
      $this->echecklistNamespace->audit = $arow;
      $this->_redirector->gotoUrl($url);
    }
  }

  public function searchAction() {
    $this->dialog_name = 'audit/search';
    // logit("In LS");
    $aud = new Application_Model_DbTable_Audit();
    if (! $this->getRequest()->isPost()) {
      $this->makeDialog();
    } else {
      require_once 'modules/Checklist/processor.php';
      // logit('Select: In post');
      if ($this->collectData())
        return;
      logit('DATA: '. print_r($this->data, true));
      /*if ($this->data['audit_type'] == '-' || $this->data['audit_type'] == '') {
        $this->echecklistNamespace->flash = "Select a Audit Type and continue";
        $this->makeDialog($this->data);
        return;
        //$this->_redirector->gotoUrl('audit/select');
      }*/

      $prefix = 'cb_';
      $this->collectExtraData($prefix);
      $audit_type = $this->data['audit_type'];

      // logit('OutData: ' . print_r($this->data, true));
      // logit('Going in for Extra stuff');
      $this->collectExtraData($prefix);
      // logit('OutExtraData: ' . print_r($this->extra, true));
      if (count($this->extra) > 0) {
        $list = array();
        foreach($this->extra as $n => $v) {
          $list[] = (int) substr($n, 3);
          // logit('LIST: '. print_r($list, true));
        }
        // logit('Auditsel: ' . print_r($this->data, true));

        $out = new Processing();
        $msg = $out->process($list, $name);
        $this->echecklistNamespace->flash = 'Excel sheet done.';
        $this->_redirector->gotoUrl($this->mainpage);
        //$this->echecklistNamespace->flash = $msg;
        //$this->_redirector->gotoUrl($this->mainpage);
      }
      $arows = $aud->selectAudits($this->data);

      //logit("AROWS: " . print_r($arows, true));
      $auditlines = $this->makeAuditLines($arows, array(
          'cb'=> false
      ));
      $this->makeDialog($this->data, $auditlines);
    }
  }

  public function runreportsAction() {
    $this->dialog_name = 'audit/runreports';
    // logit("In LS");
    $aud = new Application_Model_DbTable_Audit();
    if (! $this->getRequest()->isPost()) {
      $this->makeDialog();
    } else {
      require_once 'modules/Checklist/processor.php';
      // logit('Select: In post');
      if ($this->collectData())
        return;

      // logit('DATA: ' .  print_r($this->data, true));
      if ($this->data['todo'] == '-' || $this->data['todo'] == '') {
        $this->echecklistNamespace->flash = "Select a Report Type and continue";
        $this->makeDialog($this->data);
        return;
      }
      if ($this->data['audit_type'] == '-' || $this->data['audit_type'] == '') {
        $this->echecklistNamespace->flash = "Select an Audit Type and continue";
        $this->makeDialog($this->data);
        return;
      }
      $prefix = 'cb_';
      $this->collectExtraData($prefix);
      $name = $this->data['todo'];

      // logit('OutData: ' . print_r($this->data, true));
      // logit('Going in for Extra stuff');
      $this->collectExtraData($prefix);
      logit('OutExtraData: ' . print_r($this->extra, true));
      if (count($this->extra) > 0) {
        $list = array();
        foreach($this->extra as $n => $v) {
          $list[] = (int) substr($n, 3);
        }

        $proc = new Processing();
        // clean up old files
        $path = dirname(__DIR__) . '/../public/tmp/';
        logit("PATH: {$path}");
        $secs = 3600;
        $proc->rmOldFiles($path, $secs);

        logit("LN: {$name} " .print_r($list, true));
        $rc = $proc->process($this, $list, $name);
        if (!$rc) {
          $this->_helper->layout->disableLayout();
          $this->_helper->viewRenderer->setNoRender(true);
          return;
        } else {
          //
          $this->view->outlines = '';
          $this->view->showlines = '';
          $this->_helper->layout->setLayout('overall');
          return;
        }
      }
      // nothing selected so paint the data lines
      $arows = $aud->selectAudits($this->data);

      // logit("AROWS: " . print_r($arows, true));
      $auditlines = $this->makeAuditLines($arows, array(
          'cb'=> true
      ));
      $this->makeDialog($this->data, $auditlines);
    }
  }

  public function chooseAction() {
    $this->dialog_name = 'user/addowner';
    // logit("{$this->dialog_name}");
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int) $pinfo[3];
    $audit = new Application_Model_DbTable_Audit();
    $lab = new Application_Model_DbTable_Lab();
    $auditrow = $audit->getAudit($id);
    $this->echecklistNamespace->flash = "Selected audit #{$auditrow['audit_id']}";
    $this->echecklistNamespace->audit = $auditrow;
    $labrow = $lab->get($auditrow['lab_id']);
    $this->echecklistNamespace->lab = $labrow;
    $this->_redirector->gotoUrl($this->mainpage);
  }


  public function exportxlsAction() {
    $this->dialog_name = 'audit/select';
    // logit("In LS");
    if (! $this->getRequest()->isPost()) {
      $this->makeDialog();
    } else {
      //logit('Exportxls: In post');
      $prefix = 'cb_';
      $lprefix = strlen($prefix);
      if ($this->collectData()) return;
      $this->collectextraData($prefix);
      // logit('Exportxls: ' . print_r($this->data, true));
      // logit('Exportxls+: ' . print_r($this->extra, true));
      $alist = array ();
      foreach($this->extra as $n => $v) {
        $alist[] = (int) substr($n, $lprefix);
      }
      // logit('collected data: ' . print_r($alist, true));
      exit();
      /*$aud = new Application_Model_DbTable_Audit();
      $arows = $aud->selectAudits($this->data);
      logit("AROWS: ". print_r($arows, true));
      $this->makeDialog($this->data);
      $this->makeAuditLines($arows, true);
      */
    }
  }

  public function exportdataAction() {
    // Exports an audit to dataexport file (.edx)
    $this->dialog_name = 'audit/exportdata';
    // logit("In audit/exportdata");
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = $this->audit['audit_id'];
    // logit("Audit: ". print_r($this->audit, true));
    if (! $this->getRequest()->isPost()) {
      // export includes a row of lab, audit and matching auditdata
      $out = exportData($id);
      $outl = strlen($out['data']);
      //logit('EXP: ' . print_r($out, true));
      // The data is ready
      // The proposed name is: <lab_num>_<audit_type>_<audit_date>.edx
      $fname = $out['name'];
      // logit('FNAME: ' . $fname);
      // Send the file
      //call the action helper to send the file to the browser
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);

      $this->getResponse()->setHeader('Content-type', 'application/plain'); //octet-stream');
      $this->getResponse()->setHeader(
          'Content-Disposition', 'attachment; filename="' . $fname . '"');
      $this->getResponse()->setBody($out['data']);
    } else {
      // logit('Import: In post');
      $this->collectData();
    }
  }

  public function importAction() {
    // Imports a dataexport file
    $this->dialog_name = 'audit/import';
    // logit("In audit/import");
    $path = dirname(__DIR__) . '/tmp/';
    // logit("PATH: {$path}");
    $adapter = new Zend_File_Transfer_Adapter_Http();
    $adapter->setDestination($path);
    $toimport = new Application_Model_DbTable_ToImport();
    if (! $this->getRequest()->isPost()) {
      if ($toimport->getByOwner($this->userid)) {
        $this->_redirector->gotoUrl('audit/fileparse');
      }
      // logit('now1:');
      // logit("EC: {$this->echecklistNamespace->flash}");
      $this->makeDialog();
    } else {
      // logit('Import: In post');
      if (! $adapter->receive()) {
        $messages = $adapter->getMessages();
        // logit('MSGS: ' . print_r(implode("\n", $messages), true));
        // logit('msgout1: ');
        $this->echecklistNamespace->flash = 'File not loaded - No file selected';
        //$this->makeDialog();
        $this->_redirector->gotoUrl('audit/import');
      }
      $files = $adapter->getFileInfo();
      // logit('FILE: ' . print_r($files, true));
      $uploadedfile = $files['uploadedfile'];

      $data = array ();
      $data['owner_id'] = $this->userid;
      $data['path'] = $uploadedfile['tmp_name'];
      if (strlen($data['path']) < 10) {
        // logit('msgout1: no file');
        $this->echecklistNamespace->flash = 'File not loaded - Retry';
        //$this->makeDialog();
        $this->_redirector->gotoUrl('audit/import');
      }
      $id = $toimport->insertData($data);
      $this->_redirector->gotoUrl('audit/fileparse');
    }
  }

  public function fileparseAction() {
    // audit import file has been seen - now process it.
    $this->dialog_name = 'audit/fileparse';
    // logit("In audit/fileparse");
    if (! $this->getRequest()->isPost()) {
      // read the imported file and extract the
      // lab info and audit into
      $toimport = new Application_Model_DbTable_ToImport();
      $thisfile = $toimport->getByOwner($this->userid);
      $sdata = file_get_contents($thisfile['path']);
      // logit('SLEN: ' . strlen($sdata) . ' ' . print_r($thisfile, true));
      $data = unserialize($sdata);
      $this->lab = $data['lab'];
      // logit('LAB: ' . print_r($this->lab, true));
      $this->audit = $data['audit'];
      // logit('AUDIT: ' . print_r($this->audit, true));
      $tmpl = new Application_Model_DbTable_Template();
      $tid = $this->audit['template_id'];
      // logit("TID: {$tid}");
      $this->tmpl_row = $tmpl->get($tid);
      //get($this->audit['template_id']);
      $this->makeDialog();
    } else {
      // logit('Import: In post');
    }
  }

  public function importallAction() {
    // import the entire export file as is
    // comes here from a link click
    $this->dialog_name = "audit/importall";
    // logit('In audit/importall');
    $audit = new Application_Model_DbTable_Audit();
    $lab = new Application_Model_DbTable_Lab();
    $audit_data = new Application_Model_DbTable_AuditData();
    $toimport = new Application_Model_DbTable_ToImport();
    $auditowner = new Application_Model_DbTable_AuditOwner();


    /**
     * 1. get the file
     * 2. unserialize
     * 3. Insert lab into system - track the labid
     * 4. Change lab data in audit row, insert audit row
     * 5. Insert into audit_owner
     * 6. Update audit_id in audt_data(s) and insert all.
     */
    $thisfile = $toimport->getByOwner($this->userid);
    $sdata = file_get_contents($thisfile['path']);
    // logit('SLEN: ' . strlen($sdata) . ' ' . print_r($thisfile, true));
    $data = unserialize($sdata);
    $labinfo = $data['lab'];
    $labnum = $labinfo['labnum'];
    // logit('LAB: ' . print_r($labinfo, true));
    $auditinfo = $data['audit'];
    // logit("Audit: {$auditid} " . print_r($auditinfo, true));
    $auditdatarows = $data['audit_data'];
    // logit("Inserting data: {$auditid} - " . count($auditdatarows));
    $haslab = $lab->getLabByLabnum($labnum);
    if (! $haslab) {
      // the labnum is not in the system
      // so install this lab data
      // logit("No such lab: {$labnum}");
      unset($labinfo['id']); // remove the id
      $labid = $lab->insertData($labinfo);
      // logit("Lab: {$labid} " . print_r($labinfo, true));
      $auditinfo['lab_id'] = $labid;
    } else {
      // update audit info with the lab id (from this system)
      // logit("Lab exists: ". print_r($haslab, true));
      // replace the lab info with that from the selected lab
      //$auditdatarows = $audit_data->updateAuditWithLabInfo($auditdatarows, $haslab);
      $auditinfo['lab_id'] = $haslab['id'];
    }
    unset($auditinfo['id']); // remove the id
    // logit("adding in audit: ". print_r($auditinfo, true));
    // insert audit row
    // If an sudit status is FINALIZED down grade it to COMPLETED
    if ($auditinfo['status'] == 'FINALIZED') {
      $auditinfo['status'] = 'COMPLETED';
    }
    $auditid = $audit->insertData($auditinfo);
    logit("Imported audit is given #{$auditid}");
    // insert into audit_owner
    $ao = array (
        'audit_id' => $auditid,
        'owner' => $this->userid
    );
    $aoid = $auditowner->insertData($ao);
    // logit("AOID: {$aoid}");

    // insert the audit data rows
    $audit_data->insertAs($auditdatarows, $auditid);
    // logit('LABROW: '. print_r($labrow, true));
      // update the lab data with that from existing lab
    if ($haslab) {
      $defarray = array('labhead'=> 'place holder');
      $audit_data->handleLabData($defarray, $auditid, '-', $haslab);
    }
    // delete the physical file
    unlink($thisfile['path']);

    // delete entry from toimport
    $toimport->delete($thifile['id']);
    $this->echecklistNamespace->flash = 'Import complete';
    $this->_redirector->gotoUrl($this->mainpage);
  }

  public function import2labAction() {
    // import the export file into current lab
    // ignore the lab info with the export
    // comes here from a link click
    $this->dialog_name = "audit/import2lab";
    logit('In audit/import2lab');
    $audit = new Application_Model_DbTable_Audit();
    $lab = new Application_Model_DbTable_Lab();
    $audit_data = new Application_Model_DbTable_AuditData();
    $toimport = new Application_Model_DbTable_ToImport();
    /**
     * 1. get the file
     * 2. unserialize
     * 3. Get the lab id
     * 4. Change lab data in audit row, and into audit_data rows, insert audit row
     * 5. Insert into audit_owner
     * 6. Update audit_id in audt_data(s) and insert all.
     */

    $thisfile = $toimport->getByOwner($this->userid);
    $sdata = file_get_contents($thisfile['path']);
    // logit('SLEN: ' . strlen($sdata) . ' ' . print_r($thisfile, true));
    $data = unserialize($sdata);
    $auditdatarows = $data['audit_data'];
    // we do not need the lab data as we will use the current lab

    // insert audit row
    $auditinfo = $data['audit'];
    unset($auditinfo['id']);
    $auditinfo['lab_id'] = $this->labid; // the current lab
    // If an sudit status is FINALIZED down grade it to COMPLETED
    if ($auditinfo['status'] == 'FINALIZED') {
      $auditinfo['status'] = 'COMPLETED';
    }
    // logit("Audit: {$auditid} " . print_r($auditinfo, true));
    $auditid = $audit->insertData($auditinfo);

    // insert into audit_owner
    $auditowner = new Application_Model_DbTable_AuditOwner();
    $ao = array (
        'audit_id' => $auditid,
        'owner' => $this->userid
    );
    $aoid = $auditowner->insertData($ao);
    // logit("AOID: {$aoid}");

    // insert the audit data rows
    // logit("Inserting data: {$auditid} - ". count($auditdatarows));
    $audit_data->insertAs($auditdatarows, $auditid);
    // change the original audit data to that of the current lab
    $labrow = $lab->get($this->labid);
    // logit('LABROW: '. print_r($labrow, true));
    $defarray = array('labhead' => 'place holder');
    $audit_data->handleLabData($defarray, $auditid, '-', $labrow);
    //$auditdatarows = $audit_data->updateAuditWithLabInfo($auditdatarows, $labrow);

    // delete the physical file
    unlink($thisfile['path']);

    $toimport->delete($thifile['id']); // delete entry from toimport
    $this->echecklistNamespace->flash = 'Import complete';
    $this->_redirector->gotoUrl($this->mainpage);
  }

  public function cancelimportAction() {
    // cancel current import and reset import engine
  /*
   * 1. Remove the file from the path in toimport for this user as owner_id
   * 2. Delete the row from toimport for this user as owner_id
   */
    $toimport = new Application_Model_DbTable_ToImport();
    $thefile = $toimport->getByOwner($this->userid);
    $filepath = $thefile['path'];
    unlink($filepath);
    $toimport->delete($thefile['id']);
    $this->echecklistNamespace->flash = 'Import has been reset';
    $this->_redirector->gotoUrl($this->mainpage);
  }

  public function deleteAction() {
    // delete the audit in question
    $audit = new Application_Model_DbTable_Audit();
    $auditdata = new Application_Model_DbTable_AuditData();
    $audit_id = $this->audit['audit_id'];
    $auditdata->deleteAuditRows($audit_id);
    $audit->deleteAudit($audit_id);
    $this->echecklistNamespace->flash = "Audit with Id #{$audit_id}: delete successfully";
    $this->_redirector->gotoUrl($this->mainpage);
  }

  public function completeAction() {
    // mark audit complete
    $audit = new Application_Model_DbTable_Audit();
    $ao = new Application_Model_DbTable_AuditOwner();
    $newstatus = "COMPLETE";
    $users = array('ADMIN','USER','APPROVER');
    if ( in_array($this->usertype, $users)) {
        // if type is SLIPTA - check for incomplete
      $audit_id = $this->audit['audit_id'];
      // BAT and TB can be incomplete and it is OK
      // logit('AU: '. print_r($this->audit, true));
      if ($this->audit['status'] != 'INCOMPLETE' && $ao->isOwned($audit_id, $this->userid)) {
        $this->echecklistNamespace->flash = "Audit id #{$audit_id} status is not INCOMPLETE";
        $this->_redirector->gotoUrl($this->mainpage);
        return ;
      }
      if ($this->audit['tag'] == 'SLIPTA') {
        // $audit_id = $this->audit['audit_id'];
        // logit("COMP: {$audit_id}");
        // icmap contains the incomplete elements for use in completing
        $icmap = $this->iscomplete($audit_id);
        if ($icmap) {
          // is not complete so show incomplete map
          $this->view->outlines .= $icmap;
          $this->_helper->layout->setLayout('overall');
          // logit("IC");
        } else {
          // logit("no IC"); // It is complete - move status to complete
          $audit->moveStatus($audit_id, $newstatus);
          $this->echecklistNamespace->flash = "Audit id #{$audit_id} has been changed to {$newstatus}";
          // clear selected audit
          $this->echecklistNamespace->audit = null;
          $this->_redirector->gotoUrl($this->poststatchange);
        }
      } else {
        // not SLIPTA
        logit('not slipta');
        $audit->moveStatus($audit_id, $newstatus);
        $this->echecklistNamespace->flash = "Audit id #{$audit_id} has been changed to {$newstatus}";
        $this->echecklistNamespace->audit = null;
        $this->_redirector->gotoUrl($this->poststatchange);
      }
    } else {
      $this->echecklistNamespace->flash = 'Invalid action';
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function incompleteAction() {
    // mark audit complete
    $audit = new Application_Model_DbTable_Audit();
    $ao = new Application_Model_DbTable_AuditOwner();
    $newstatus = "INCOMPLETE";
    $users = array('ADMIN','USER','APPROVER');
    if ( in_array($this->usertype, $users)) {
      // if type is SLIPTA - check for incomplete
      $audit_id = $this->audit['audit_id'];
      // BAT and TB can be incomplete and it is OK
      // logit('AU: '. print_r($this->audit, true));
      if ($this->audit['status'] != 'COMPLETE' && $ao->isOwned($audit_id, $this->userid)) {
        $this->echecklistNamespace->flash = "Audit id #{$audit_id} status is not COMPLETE";
        $this->_redirector->gotoUrl($this->poststatchange);
        //return ;
      }
      $audit->moveStatus($audit_id, $newstatus);
      $this->echecklistNamespace->flash = "Audit id #{$audit_id} has been changed to {$newstatus}";
      $this->echecklistNamespace->audit = null;
      $this->_redirector->gotoUrl($this->poststatchange);
      //return;
    } else {
      $this->echecklistNamespace->flash = 'Invalid action';
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function finalizeAction() {
    // finalize the complete audit
    $audit = new Application_Model_DbTable_Audit();
    $newstatus = "FINALIZED";
    $users = array('APPROVER');
    if ( in_array($this->usertype, $users)) {
      // if type is SLIPTA - check for incomplete
      $audit_id = $this->audit['audit_id'];
      // BAT and TB can be incomplete and it is OK
      // logit('AU: '. print_r($this->audit, true));
      if ($this->audit['status'] != 'COMPLETE') {
        $this->echecklistNamespace->flash = "Audit id #{$audit_id} status is not COMPLETE";
        $this->echecklistNamespace->audit = null;
        $this->_redirector->gotoUrl($this->poststatchange);
        //return ;
      }
      $audit->moveStatus($audit_id, $newstatus);
      $this->echecklistNamespace->flash = "Audit id #{$audit_id} has been changed to {$newstatus}";
      $this->echecklistNamespace->audit = null;
      $this->_redirector->gotoUrl($this->poststatchange);
      // return;
    } else {
      $this->echecklistNamespace->flash = 'Invalid action';
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function rejectAction() {
    // reject the complete audit
    $audit = new Application_Model_DbTable_Audit();
    $newstatus = "REJECTED";
    $users = array('APPROVER');
    if ( in_array($this->usertype, $users)) {
      // if type is SLIPTA - check for incomplete
      $audit_id = $this->audit['audit_id'];
      // BAT and TB can be incomplete and it is OK
      // logit('AU: '. print_r($this->audit, true));
      if ($this->audit['status'] != 'COMPLETE') {
        $this->echecklistNamespace->flash = "Audit id #{$audit_id} status is not COMPLETE";
        $this->echecklistNamespace->audit = null;
        $this->_redirector->gotoUrl($this->poststatchange);
        //return ;
      }
      $audit->moveStatus($audit_id, $newstatus);
      $this->echecklistNamespace->flash = "Audit id #{$audit_id} has been changed to {$newstatus}";
      $this->echecklistNamespace->audit = null;
      $this->_redirector->gotoUrl($this->poststatchange);
      // return;
    } else {
      $this->echecklistNamespace->flash = 'Invalid action';
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function complete2Action() {

  }

  public function iscomplete($audit_id) {
    // check if this slipta audit is complete
    $sec_div = '<div style="border:1px solid #ccc;background-color:#ffc;padding:3px;margin: 2px;display:inline-block;">';
    $ncnote_div = '<div style="border:1px solid #ccc;background-color:#ccf;padding:3px;margin: 2px;display:inline-block;">';
    $comm_div = '<div style="border:1px solid #ccc;background-color:#ccc;padding:3px;margin: 2px;display:inline-block;">';
    $inc_div = '<div style="border:1px solid #cfc;background-color:#ccc;padding:3px;margin: 2px;display:inline-block;">';
    $tmplr = new Application_Model_DbTable_TemplateRows();
    $auditd = new Application_Model_DbTable_AuditData();
    $tmpl_id = 1; // for SLIPTA
    $tmplrows = $tmplr->getAllRowsNotext($tmpl_id, $this->langtag);
    $adrows = $auditd->getAllData($audit_id);
    $tracker = array();
    $line = '';
    $secbegin = '';
    foreach($tmplrows as $tr) {
      // logit("TR: ". print_r($tr, true));
      if ($tr['required'] == 'F')
        continue;
      $ect = $tr['element_count'];
      $vname = $tr['varname'];
      switch ($tr['row_type']) {
        case 'sec_head' :
          if ($line != '') {
            logit('NEW:');
            $tracker[] = $secbegin . $line;
          }
          $line = '';
          //$name = $vname;
          //logit("sec: {$vname} - ". get_arrval($adrows, "{$vname}_secinc", 100));
          $secbegin = $sec_div . 'Section ' . (int) substr($vname, 1). '</div>';
          if (get_arrval($adrows, "{$vname}_secinc", 999) != 0) {
            //$secbegin = $sec_div . 'Section ' . (int) substr($vname, 1). '</div>';
            $line .= $inc_div . (int) substr($vname, 1). ": Inc </div>";
          } else {
            $line .= '';
          }
          break;
        case 'sub_sec_head_ro' :
          // multiple elements
          $name = $vname;
          //logit("RO {$name}");
          //logit("EXT: {$name} " . get_arrval($adrows, $name, ''));
          $ssinc = get_arrval($adrows, "{$name}_inc", 9);
          //logit("ssinc: {$ssinc}");
          $ss = (int) substr($name, 1, 2) . '.' . (int) substr($name, 3, 2);
          if ($ssinc > 0) {
            // $tracker[] = "SubSection {$ss} incomplete";
            $line .= "{$inc_div} {$ss}: Inc </div>";
          }
          $val = get_arrval($adrows, "{$name}_yn", '') . get_arrval($adrows, "{$name}_yna", '');
          //logit("ECT: {$name} ==> {$val} :" . $adrows['{$name}_inc']);
          logit("rVAL: {$name} {$val}");
          $s1 = (int) substr($name, 1, 2);
          $s2 = (int) substr($name, 3, 2);
          $sse = "{$s1}.{$s2}";
          if ($val && $val != 'YES' and $val != '-') {
            logit("r1VAL: {$name} {$val}");
            // check for comment
            if ($adrows["{$name}_comment"] == '') {
              // $tracker[] = "Missing comment: {$name}";
              $line .= "{$comm_div}{$sse}: Comm </div>";
            }
          }
          $nc =   get_arrval($adrows, "{$name}_nc", '');
          $note = get_arrval($adrows, "{$name}_note", '');
          logit("{$name} NC: {$nc} - {$note}");
          if ($nc == 'T' && $note == '') {
            // there should be a note - non compliant note - it is missing
            // $tracker[] = "Missing Non Compliant note: {$name}";
            $line .= "{$ncnote_div}{$sse}: nc note </div>";
          }
          for($i = 1; $i <= $ect; $i ++) {
            $name = ($i < 10) ? "{$vname}0{$i}" : "{$vname}{$i}";
            $val = get_arrval($adrows, "{$name}_yn", '') . get_arrval($adrows, "{$name}_yna", '');
            //logit("ECT: {$name} ==> {$val} :" . $adrows['{$name}_inc']);
            logit("rVAL: {$name} {$val}");
            $s1 = (int) substr($name, 1, 2);
            $s2 = (int) substr($name, 3, 2);
            $s3 = (int) substr($name, 5, 2);
            $sse = "{$s1}.{$s2}.{$s3}";
            if ($val && $val != 'YES' and $val != '-') {
              logit("r1VAL: {$name} {$val}");
              // check for comment
              if ($adrows["{$name}_comment"] == '') {
                // $tracker[] = "Missing comment: {$name}";
                $line .= "{$comm_div}{$sse}: Comm </div>";
              }
            }
            $nc =   get_arrval($adrows, "{$name}_nc", '');
            $note = get_arrval($adrows, "{$name}_note", '');
            logit("{$name} NC: {$nc} - {$note}");
            if ($nc == 'T' && $note == '') {
              // there should be a note - non compliant note - it is missing
              // $tracker[] = "Missing Non Compliant note: {$name}";
              $line .= "{$ncnote_div}{$sse}: nc note </div>";
            }
          }

          break;
        case 'sub_sec_head' :
          $name = $vname;
          //logit("Non-RO {$name}");
          $ssinc = get_arrval($adrows, "{$name}_inc", 9);
          //logit("ssinc: {$ssinc}");
          $ss = (int) substr($name, 1, 2) . '.' . (int) substr($name, 3, 2);
          if ($ssinc > 0) {
            // $tracker[] = "SubSection {$ss} incomplete";
            $line .= "{$inc_div} {$ss}: Inc </div>";
          }
          $val = get_arrval($adrows, "{$name}", '-');
          //logit("ECT: {$name} ==> {$val} : " . get_arrval($adrows, "{$name}_inc", ''));
          logit("nVAL: {$name} {$val}");
          if ($val && $val != 'YES' && $val != '-') {
            logit("n1VAL: {$name} {$val}");
            // check for comment
            if (get_arrval($adrows, "{$name}_comment", '') == '') {
              // $tracker[] = "Missing comment: {$name}";
              $line .= "<div style=\"border:1px solid #ccc;background-color:#ddd;padding:3px;margin:2px;display:inline-block;\">{$ss}: Comm</div> ";
            }
          }
          $nc =   get_arrval($adrows, "{$name}_nc", '');
          $note = get_arrval($adrows, "{$name}_note", '');
          logit("{$name} NC: {$nc} - {$note}");
          if ($nc == 'T' && $note == '') {
            // there should be a note - non compliant note - it is missing
            // $tracker[] = "Missing Non Compliant note: {$name}";
            logit("NC: {$ss}");
            $line .= "<div style=\"border:1px solid #ccc;background-color:#ccf;padding:3px;margin: 2px;display:inline-block;\">{$ss}: nc note</div> ";
          }
          break;
        default :
      }
    }
    if ($line != '') {
      logit('end');
      $tracker[] = "{$secbegin} {$line}";
    }
    // logit("IC: ". print_r($tracker, true));
    if ($tracker) {
      array_unshift($tracker, "<h2>Missing items in Audit</h2>");
      return implode("<br />\n", $tracker);
    }
    return null;
  }

  public function doAction() {
    // just a way to call code for testing
    require_once 'modules/Checklist/processCommon.php';
    $proc = new Process_Common();
    $path = dirname(__DIR__) . '/../public/tmp/';
    logit("PATH: {$path}");
    $secs = 3600;
    $proc->rmOldFiles($path, $secs);
    // exit();
  }
}
