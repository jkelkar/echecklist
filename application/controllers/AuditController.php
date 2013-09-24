<?php

class AuditController extends Checklist_Controller_Action
{
  public $debug = 0;
  public function init()
  {
    /* Initialize action controller here */
    parent::init();
    $this->log->logit("MT aud init: " . microtime(true));
  }

  public function indexAction()
  {
  }

  public function editAction()
  {
    $lang_default = 'EN';
    $log = new Checklist_Logger();
    $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $prof = false;
    if ($prof)
    {
      $mt = microtime(true);
      $this->log->logit("Start: {$mt}");
    }
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
    $pinfo = explode("/", $vars);
    $audit_id = $this->audit['audit_id'];
    $template_id = $data->getTemplateId($audit_id);
    $thistmpl = $tmpl->get($template_id);
    $tmpl_type = $thistmpl['tag'];
    $langtag = $this->session->lang;
    $thispage = '';
    if (count($pinfo) > 3)
      $thispage = (int) $pinfo[3];
    $auditrow = $aud->getAudit($audit_id);
    $this->session->audit = $auditrow;
    $this->session->lab = array(

        'id'=> $auditrow['lab_id'],
        'labname'=> $auditrow['labname'],
        'labnum'=> $auditrow['labnum']
    );

    if ($thispage == '')
    {
      $fp = $page->getStartPage($template_id);
      $thispage = $fp; // this is the first page of template
    }
    else
    {
      $thispage = (int) $thispage;
    }
    $nav = $page->getNav($template_id, $thispage);
    $page_row = $nav['row'];
    $display_only = $page_row['display_only'];
    $pageid = $page_row['page_id'];
    $nrows = $nav['rows'];
    if (! $this->getRequest()->isPost())
    {
      // write out the page
      if ($this->debug)
      {
        $this->log->logit("Got showpage value: {$thispage}");
        $this->log->logit("Got language value: {$langtag}");
      }
      $rows = $tmplr->getRows($template_id, $thispage, $langtag);
      /*
       * if this page is display only we load values for
       * page 0 page 0 has global data attached to it
       */
      $this->log->logit("DISPO: {$display_only}");
      if ($display_only == 't')
      {
        $value = $data->getData($audit_id, 0);
      }
      else
      {
        $value = $data->getData($audit_id, $pageid);
      }

      if ($this->debug)
      {
        foreach($page_row as $a => $p)
        {
          $this->log->logit("Page data: {$a} => {$p}\n");
        }
      }
      // Generate the entries to make a tree - using dtree
      $jsrows = array();
      $page_url = "{$baseurl}/audit/edit"; // $audit_id}";
      if ($prof)
      {
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        $this->log->logit("D1: {$mtx}");
        $mt = $mt2;
      }
      $secinc = array();
      if ($tmpl_type == 'SLIPTA')
      {
        // get incomplete counts
        $secdata = $data->getSecIncCounts($audit_id);
        foreach($secdata as $s => $v)
        {
          $secno = (int) substr($s, 1, 2);
          $secinc[$secno] = $v;
        }
      }
      foreach($nrows as $r)
      {
        if ($this->debug)
        {
          foreach($r as $x => $y)
          {
            $this->log->logit("{$x} -- {$y}");
          }
          $this->log->logit("{$r['parent']} -> {$r['page_num']}");
        }
        $purl = "{$page_url}/{$r['page_num']}";
        $ptag = $r['tag'];
        $pint = (int) substr($ptag, 7);
        if ($tmpl_type == 'SLIPTA' && strtolower(substr($ptag, 0, 7)) == 'section')
        {
          $incval = $this->general->get_arrval($secinc, $pint, 1);
        }
        else
        {
          $incval = 0;
        }
        $inc = '';
        if ($incval > 0)
        {
          $inc = "<img src=\"{$this->baseurl}/cancel-on.png\" />";
        }
        $line = "d.add({$r['page_num']},{$r['parent']}, '{$r['tag']}{$inc}'";
        if ($r['leaf'] == 't')
        { // draw a URL for a leaf node not otherwise
          $line = $line . ", '{$purl}'";
        }
        $line = $line . ");";
        $jsrows[] = $line;
        if ($this->debug)
        {
          $this->log->logit("Line: {$line}");
        }
      }
      if ($prof)
      {
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        $this->log->logit("D2: {$mtx}");
        $mt = $mt2;
      }
      if ($this->debug)
      {
        $this->log->logit('Dumping J');
        foreach($jsrows as $j)
        {
          $this->log->logit("J: {$j}");
        }
      }
      $tout = $this->fillout->calculate_page($rows, $value, $langtag);
      if ($prof)
      {
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        $this->log->logit("D3: {$mtx}");
        $mt = $mt2;
      }
      $next = $thispage + 1;
      $this->view->thispage = $thispage;
      $this->view->treelines = implode("\n", $jsrows);
      $olines = implode("\n", $tout);
      $this->log->logit('VALUE: ' . print_r($value, true));
      if ($display_only == 't')
      {
        // $this->log->logit("OUT:\n$olines");
        $olines = str_replace('"', '\"', $olines);
        eval("\$olines = \"$olines\"; ");
      }
      $this->view->outlines = $olines;
      $this->getButtons($page_row);
      $this->view->hidden = implode(
          "\n",
          array(
            "<input type=\"hidden\" name=\"audit_id\" value=\"{$audit_id}\">"
      ));
      $this->view->flash = $this->session->flash;
      unset($this->session->flash);
      $this->_helper->layout->setLayout('template');
    }
    else
    { // Handle the POST request here
      $this->log->logit('In post for Audit');
      if ($prof)
      {
        $mt = microtime(true);
        $this->log->logit("P1: {$mt}");
      }
      if ($prof)
        $mt = $mt2;
      $formData = $this->getRequest()->getPost();
      $dvalue = array();
      $not_include = array(

          'sbname',
          'audit_id',
          'id'
      );
      // $thispage = 0;
      foreach($formData as $a => $b)
      {
        if (in_array($a, $not_include))
        {
          continue;
        }
        if ($a == 'nextpage')
        {
          $nextpage = (int) $b;
        }
        $dvalue[$a] = $b;
      }
      $sbname = $formData['sbname'];
      $this->log->logit("action: {$sbname}");
      $uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $u = preg_split("/\//", $uri);
      if ($this->debug)
      {
        foreach($u as $un)
        {
          $this->log->logit("U: {$un}");
        }
      }
      if ($prof)
      {
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        $this->log->logit("P2: {$mtx}");
        $mt = $mt2;
      }
      $newuri = implode('/', array_slice($u, 3));
      $pagerow = $page->getPage($template_id, $thispage);
      $pageid = $pagerow['page_id'];
      $nextpage = $pagerow['next_page_num'];

      $page_url = "/audit/edit/{$nextpage}";
      switch ($sbname)
      {
        case 'Cancel' :
          $this->log->logit("Sbname: {$sbname} switch");
          // refresh the page
          $this->redirect($newuri);
          break;
        case 'Save' :
        // save the data and goto main page
        // break;
        case 'Save & Continue' :
          // save data and go the next logical page
          // for now just save the data
          if ($prof)
          {
            $mt2 = microtime(true);
            $mtx = $mt2 - $mt;
            $this->log->logit("P3: {$mtx}");
            $mt = $mt2;
          }
          $did = $formData['audit_id'];
          $labrow = $lab->get($this->labid);
          $data->updateData($dvalue, $did, $pageid, $labrow);
          $srows = $data->get($did, 'slmta_status');
          if ($prof)
          {
            $mt2 = microtime(true);
            $mtx = $mt2 - $mt;
            $this->log->logit("P4: {$mtx}");
            $mt = $mt2;
          }
          if ($sbname == 'Save')
          {
            $this->redirect($newuri);
          }
          else
          {
            if ($nextpage == 999)
            {
              $this->redirect($this->mainpage);
            }
            $this->redirect($page_url);
          }
          break;
        case 'Next':
        /* just go to the next page - nothing to save */
        if ($nextpage == 999)
          {
            $this->redirect($this->mainpage);
          }
          $this->redirect($page_url);
        default :
      }
    }
  }

