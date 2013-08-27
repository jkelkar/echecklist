<?php
require_once 'modules/Checklist/htmlhelp.php';
require_once 'modules/Checklist/logger.php';
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
    $id = (int) $pinfo [3];
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
      $lab->updateData($this->data, $id);
      $this->_redirector->gotoUrl($this->mainpage);
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
      $labs = $labh->getLabs($this->data, 0, 20);
      $this->makeDialog($this->data);
      $this->makeLabLines($labs);
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
    $this->init();
    // $this->redirect($this->mainpage);
    // Show all audits for this
    $audit = new Application_Model_DbTable_Audit();
    $arows = $audit->selectAudits(array (
        'labnum' => $row['labnum']
    ));
    logit('SELAUD: ' . print_r($arows, true));
    $this->makeDialog($this->data);
    $this->makeAuditLines($arows, array (
        'addsel' => false
    ));
  }
}