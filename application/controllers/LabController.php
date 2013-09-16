<?php

require_once 'modules/Checklist/logger.php';
require_once 'modules/Checklist/htmlhelp.php';
require_once '../application/controllers/ActionController.php';

class LabController extends Application_Controller_Action // Zend_Controller_Action
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
    logit("{$this->dialog_name}");
    $lab = new Application_Model_DbTable_Lab();
    $urldata = $this->getRequest()->getParams();
    $langtag = $this->echecklistNamespace->lang;
    if (! $this->getRequest()->isPost()) {
      // logit('LAB: '. print_r($row, true));
      $this->makeDialog();
    } else {
      if ($this->collectData()) return;
      // logit('Data: ' . print_r($this->data, true));
      logit('LD: '. print_r($this->data, true));
      unset($this->data['submit_button']);
      $labrow = unserialize(serialize($this->data));
      logit('LABROW: '. print_r($labrow, true));
      $newlabid = $lab->insertData($labrow);
      $labrow['id'] = $newlabid;
      logit('LABROW: '. print_r($labrow, true));
      $this->echecklistNamespace->lab = $labrow;
      $this->init();
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function editAction() {
    $this->dialog_name = 'lab/edit';
    logit("{$this->dialog_name}");
    $lab = new Application_Model_DbTable_Lab();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    //$id = (int) $pinfo [3];
    $id = $this->labid;
    $langtag = $this->echecklistNamespace->lang;
    $urldata = $this->getRequest()->getParams();

    if (! $this->getRequest()->isPost()) {
      $row = $lab->getLab($id);
      // logit('LAB: '. print_r($row, true));
      $this->makeDialog($row);
    } else {
      // display the form here
      if ($this->collectData()) return;
      // logit('Data: ' . print_r($this->data, true));
      unset($this->data['save_button']);
      logit('LAB edit: '. print_r($this->data, true));
      $lab->updateData($this->data, $id);
      $this->updateDocs($id);
      $this->_redirector->gotoUrl('/lab/select');
    }
  }

  public function selectAction() {
    // $this->_helper->layout->setLayout ( '13' );
    $this->dialog_name = 'lab/select';
    logit('Find: ');
    $labh = new Application_Model_DbTable_Lab();

    if (! $this->getRequest()->isPost()) {
      logit('Find: In display');

      $this->makeDialog();
    } else {
      if ($this->collectData()) return;
      logit('Find: In post');
      logit('tdlab: ' . print_r($this->data, true));
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
    logit('SELLAB: ' . print_r($row, true));
    $this->echecklistNamespace->lab = $row;
    logit('AT: ' . print_r($this->echecklistNamespace->lab, true));
    $this->_redirector->gotoUrl($this->mainpage);
    #$labs = $lab->getLabs($this->data, 0, 20);
    #$lablines = $this->makeLabLines($labs);
    #$this->makeDialog($this->data, $lablines);
    #$this->_redirector->gotoUrl('/lab/select');
    #$this->init();
    // $this->redirect($this->mainpage);
    // Show all audits for this
    #$audit = new Application_Model_DbTable_Audit();
    #$arows = $audit->selectAudits(array (
    #    'labnum' => $row['labnum']
    #));
    #logit('SELAUD: ' . print_r($arows, true));

    #$auditlines = $this->makeAuditLines($arows, array (
    #    'addsel' => false
    #))
    #;
    #$this->makeDialog($this->data, $auditlines);
  }

  public function updateDocs($labid) {
    // update all INCOMPLETE docs with this labid and set all the lab details accurately
    // This is necessary because audit_data table stores one value per row
    logit("LABID: {$labid}");
    $lab = new Application_Model_DbTable_Lab();
    $audit = new Application_Model_DbTable_Audit();
    $tr =new Application_Model_DbTable_Template();
    $tmplr = new Application_Model_DbTable_TemplateRows();
    $auditd = new Application_Model_DbTable_AuditData();
    // fetch the labrow
    $labrow = $lab->get($labid);
    logit("LABROW: {$labid} ".print_r($labrow, true));
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
      logit("SMLTA UPdate: {$dummy}-{$aid}-{$page_id}" . print_r($labrow, true));
      $auditd->handleSLMTAData($dummy, $aid, $page_id, $labrow);
      logit("UPdated: ADid {$aid}-T {$tid}-V {$varname}-P {$page_id}". print_r($labrow, true));
    }
  }
}