  public function viewAction()
  {
    // build the html that represents the completed audit
    // and display
    // require_once 'modules/Checklist/htmlout.php';
    $htmlout = new Checklist_Modules_Htmlout();
    $this->log->logit('This is a test');
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
    $audit_id = $this->audit['audit_id'];
    $template_id = $data->getTemplateId($audit_id);
    $langtag = $this->session->lang;
    $auditrow = $aud->getAudit($audit_id);
    $this->session->audit = $auditrow;
    $this->session->lab = array(

        'id'=> $auditrow['lab_id'],
        'labname'=> $auditrow['labname'],
        'labnum'=> $auditrow['labnum']
    );

    $rows = $tmplr->getAllRows($template_id, $langtag);
    $value = $data->getAllData($audit_id);
    $audit_type = $auditrow['tag'];
    $tout = $htmlout->calculate_view($rows, $value, $langtag, $audit_type); // $tword
                                                                            // );
    $this->view->outlines = implode("\n", $tout);
    $this->_helper->layout->setLayout('mainview');
  }

  public function mainAction()
  {
    /*
     * This is the first main page presented to a user - we may have to adjust
     * for other users
     */
    $this->dialog_name = 'audit/main';
    $format = 'Y-m-d H:i:s';
    $audit = new Application_Model_DbTable_Audit();
    $id = (int) $this->session->user['id'];
    $langtag = $this->session->lang;
    $rows = $audit->getIncompleteAudits($id);
    $auditlines = $this->makeAuditLines($rows);
    $this->makeDialog(null, $auditlines);
  }

