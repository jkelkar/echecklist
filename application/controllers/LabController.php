<?php

class LabController extends Checklist_Controller_Action
{

  public function init() {
    /* Initialize action controller here */
    /**
     * initialize parent here
     */
    parent::init();
  }

  public function mainAction() {
    if (! $this->getRequest()->isPost()) {
      $this->_helper->layout->setLayout('overall');
      $labh = new Application_Model_DbTable_Lab();
      $rows = $labh->getLabs(0, 5);
      $this->view->baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
      $this->view->rows = $rows;
    }
  }

  public function createAction() {
    $this->dialog_name = 'lab/create';
    $this->log->logit("{$this->dialog_name}");
    $lab = new Application_Model_DbTable_Lab();
    $urldata = $this->getRequest()->getParams();
    $langtag = $this->session->lang;
    if (! $this->getRequest()->isPost()) {
      // $this->log->logit('LAB: '. print_r($row, true));
      $this->makeDialog();
    } else {
      if ($this->collectData()) return;
      // $this->log->logit('Data: ' . print_r($this->data, true));
      $this->log->logit('LD: '. print_r($this->data, true));
      unset($this->data['submit_button']);
      $labrow = unserialize(serialize($this->data));
      $this->log->logit('LABROW: '. print_r($labrow, true));
      $newlabid = $lab->insertData($labrow);
      $labrow['id'] = $newlabid;
      $this->log->logit('LABROW: '. print_r($labrow, true));
      $this->session->lab = $labrow;
      $this->init();
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function editAction() {
    $this->dialog_name = 'lab/edit';
    $this->log->logit("{$this->dialog_name}");
    $lab = new Application_Model_DbTable_Lab();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    //$id = (int) $pinfo [3];
    $id = $this->labid;
    $langtag = $this->session->lang;
    $urldata = $this->getRequest()->getParams();

    if (! $this->getRequest()->isPost()) {
      $row = $lab->getLab($id);
      // $this->log->logit('LAB: '. print_r($row, true));
      $this->makeDialog($row);
    } else {
      // display the form here
      if ($this->collectData()) return;
      // $this->log->logit('Data: ' . print_r($this->data, true));
      unset($this->data['save_button']);
      $this->log->logit('LAB edit: '. print_r($this->data, true));
      $lab->updateData($this->data, $id);
      $this->updateDocs($id);
      $this->_redirector->gotoUrl('/lab/select');
    }
  }

  public function selectAction() {
    $this->dialog_name = 'lab/select';
    $this->log->logit('Find: ');
    $labh = new Application_Model_DbTable_Lab();

    if (! $this->getRequest()->isPost()) {
      $this->log->logit('Find: In display');

      $this->makeDialog();
    } else {
      if ($this->collectData()) return;
      $this->log->logit('Find: In post');
      $this->log->logit('tdlab: ' . print_r($this->data, true));
      $labs = $labh->getLabs($this->data); #, 0, 20);
      $lablines = $this->makeLabLines($labs);
      $this->makeDialog($this->data, $lablines);

    }
  }


  public function chooseAction() {
    // choose the lab id provided
    // save it in session, and
    // show matching audits, order by end_date desc
    $this->dialog_name = 'lab/choose';
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int) $pinfo[3];
    $lab = new Application_Model_DbTable_Lab();
    $row = $lab->getLab($id);
    $this->log->logit('SELLAB: ' . print_r($row, true));
    $this->session->lab = $row;
    $this->log->logit('AT: ' . print_r($this->session->lab, true));
    $this->_redirector->gotoUrl($this->mainpage);
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
}