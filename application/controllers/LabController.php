<?php
require_once 'modules/Checklist/htmlhelp.php';
require_once 'modules/Checklist/logger.php';
require_once '../application/controllers/ActionController.php';

class LabController extends Application_Controller_Action// Zend_Controller_Action
{

  public function init() {
    /* Initialize action controller here */
    /**
     * initialize parent here
     */
    parent::init ();
  
  }

  public function mainAction() {
    if ($this->getRequest ()->isPost ()) {
      /*$formData = $this->getRequest ();
      // if ($form->isValid($formData)) {
      $username = $formData->getPost ( 'username', '' );
      $password = $formData->getPost ( 'password', '' );
      $user = new Application_Model_DbTable_User ();
      $row = $user->getUserByUsername ( $username );
      logit ( "eChecklist {$eNamespace->userct}" );
      $eNamespace->user = array ();
      foreach ( $row as $a => $b ) {
        if ($a != 'password') { // FIXME - goto BCRYPT
          $eNamespace->user [$a] = $b;
          logit ( "Added {$a} => {$b}" );
        }
      }*/
    } else {
      $this->_helper->layout->setLayout ( 'overall' );
      $labh = new Application_Model_DbTable_Lab();
      $rows = $labh->getLabs(0,5);
      $this->view->baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
      $this->view->rows = $rows;
    }
  }

  public function findAction() {
    $this->_helper->layout->setLayout ( '13' );
    
    logit('Find: outer');
    $labh = new Application_Model_DbTable_Lab();

    if ($this->getRequest()->isPost()) {
      logit('Find: In post');
      $formData = $this->getRequest ();
       // if ($form->isValid($formData)) {
      $labname = $formData->getPost ( 'labname', '' );
      $country = $formData->getPost ( 'country', '' );
      logit("labname: {$labname}");
      logit("country: {$country}");
      
      $data = array(
            'labname' => $labname,
            'country' => $country);
      $labs = $labh->getLabs($data, 0, 20);
      $this->view->lrows = $labs;
    } else {
      logit('Find: In display');
            $labh = new Application_Model_DbTable_Lab();
      $rows = $labh->getAllLabs(0, 20);
      $this->view->baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
      $this->view->rows = $rows;
    }
  }
  
  public function createAction() {
    $this->dialog_name = 'lab/create';
    logit ( "{$this->dialog_name}" );
    $lab = new Application_Model_DbTable_Lab();
    $urldata = $this->getRequest()->getParams();
    $langtag = $this->echecklistNamespace->lang;
    if (!$this->getRequest()->isPost()) {
      // logit('LAB: '. print_r($row, true));
      $this->makeDialog();
    } else {
      $this-> collectData();
      //logit('Data: ' . print_r($this->data, true));
      $lab->insertData($this->data); 
    }
  }

  public function editAction() {
    $this->dialog_name = 'lab/edit';
    logit ( "{$this->dialog_name}" );
    $lab = new Application_Model_DbTable_Lab();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int)  $pinfo[3];
    $langtag = $this->echecklistNamespace->lang;
    $urldata = $this->getRequest()->getParams();

    if (!$this->getRequest()->isPost()) {
      $row = $lab->getLab($id);
      //logit('LAB: '. print_r($row, true));
      $this->makeDialog($row);
    } else {
      // display the form here
      $this-> collectData();
      //logit('Data: ' . print_r($this->data, true));
      $lab->updateData($this->data, $id); 
    }
  }

  public function searchAction() {
    logit ( "In LS" );
    if (! $this->getRequest ()->isPost ()) {
      $_fields = array (
          'username',
          'password',
          'submit'
      );
      $baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
      $page_url = "/zftest/public/lab/find";
      $flist = array (
          '_fields' => $_fields,
          'labname' => array (
              'type' => 'string',
              'length' => 32,
              'label' => 'Lab Name',
              'autocomplete' => array (
                  'url' => $page_url,
                  'setvals' => 'setlab'
              )
          )
      );
      // logit("flist: {$flist}");
      $outlines = dumpForm ( $flist );
      $this->view->formtext = $outlines;
      $this->view->title = 'Search';
      $this->_helper->layout->setLayout ( 'overall' );
    } else {
      
    }
  
  }
}