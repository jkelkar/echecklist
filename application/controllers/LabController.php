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
    $dialog_name = 'lab/create';
    logit ( "{$dialog_name}" );
    $lab = new Application_Model_DbTable_Lab ();
    $urldata = $this->getRequest ()->getParams ();
    if (!$this->getRequest ()->isPost ()) {
      $dialog = new Application_Model_DbTable_DialogRow();
      $allrows = $dialog->getFullDialog($dialog_name);
      $title = $allrows['dialog']['title'];
      $drows = $allrows['dialog_rows'];
      $this->view->outlines = calculate_dialog($drows, array(''=>''), $this->view->langtag);
      //generate_dialog_processing($drows);
      $this->view->title = $title;
      $this->_helper->layout->setLayout('overall');
    } else {
      // display the form here
      // $this->view->langtag = $this->echecklistNamespace->lang;
      $formData = $this->getRequest();
      $data = array();
      $data['labname'] = $formData->getPost('labname','');
      $data['labnum'] = $formData->getPost('labnum','');
      $data['street'] = $formData->getPost('street','');
      $data['street2'] = $formData->getPost('street2','');
      $data['street3'] = $formData->getPost('street3','');
      $data['city'] = $formData->getPost('city','');
      $data['state'] = $formData->getPost('state','');
      $data['country'] = $formData->getPost('country','');
      $data['postcode'] = $formData->getPost('postcode','');
      $data['labtel'] = $formData->getPost('labtel','');
      $data['labfax'] = $formData->getPost('labfax','');
      $data['labemail'] = $formData->getPost('labemail','');
      $data['lablevel'] = $formData->getPost('lablevel','');
      $data['labaffil'] = $formData->getPost('labaffil','');


      logit('DATA: '. print_r($data, true));
      $lab->insertData($data);
      $this->view->title = 'Create Lab';
      $this->_helper->layout->setLayout('overall');
    }
  }

  public function editAction() {
    $dialog_name = 'lab/edit';
    logit ( "{$dialog_name}" );
    $lab = new Application_Model_DbTable_Lab ();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int)  $pinfo[3];
    $langtag = $this->echecklistNamespace->lang;
    $urldata = $this->getRequest ()->getParams ();
    if (!$this->getRequest ()->isPost ()) {
      $dialog = new Application_Model_DbTable_DialogRow();
      $row = $lab->getLab($id);
      
      logit('LAB: '. print_r($row, true));
      $allrows = $dialog->getFullDialog($dialog_name);
      $title = $allrows['dialog']['title'];
      $drows = $allrows['dialog_rows'];
      $this->view->outlines = calculate_dialog($drows, $row, $this->view->langtag);
      //generate_dialog_processing($drows);
      $this->view->title = $title;
      $this->_helper->layout->setLayout('overall');
    } else {
      // display the form here
      $formData = $this->getRequest();
      $data = array();
      $data['labname'] = $formData->getPost('labname','');
      $data['labnum'] = $formData->getPost('labnum','');
      $data['street'] = $formData->getPost('street','');
      $data['street2'] = $formData->getPost('street2','');
      $data['street3'] = $formData->getPost('street3','');
      $data['city'] = $formData->getPost('city','');
      $data['state'] = $formData->getPost('state','');
      $data['country'] = $formData->getPost('country','');
      $data['postcode'] = $formData->getPost('postcode','');
      $data['labtel'] = $formData->getPost('labtel','');
      $data['labfax'] = $formData->getPost('labfax','');
      $data['labemail'] = $formData->getPost('labemail','');
      $data['lablevel'] = $formData->getPost('lablevel','');
      $data['labaffil'] = $formData->getPost('labaffil','');


      logit('DATA: '. print_r($data, true));
      $labnum = $formData->getPost('labnum','');
      $lab->updateData($data, $id); 

      /*
      // Use next line for inserting data.
      $xxxxx->insertData($data);
      // Use next 2 lines - suitably changed -  to update data.
      */
   
  
      $this->view->title = 'Create Lab';
      $this->_helper->layout->setLayout('overall');
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
      /*
       * // $formData = $this->getRequest(); $lab = new Application_Model_DbTable_Lab(); $urldata = $this->getRequest()->getParams(); $labname = get_arrval($urldata, 'labname', ''); if ($labname == '') { throw new Exception('Bad name in Lab search'); } $out = $lab->getLabByPartialName($labname); logit("Lab Match: {$out}"); return $out;
       */
      // if ($form->isValid($formData)) {
    /**
     * $labname = $formData->getPost('username', '');
     * $password = $formData->getPost('password', '');
     * $user = new Application_Model_DbTable_User();
     * $row = $user->getUserByUsername($username);
     * // $u = array();
     * $eNamespace = parent::getHandle();
     * logit("eChecklist {$eNamespace->userct}");
     * foreach($row as $a => $b) {
     * // logit("User: {$a} -- {$b}");
     * if ($a != 'password') {
     * $eNamespace->$a = $b;
     * logit("Added {$a} => {$b}");
     * }
     *
     * }
     */
      // $echecklistNamespace->user = $u;
      /* $this->_helper->redirector('index'); */
    }
  
  }
}