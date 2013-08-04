<?php
require_once 'modules/Checklist/htmlhelp.php';
require_once 'modules/Checklist/logger.php';
require_once '../application/controllers/Action.php';

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
      $labh = new Application_Model_DbTable_Lab();
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
  
  public function findOldAction() {
    logit ( "in Find" );
    $lab = new Application_Model_DbTable_Lab ();
    $urldata = $this->getRequest ()->getParams ();
    $name = get_arrval ( $urldata, 'term', '' );
    if ($name == '') {
      throw new Exception ( 'Bad name in Lab search' );
    }
    $out = $lab->getLabByPartialName ( $name );
    logit ( "Lab Match: {$out}" );
    echo $out;
    exit ();
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