  public function createAction()
  {
    /*
     * either show open audits OR show select audit type + lab
     */
    $this->dialog_name = 'audit/create';
    $this->log->logit("{$this->dialog_name}");
    $audit = new Application_Model_DbTable_Audit();
    $adata = new Application_Model_DbTable_AuditData();
    $ao = new Application_Model_DbTable_AuditOwner();
    $lab = new Application_Model_DbTable_Lab();
    $tmpl = new Application_Model_DbTable_Template();
    $tmplr = new Application_Model_DbTable_TemplateRows();

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
      if ($this->data['audit_type'] == '-')
      {
        $this->session->flash = 'Choose a "Type of Audit" and continue';
        $this->_redirector->gotoUrl('audit/create');
      }

      $trow = $tmpl->getByTag($this->data['audit_type']);
      // get page_id for 'labhead'
      $varname = 'labhead';
      $page_id = $tmplr->findPageId($trow['id'], $varname);
      $this->log->logit("PAGE_ID: {$page_id}");
      $now = new DateTime();
      $nowiso = $now->format($this->ISOdtformat);
      // this will become the audit row
      $arow = array(

          'template_id'=> $trow['id'],
          'created_at'=> $nowiso,
          'updated_at'=> $nowiso,
          'updated_by'=> $this->userid,
          'audit_type'=> $this->data['audit_type'],
          'lab_id'=> $this->labid,
          'status'=> 'INCOMPLETE'
      );
      $labrow = $lab->get($this->labid);
      $this->log->logit("LABROW: " . print_r($labrow, true));
      // insert audit row
      $newauditid = $audit->insertData($arow);
      $aorow = array(

          'audit_id'=> $newauditid,
          'owner'=> $this->userid
      );
      $ao->insertData($aorow);
      // insert lab data into audit data
      $ad = array(

          'labhead'=> 1,
          'slmta_cohortid'=> null
      );
      // 'ad' data is used to trigger copying lab data into audit_data
      $adata->handleLabData($ad, $newauditid, $page_id, $labrow);
      $varname = 'slmta_cohortid';
      $page_id = $tmplr->findPageId($trow['id'], $varname);
      $this->log->logit(
                        "page_id + SLMTA: $page_id} " .
                             print_r($ad, true) .
                             ' ' .
                             print_r($labrow, true));
      $adata->handleSLMTAData($ad, $newauditid, $page_id, $labrow);
      $url = "/audit/edit/";
      $this->session->flash = "New {$this->data['audit_type']} audit #{$newauditid} created";
      $arow = $audit->getAudit($newauditid);
      $this->session->audit = $arow;
      $this->_redirector->gotoUrl($url);
    }
  }

  public function searchAction()
  {
    $this->dialog_name = 'audit/search';
    $aud = new Application_Model_DbTable_Audit();
    if (! $this->getRequest()->isPost())
    {
      $this->makeDialog();
    }
    else
    {
      if ($this->collectData())
        return;
      $this->log->logit('DATA: ' . print_r($this->data, true));

      $prefix = 'cb_';
      $audit_type = $this->data['audit_type'];
      $this->collectExtraData($prefix);
      if (count($this->extra) > 0)
      {
        $list = array();
        foreach($this->extra as $n => $v)
        {
          $list[] = (int) substr($n, 3);
        }

        $out = new Processing();
        $msg = $out->process($list, $name);
        $this->session->flash = 'Excel sheet done.';
        $this->_redirector->gotoUrl($this->mainpage);
      }
      $arows = $aud->selectAudits($this->data);
      $auditlines = $this->makeAuditLines($arows, array(

          'cb'=> false
      ));
      $this->makeDialog($this->data, $auditlines);
    }
  }

  public function runreportsAction()
  {
    $this->dialog_name = 'audit/runreports';
    $aud = new Application_Model_DbTable_Audit();
    if (! $this->getRequest()->isPost())
    {
      $this->makeDialog();
    }
    else
    {
      $processor = new Checklist_Modules_Processor();
      if ($this->collectData())
        return;

      if ($this->data['todo'] == '-' || $this->data['todo'] == '')
      {
        $this->session->flash = "Select a Report Type and continue";
        $this->makeDialog($this->data);
        return;
      }
      if ($this->data['audit_type'] == '-' || $this->data['audit_type'] == '')
      {
        $this->session->flash = "Select an Audit Type and continue";
        $this->makeDialog($this->data);
        return;
      }
      $prefix = 'cb_';
      $this->collectExtraData($prefix);
      $name = $this->data['todo'];

      $this->collectExtraData($prefix);
      $this->log->logit('OutExtraData: ' . print_r($this->extra, true));
      if (count($this->extra) > 0)
      {
        $list = array();
        foreach($this->extra as $n => $v)
        {
          $list[] = (int) substr($n, 3);
        }

        // clean up old files
        $path = dirname(__DIR__) . '/../public/tmp/';
        $this->log->logit("PATH: {$path}");
        $secs = 3600;
        $processor->rmOldFiles($path, $secs);

        $this->log->logit("LN: {$name} " . print_r($list, true));
        $rc = $processor->process($this, $list, $name);
        if (! $rc)
        {
          $this->_helper->layout->disableLayout();
          $this->_helper->viewRenderer->setNoRender(true);
          return;
        }
        else
        {
          //
          $this->view->outlines = '';
          $this->view->showlines = '';
          $this->_helper->layout->setLayout('overall');
          return;
        }
      }
      // nothing selected so paint the data lines
      $arows = $aud->selectAudits($this->data);

      $auditlines = $this->makeAuditLines($arows, array(

          'cb'=> true
      ));
      $this->makeDialog($this->data, $auditlines);
    }
  }

  public function chooseAction()
  {
    $this->dialog_name = 'user/addowner';
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int) $pinfo[3];
    $audit = new Application_Model_DbTable_Audit();
    $lab = new Application_Model_DbTable_Lab();
    $auditrow = $audit->getAudit($id);
    $this->session->flash = "Selected audit #{$auditrow['audit_id']}";
    $this->session->audit = $auditrow;
    $labrow = $lab->get($auditrow['lab_id']);
    $this->session->lab = $labrow;
    $this->_redirector->gotoUrl($this->mainpage);
  }

  public function exportxlsAction()
  {
    $this->dialog_name = 'audit/select';
    if (! $this->getRequest()->isPost())
    {
      $this->makeDialog();
    }
    else
    {
      $prefix = 'cb_';
      $lprefix = strlen($prefix);
      if ($this->collectData())
        return;
      $this->collectextraData($prefix);
      $alist = array();
      foreach($this->extra as $n => $v)
      {
        $alist[] = (int) substr($n, $lprefix);
      }
      exit();
    }
  }

  public function exportdataAction()
  {
    // Exports an audit to dataexport file (.edx)
    $export = new Checklist_Modules_Export();
    $this->dialog_name = 'audit/exportdata';
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = $this->audit['audit_id'];
    if (! $this->getRequest()->isPost())
    {
      // export includes a row of lab, audit and matching auditdata
      $out = $export->exportData($id);
      $outl = strlen($out['data']);
      // The data is ready
      // The proposed name is: <lab_num>_<audit_type>_<audit_date>.edx
      $fname = $out['name'];
      // Send the file
      // call the action helper to send the file to the browser
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);

      $this->getResponse()->setHeader('Content-type', 'application/plain'); // octet-stream');
      $this->getResponse()->setHeader('Content-Disposition',
                                      'attachment; filename="' . $fname . '"');
      $this->getResponse()->setBody($out['data']);
    }
    else
    {
      $this->collectData();
    }
  }

  public function importAction()
  {
    // Imports a dataexport file
    $this->dialog_name = 'audit/import';
    $path = dirname(__DIR__) . '/tmp/';
    $adapter = new Zend_File_Transfer_Adapter_Http();
    $adapter->setDestination($path);
    $toimport = new Application_Model_DbTable_ToImport();
    if (! $this->getRequest()->isPost())
    {
      if ($toimport->getByOwner($this->userid))
      {
        $this->_redirector->gotoUrl('audit/fileparse');
      }
      $this->makeDialog();
    }
    else
    {
      if (! $adapter->receive())
      {
        $messages = $adapter->getMessages();
        $this->session->flash = 'File not loaded - No file selected';
        $this->_redirector->gotoUrl('audit/import');
      }
      $files = $adapter->getFileInfo();
      $uploadedfile = $files['uploadedfile'];

      $data = array();
      $data['owner_id'] = $this->userid;
      $data['path'] = $uploadedfile['tmp_name'];
      if (strlen($data['path']) < 10)
      {
        $this->session->flash = 'File not loaded - Retry';
        $this->_redirector->gotoUrl('audit/import');
      }
      $id = $toimport->insertData($data);
      $this->_redirector->gotoUrl('audit/fileparse');
    }
  }

  public function fileparseAction()
  {
    // audit import file has been seen - now process it.
    $this->dialog_name = 'audit/fileparse';
    if (! $this->getRequest()->isPost())
    {
      // read the imported file and extract the
      // lab info and audit into
      $toimport = new Application_Model_DbTable_ToImport();
      $thisfile = $toimport->getByOwner($this->userid);
      $sdata = file_get_contents($thisfile['path']);
      $data = unserialize($sdata);
      $this->lab = $data['lab'];
      if ($this->labname === '')
      {
        $this->labname = 'No Lab Chosen - Select a Lab to use Choice #2';
      }
      $this->audit = $data['audit'];
      $tmpl = new Application_Model_DbTable_Template();
      $tid = $this->audit['template_id'];
      $this->tmpl_row = $tmpl->get($tid);
      $this->makeDialog();
    }
  }

  public function importallAction()
  {
    // import the entire export file as is
    // comes here from a link click
    $this->dialog_name = "audit/importall";
    $audit = new Application_Model_DbTable_Audit();
    $lab = new Application_Model_DbTable_Lab();
    $audit_data = new Application_Model_DbTable_AuditData();
    $toimport = new Application_Model_DbTable_ToImport();
    $auditowner = new Application_Model_DbTable_AuditOwner();

    /**
     * 1.
     * get the file
     * 2. unserialize
     * 3. Insert lab into system - track the labid
     * 4. Change lab data in audit row, insert audit row
     * 5. Insert into audit_owner
     * 6. Update audit_id in audt_data(s) and insert all.
     */
    $thisfile = $toimport->getByOwner($this->userid);
    $sdata = file_get_contents($thisfile['path']);
    $data = unserialize($sdata);
    $labinfo = $data['lab'];
    $labnum = $labinfo['labnum'];
    $auditinfo = $data['audit'];
    $auditdatarows = $data['audit_data'];
    $haslab = $lab->getLabByLabnum($labnum);
    if (! $haslab)
    {
      // the labnum is not in the system
      // so install this lab data
      unset($labinfo['id']); // remove the id
      $labid = $lab->insertData($labinfo);
      $auditinfo['lab_id'] = $labid;
    }
    else
    {
      // update audit info with the lab id (from this system)
      // replace the lab info with that from the selected lab
      $auditinfo['lab_id'] = $haslab['id'];
    }
    unset($auditinfo['id']); // remove the id

    if ($auditinfo['status'] == 'FINALIZED')
    {
      $auditinfo['status'] = 'COMPLETED';
    }
    $auditid = $audit->insertData($auditinfo);
    $this->log->logit("Imported audit is given #{$auditid}");
    // insert into audit_owner
    $ao = array(

        'audit_id'=> $auditid,
        'owner'=> $this->userid
    );
    $aoid = $auditowner->insertData($ao);

    // insert the audit data rows
    $audit_data->insertAs($auditdatarows, $auditid);
    // update the lab data with that from existing lab
    if ($haslab)
    {
      $defarray = array(

          'labhead'=> 'place holder'
      );
      $audit_data->handleLabData($defarray, $auditid, '-', $haslab);
    }
    // delete the physical file
    unlink($thisfile['path']);

    // delete entry from toimport
    $toimport->delete($thifile['id']);
    $this->session->flash = 'Import complete';
    $this->_redirector->gotoUrl($this->mainpage);
  }

  public function import2labAction()
  {
    // import the export file into current lab
    // ignore the lab info with the export
    // comes here from a link click
    $this->dialog_name = "audit/import2lab";
    if (! $this->lab)
    { // no lab selected
      $this->session->flash = 'No lab selected, either select a lab or choose "Import Lab and Data" and retry';
      $this->_redirector->gotoUrl($this->mainpage);
    }
    $this->log->logit('In audit/import2lab');
    $audit = new Application_Model_DbTable_Audit();
    $lab = new Application_Model_DbTable_Lab();
    $audit_data = new Application_Model_DbTable_AuditData();
    $toimport = new Application_Model_DbTable_ToImport();
    /**
     * 1.
     * get the file
     * 2. unserialize
     * 3. Get the lab id
     * 4. Change lab data in audit row, and into audit_data rows, insert audit
     * row
     * 5. Insert into audit_owner
     * 6. Update audit_id in audt_data(s) and insert all.
     */

    $thisfile = $toimport->getByOwner($this->userid);
    $sdata = file_get_contents($thisfile['path']);
    $data = unserialize($sdata);
    $auditdatarows = $data['audit_data'];
    // we do not need the lab data as we will use the current lab

    // insert audit row
    $auditinfo = $data['audit'];
    unset($auditinfo['id']);
    $auditinfo['lab_id'] = $this->labid; // the current lab
    /**
     *  If an sudit status is FINALIZED
     *    downgrade it to COMPLETED
     */
    if ($auditinfo['status'] == 'FINALIZED')
    {
      $auditinfo['status'] = 'COMPLETED';
    }
    $auditid = $audit->insertData($auditinfo);

    // insert into audit_owner
    $auditowner = new Application_Model_DbTable_AuditOwner();
    $ao = array(

        'audit_id'=> $auditid,
        'owner'=> $this->userid
    );
    $aoid = $auditowner->insertData($ao);
    // insert the audit data rows
    $audit_data->insertAs($auditdatarows, $auditid);
    // change the original audit data to that of the current lab
    $labrow = $lab->get($this->labid);
    $defarray = array(

        'labhead'=> 'place holder'
    );
    $audit_data->handleLabData($defarray, $auditid, '-', $labrow);
    // delete the physical file
    unlink($thisfile['path']);

    $toimport->delete($thifile['id']); // delete entry from toimport
    $this->session->flash = 'Import complete';
    $this->_redirector->gotoUrl($this->mainpage);
  }

  public function cancelimportAction()
  {
    // cancel current import and reset import engine
    /*
     * 1. Remove the file from the path in toimport for this user as owner_id 2.
     * Delete the row from toimport for this user as owner_id
     */
    $toimport = new Application_Model_DbTable_ToImport();
    $thefile = $toimport->getByOwner($this->userid);
    $filepath = $thefile['path'];
    unlink($filepath);
    $toimport->delete($thefile['id']);
    $this->session->flash = 'Import has been reset';
    $this->_redirector->gotoUrl($this->mainpage);
  }

  public function deleteAction()
  {
    // delete the audit in question
    $audit = new Application_Model_DbTable_Audit();
    $auditdata = new Application_Model_DbTable_AuditData();
    $audit_id = $this->audit['audit_id'];
    $auditdata->deleteAuditRows($audit_id);
    $audit->deleteAudit($audit_id);
    $this->session->flash = "Audit with Id #{$audit_id}: delete successfully";
    $this->_redirector->gotoUrl($this->mainpage);
  }

  public function completeAction()
  {
    // mark audit complete
    if (! isset($this->session->audit))
    {
      $this->session->flash = 'First select and audit then retry';
      $this->_redirector->gotoUrl($this->mainpage);
    }
    $audit = new Application_Model_DbTable_Audit();
    $ao = new Application_Model_DbTable_AuditOwner();
    $newstatus = "COMPLETE";
    $users = array(

        'ADMIN',
        'USER',
        'APPROVER'
    );
    if (in_array($this->usertype, $users))
    {
      // if type is SLIPTA - check for incomplete
      $audit_id = $this->audit['audit_id'];
      // BAT and TB can be incomplete and it is OK
      if ($this->audit['status'] != 'INCOMPLETE' && $ao->isOwned($audit_id, $this->userid))
      {
        $this->session->flash = "Audit id #{$audit_id} status is not INCOMPLETE";
        $this->_redirector->gotoUrl($this->mainpage);
        return;
      }
      if ($this->audit['tag'] == 'SLIPTA')
      {
        // icmap contains the incomplete elements for use in completing
        $icmap = $this->iscomplete($audit_id);
        if ($icmap)
        {
          // is not complete so show incomplete map
          $this->view->outlines .= $icmap;
          $this->_helper->layout->setLayout('overall');
        }
        else
        {
          // It is complete - move status to complete
          $audit->moveStatus($audit_id, $newstatus);
          $this->session->flash = "Audit id #{$audit_id} has been changed to {$newstatus}";
          // clear selected audit
          $this->session->audit = null;
          $this->_redirector->gotoUrl($this->poststatchange);
        }
      }
      else
      {
        // not SLIPTA
        $this->log->logit('not slipta');
        $audit->moveStatus($audit_id, $newstatus);
        $this->session->flash = "Audit id #{$audit_id} has been changed to {$newstatus}";
        $this->session->audit = null;
        $this->_redirector->gotoUrl($this->poststatchange);
      }
    }
    else
    {
      $this->session->flash = 'Invalid action';
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function incompleteAction()
  {
    // mark audit complete
    $audit = new Application_Model_DbTable_Audit();
    $ao = new Application_Model_DbTable_AuditOwner();
    $newstatus = "INCOMPLETE";
    $users = array(

        'ADMIN',
        'USER',
        'APPROVER'
    );
    if (in_array($this->usertype, $users))
    {
      // if type is SLIPTA - check for incomplete
      $audit_id = $this->audit['audit_id'];
      // BAT and TB can be incomplete and it is OK
      if ($this->audit['status'] != 'COMPLETE' && $ao->isOwned($audit_id, $this->userid))
      {
        $this->session->flash = "Audit id #{$audit_id} status is not COMPLETE";
        $this->_redirector->gotoUrl($this->poststatchange);
      }
      $audit->moveStatus($audit_id, $newstatus);
      $this->session->flash = "Audit id #{$audit_id} has been changed to {$newstatus}";
      $this->session->audit = null;
      $this->_redirector->gotoUrl($this->poststatchange);
    }
    else
    {
      $this->session->flash = 'Invalid action';
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function finalizeAction()
  {
    // finalize the complete audit
    $audit = new Application_Model_DbTable_Audit();
    $newstatus = "FINALIZED";
    $users = array(

        'APPROVER'
    );
    if (in_array($this->usertype, $users))
    {
      // if type is SLIPTA - check for incomplete
      $audit_id = $this->audit['audit_id'];
      // BAT and TB can be incomplete and it is OK
      if ($this->audit['status'] != 'COMPLETE')
      {
        $this->session->flash = "Audit id #{$audit_id} status is not COMPLETE";
        $this->session->audit = null;
        $this->_redirector->gotoUrl($this->poststatchange);
      }
      $audit->moveStatus($audit_id, $newstatus);
      $this->session->flash = "Audit id #{$audit_id} has been changed to {$newstatus}";
      $this->session->audit = null;
      $this->_redirector->gotoUrl($this->poststatchange);
    }
    else
    {
      $this->session->flash = 'Invalid action';
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function rejectAction()
  {
    // reject the complete audit
    $audit = new Application_Model_DbTable_Audit();
    $newstatus = "REJECTED";
    $users = array(

        'APPROVER'
    );
    if (in_array($this->usertype, $users))
    {
      // if type is SLIPTA - check for incomplete
      $audit_id = $this->audit['audit_id'];
      // BAT and TB can be incomplete and it is OK
      if ($this->audit['status'] != 'COMPLETE')
      {
        $this->session->flash = "Audit id #{$audit_id} status is not COMPLETE";
        $this->session->audit = null;
        $this->_redirector->gotoUrl($this->poststatchange);
      }
      $audit->moveStatus($audit_id, $newstatus);
      $this->session->flash = "Audit id #{$audit_id} has been changed to {$newstatus}";
      $this->session->audit = null;
      $this->_redirector->gotoUrl($this->poststatchange);
    }
    else
    {
      $this->session->flash = 'Invalid action';
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function showownersAction()
  {
    // show the owners of the selected audit
    $ao = new Application_Model_DbTable_AuditOwner();
    if ($this->session->audit)
    {
      $audit_id = $this->session->audit['audit_id'];
      $urows = $ao->getOwnersByAuditId($audit_id);
      $showlines = array();
      $showlines[] = "<div style=\"margin-left:200px;\"><h3>Names of owners of (selected) audit id #{$audit_id}<br />";
      $showlines[] = '<table">';
      foreach($urows as $u)
      {
        $showlines[] = "<span style=\"color:black;font-weight:normal;\">{$u['name']}</span><br />";
      }
      $showlines[] = '</table></div>';
    }
    $this->view->showlines = implode("\n", $showlines);
    $this->_helper->layout->setLayout('overall');
  }

}
