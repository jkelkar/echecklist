<?php
require_once 'modules/Checklist/htmlhelp.php';
require_once 'modules/Checklist/logger.php';
require_once '../application/controllers/Action.php';

class ExportController extends Application_Controller_Action
{

  public function init()
  {
    /* Initialize action controller here */
    /**
     * initialize parent here
     **/
    parent::init();
  }

  
  public function dataexportAction()
  {
    if (!$this->getRequest()->isPost()) {
      // $formData = $this->getRequest();
      //if ($form->isValid($formData)) {
      //$username = $formData->getPost('username', '');
      
      $urldata = $this->getRequest ()->getParams ();
      $audit_id = get_arrval ( $urldata, 'audit_id', '' );
      //$audit_id = $formData->getPost('audit_id', '');
      logit("Export data: audit id {$audit_id}");
      $lab = new Application_Model_DbTable_Lab();
      $data = new Application_Model_DbTable_AuditData();
      $audit_rows = $data->getAudit($audit_id);
      $labline = $data->getAuditItem($audit_id, 'lab_id');
      $labid = $labline['int_val'];
      $labrow = $lab->getLab($labid);
      $alldata = array('Lab' => $labrow, 'Audit' => $audit_rows);
      $serdata = serialize($alldata);
      logit("SERDATA:\n {$serdata}");
      echo "{$serdata}";
      exit();
      /*logit("eChecklist {$eNamespace->userct}");
      $eNamespace->user = array();
      foreach($row as $a => $b) {
        if ($a != 'password') { // FIXME - goto BCRYPT
          $eNamespace->user[$a] = $b;
          logit("Added {$a} => {$b}");
        }
      }
      //$echecklistNamespace->user = $u;
       *
       */
      /* $this->_helper->redirector('index'); */
    } else {

      
      
      $this->_helper->layout->setLayout('overall');
    }
    //}
  }
  
  public function findAction() {
    logit('Find: outer');
    if ($this->getRequest()->isPost()) {
      logit('Find: In post');
      $formData = $this->getRequest ();
      // if ($form->isValid($formData)) {
      $labname = $formData->getPost ( 'labname', '' );
      $country = $formData->getPost ( 'country', '' );
      $stdate = $formData->getPost ( 'stdate', '' );
      $enddate = $formData->getPost ( 'enddate', '' );
      $audittype = $formData->getPost ( 'audittype', '' );
      logit("labname: {$username}");
      logit("country: {$country}");
      logit("stdate: {$stdate}");
      logit("enddate: {$enddate}");
      logit("audittype: {$audittype}");
  
      $data = array(
          'labname' => $labneme,
          'country' => $country,
          'stdate' => strtotime($stdate) ,
          'enddate' => strtotime($enddate),
          'audittype' => $audittype);
      $labh = new Application_Model_DbTable_Lab();
      $labs = $labh($data, 0, 20);
  
    } else {
      logit('Find: In display');
      $this->_helper->layout->setLayout ( '13' );
      $labh = new Application_Model_DbTable_Lab();
      $rows = $labh->getLabs(0, 20);
      $this->view->baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
      $this->view->rows = $rows;
    }
  }

}
