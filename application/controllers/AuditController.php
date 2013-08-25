<?php
require_once 'modules/Checklist/fillout.php';
require_once 'modules/Checklist/export.php';
require_once 'modules/Checklist/logger.php';
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
    <input type="submit" value="Save" id="savebutton" name="sbname">
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
    $lang_default = 'EN';
    $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $prof = false;
    if ($prof) {
      $mt = microtime(true);
      logit("Start: {$mt}");
    }
    $tmplr = new Application_Model_DbTable_TemplateRows();
    $data = new Application_Model_DbTable_AuditData();
    $aud = new Application_Model_DbTable_Audit();
    $page = new Application_Model_DbTable_Page();
    $lang = new Application_Model_DbTable_Language();
    $lang_word = new Application_Model_DbTable_langword();
    $this->dialog_name = 'audit/edit';
    $vars = $this->_request->getPathInfo();
    //logit("VARS: {$vars}");
    $pinfo = explode("/", $vars);
    //logit('PARTS: '. print_r($pinfo, true));
    $audit_id = (int) $pinfo[3];
    $template_id = $data->getTemplateId($audit_id);
    $langtag = $this->echecklistNamespace->lang;
    $thispage = (int) $pinfo[4];
    $auditrow = $aud->getAudit($audit_id);
    $this->echecklistNamespace->audit = $auditrow;
    $this->echecklistNamespace->lab = array (
        'id' => $auditrow['lab_id'],
        'labname' => $auditrow['labname'],
        'labnum' => $auditrow['labnum']
    );
    $this->setupSession();
    $this->setHeader();

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

      $rows = $tmplr->getrows($template_id, $thispage, $langtag); // 1 is the template_id
      // if this page is display only we load values for page 0
      // page 0 has global data on it
      if ($display_only == 't') {
        $value = $data->getData($audit_id, 0);
      } else {
        $value = $data->getData($audit_id, $pageid); // 1 is the audit_id
      }

      if ($this->debug) {
        foreach($page_row as $a => $p) {
          logit("Page data: {$a} => {$p}\n");
        }
      }
      logit("Page Tag {$page_row['tag']}\n");
      // Generate the entries to make a tree - using dtree
      $jsrows = array ();
      $page_url = "{$baseurl}/audit/edit/{$audit_id}";
      if ($prof) {
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        logit("D1: {$mtx}");
        $mt = $mt2;
      }
      foreach($nrows as $r) {
        if ($this->debug) {
          foreach($r as $x => $y) {
            logit("{$x} -- {$y}");
          }
          logit("{$r['parent']} -> {$r['page_num']}");
        }
        $purl = "{$page_url}/{$r['page_num']}";
        $line = "d.add({$r['page_num']},{$r['parent']}, '{$r['tag']}'";
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
      logit(
          'VALUE: ' . print_r($value, true));

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
      unst($this->echecklistNamespace->flash);
      $this->_helper->layout->setLayout('template');
    } else { // Handle the POST request here
      logit('In post for Audit');
      if ($prof) {
        $mt = microtime(true);
        logit("P1: {$mt}");
      }
      //$mt = $mt2;
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
      logit("URI: {$uri}");
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

      $page_url = "/audit/edit/{$audit_id}/{$nextpage}";
      logit("URINEW: {$newuri}");
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
          $data->updateData($dvalue, $did, $pageid);
          $srows = $data->get($did, 'slmta_status');
          logit('AData: ' . print_r($srows, true));
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
              $this->redirect($mainpage);
            }
            $this->redirect($page_url);
          }
          break;
        case 'Next':
        /* just go to the next page - nothing to save */
        if ($nextpage == 999) {
            $this->redirect($mainpage);
          }
          $this->redirect($page_url);
        default :
      }
    }
  }

  public function mainAction() {
    /*
     * This is the first main page presented to a user
     * - we may have to adjust for other users
     */
    $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $this->dialog_name = 'audit/main';
    $format = 'Y-m-d H:i:s';
    logit("{$this->dialog_name}");
    $audit = new Application_Model_DbTable_Audit();
    //$vars = $this->_request->getPathInfo();
    //$pinfo = explode("/", $vars);
    $id = (int) $this->echecklistNamespace->user['id'];
    $langtag = $this->echecklistNamespace->lang;
    if (! $this->getRequest()->isPost()) {
      $rows = $audit->getIncompleteAudits($id);
      logit('AROWS: ' . print_r($rows, true));
      $this->makeDialog();
      $this->makeAuditLines($rows);
    }
  }

  public function createAction() {
    /*
     * either show open audits OR show select audit type + lab
     */
    $this->dialog_name = 'audit/create';
    logit("{$this->dialog_name}");
    $audit = new Application_Model_DbTable_Audit();
    //$vars = $this->_request->getPathInfo();
    //$pinfo = explode("/", $vars);
    // // $id = (int)  $pinfo[3];
    $langtag = $this->echecklistNamespace->lang;
    // $urldata = $this->getRequest()->getParams();
    if (! $this->getRequest()->isPost()) {
      $this->makeDialog();
    } else {
      // display the form here
      $this->collectData();
      // logit('Data: ' . print_r($this->data));
      $user->updateData($data, $id);
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function selectAction() {
    $this->dialog_name = 'audit/select';
    logit("In LS");
    if (! $this->getRequest()->isPost()) {
      $this->makeDialog();
    } else {
      logit('Select: In post');
      $this->collectData();
      logit('Auditsel: ' . print_r($this->data, true));
      $aud = new Application_Model_DbTable_Audit();
      $arows = $aud->selectAudits($this->data);
      logit("AROWS: " . print_r($arows, true));
      $this->makeDialog($this->data);
      $this->makeAuditLines($arows, array (
          'cb' => true
      ));
    }
  }

  public function exportxlsAction() {
    $this->dialog_name = 'audit/select';
    logit("In LS");
    if (! $this->getRequest()->isPost()) {
      $this->makeDialog();
    } else {
      logit('Exportxls: In post');
      $prefix = 'cb_';
      $lprefix = strlen($prefix);
      $this->collectData();
      $this->collectextraData($prefix);
      logit('Exportxls: ' . print_r($this->data, true));
      logit('Exportxls+: ' . print_r($this->extra, true));
      $alist = array ();
      foreach($this->extra as $n => $v) {
        $alist[] = (int) substr($n, $lprefix);
      }
      logit('collected data: ' . print_r($alist, true));
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
    logit("In audit/exportdata");
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int) $pinfo[3];
    if (! $this->getRequest()->isPost()) {
      // export includes a row of lab, audit and matching auditdata
      $out = exportData($id);
      $outl = strlen($out['data']);
      //logit('EXP: ' . print_r($out, true));
      // The data is ready
      // The proposed name is: <lab_num>_<audit_type>_<audit_date>.edx
      $fname = $out['name'];
      logit('FNAME: ' . $fname);
      // Send the file
      //call the action helper to send the file to the browser
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);

      $this->getResponse()->setHeader('Content-type', 'application/plain'); //octet-stream');
      $this->getResponse()->setHeader(
          'Content-Disposition', 'attachment; filename="' . $fname . '"');
      $this->getResponse()->setBody($out['data']);
      // $this->echecklistNamespace->flash = "file sent";
        //$referer = $this->getRequest()->getHeader('referer');
        //$this->_redirector->gotoUrl($referer);
        //$this->_redirector->gotoUrl($this->mainpage);


      //$this->makeDialog();
    } else {
      logit('Import: In post');
      $this->collectData();
    }
  }

  public function importAction() {
    // Imports a dataexport file
    $this->dialog_name = 'audit/import';
    logit("In audit/import");
    $path = dirname(__DIR__) . '/tmp/';
    logit("PATH: {$path}");
    $adapter = new Zend_File_Transfer_Adapter_Http();
    $adapter->setDestination($path);
    $toimport = new Application_Model_DbTable_ToImport();
    if (! $this->getRequest()->isPost()) {
      if ($toimport->getByOwner($this->userid)) {
        $this->_redirector->gotoUrl('audit/fileparse');
      }
      logit('now1:');
      logit("EC: {$this->echecklistNamespace->flash}");
      $this->makeDialog();
    } else {
      logit('Import: In post');
      if (! $adapter->receive()) {
        $messages = $adapter->getMessages();
        logit('MSGS: ' . print_r(implode("\n", $messages), true));
        logit('msgout1: ');
        $this->echecklistNamespace->flash = 'File not loaded - No file chosen';
        //$this->makeDialog();
        $this->_redirector->gotoUrl('audit/import');
      }
      $files = $adapter->getFileInfo();
      logit('FILE: ' . print_r($files, true));
      $uploadedfile = $files['uploadedfile'];

      $data = array ();
      $data['owner_id'] = $this->userid;
      $data['path'] = $uploadedfile['tmp_name'];
      if (strlen($data['path']) < 10) {
        logit('msgout1: no file');
        $this->echecklistNamespace->flash = 'File not loaded - Try again';
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
    logit("In audit/fileparse");
    if (! $this->getRequest()->isPost()) {
      // read the imported file and extract the
      // lab info and audit into
      $toimport = new Application_Model_DbTable_ToImport();
      $thisfile = $toimport->getByOwner($this->userid);
      $sdata = file_get_contents($thisfile['path']);
      logit('SLEN: ' . strlen($sdata) . ' ' . print_r($thisfile, true));
      $data = unserialize($sdata);
      $this->lab = $data['lab'];
            logit('LAB: ' . print_r($this->lab, true));
      $this->audit = $data['audit'];
      logit('AUDIT: ' . print_r($this->audit, true));
      $tmpl = new Application_Model_DbTable_Template();
      $tid = $this->audit['template_id'];
      logit("TID: {$tid}");
      $this->tmpl_row = $tmpl->get($tid);
      //get($this->audit['template_id']);
      $this->makeDialog();
    } else {
      logit('Import: In post');
    }
  }

  public function importallAction() {
    // import the entire export file as is
    // comes here from a link click
    $this->dialog_name = "audit/importall";
    logit('In audit/importall');
    $audit = new Application_Model_DbTable_Audit();
    $lab = new Application_Model_DbTable_Lab();
    $audit_data = new Application_Model_DbTable_AuditData();
    /**
     * 1. get the file
     * 2. unserialize
     * 3. Insert lab into system - track the labid
     * 4. Change lab data in audit row, insert audit row
     * 5. Insert into audit_owner
     * 6. Update audit_id in audt_data(s) and insert all.
     */
    $toimport = new Application_Model_DbTable_ToImport();
    $thisfile = $toimport->getByOwner($this->userid);
    $sdata = file_get_contents($thisfile['path']);
    logit('SLEN: ' . strlen($sdata) . ' ' . print_r($thisfile, true));
    $data = unserialize($sdata);
    $labinfo = $data['lab'];
    logit('LAB: ' . print_r($labinfo, true));

    // lab data
    unset($labinfo['id']); // remove the id
    $labid = $lab->insertData($labinfo);
    logit("Lab: {$labid} " . print_r($labinfo, true));
    // insert audit row
    $auditinfo = $data['audit'];
    unset($auditinfo['id']);
    $auditinfo['lab_id'] = $labid;
    logit("Audit: {$auditid} " . print_r($auditinfo, true));
    $auditid = $audit->insertData($auditinfo);
    // insert into audit_owner
    $auditowner = new Application_Model_DbTable_AuditOwner();
    $ao = array (
        'audit_id' => $auditid,
        'owner' => $this->userid
    );
    $aoid = $auditowner->insertData($ao);
    logit("AOID: {$aoid}");
    // insert the audit data rows
    $auditdatarows = $data['audit_data'];
    logit("Inserting data: {$auditid} - ". count($auditdatarows));
    $audit_data->insertAs($auditdatarows, $auditid);
    // delete the physical file
    unlink($thisfile['path']);
    $toimport->delete($thifile['id']); // delete entry from toimport
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
    //$lab = new Application_Model_DbTable_Lab();
    $audit_data = new Application_Model_DbTable_AuditData();
    /**
     * 1. get the file
     * 2. unserialize
     * 3. Get the lab id
     * 4. Change lab data in audit row, insert audit row
     * 5. Insert into audit_owner
     * 6. Update audit_id in audt_data(s) and insert all.
     */
    $toimport = new Application_Model_DbTable_ToImport();
    $thisfile = $toimport->getByOwner($this->userid);
    $sdata = file_get_contents($thisfile['path']);
    logit('SLEN: ' . strlen($sdata) . ' ' . print_r($thisfile, true));
    $data = unserialize($sdata);

    // insert audit row
    $auditinfo = $data['audit'];
    unset($auditinfo['id']);
    $auditinfo['lab_id'] = $this->labid; // the current lab
    $auditid = $audit->insertData($auditinfo);
    logit("Audit: {$auditid} " . print_r($auditinfo, true));
    // insert into audit_owner
    $auditowner = new Application_Model_DbTable_AuditOwner();
    $ao = array (
        'audit_id' => $auditid,
        'owner' => $this->userid
    );
    $aoid = $auditowner->insertData($ao);
    logit("AOID: {$aoid}");
    // insert the audit data rows
    $auditdatarows = $data['audit_data'];
    logit("Inserting data: {$auditid} - ". count($auditdatarows));
    $audit_data->insertAs($auditdatarows, $auditid);
    // delete the physical file
    unlink($thisfile['path']);
    $toimport->delete($thifile['id']); // delete entry from toimport
    $this->echecklistNamespace->flash = 'Import complete';
    $this->_redirector->gotoUrl($this->mainpage);
  }
}
