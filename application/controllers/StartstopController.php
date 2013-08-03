<?php
require_once 'modules/Checklist/htmlhelp.php';
require_once 'modules/Checklist/logger.php';
require_once '../application/controllers/Action.php';

class StartstopController extends Application_Controller_Action
// Zend_Controller_Action
{

  public function init()
  {
    /* Initialize action controller here */
    /**
     * initialize parent here
     **/
    parent::init();
  }

  
  public function loginAction()
  {
    if ($this->getRequest()->isPost()) {
      $formData = $this->getRequest();
      //if ($form->isValid($formData)) {
      $username = $formData->getPost('username', '');
      $password = $formData->getPost('password', '');
      $user = new Application_Model_DbTable_User();
      $row = $user->getUserByUsername($username);
      // $u = array();
      //$eNamespace = parent::getHandle();
      logit("eChecklist {$eNamespace->userct}");
      $eNamespace->user = array();
      foreach($row as $a => $b) {
        if ($a != 'password') { // FIXME - goto BCRYPT
          $eNamespace->user[$a] = $b;
          logit("Added {$a} => {$b}");
        }
      }
      //$echecklistNamespace->user = $u;
      /* $this->_helper->redirector('index'); */
    } else {

      $_fields = array('username', 'password', 'submit');
      $flist = array('_fields' => $_fields,
                     'username' =>
                     array('type'=>'string',
                           'length' => 32,
                           'label' => 'Userid:'),
                     'password' =>
                     array('type'=>'password',
                           'length' => 32,
                           'label' => 'Password:'),
                     'submit' =>
                     array('type' => 'submit',
                           'value' => 'Login')
                     );
      // logit("flist: {$flist}");
      $outlines = dumpForm($flist);
      $this->view->formtext = $outlines;
      $this->view->title = 'Login';
      $this->_helper->layout->setLayout('overall');
    }
    //}
  }
  
  public function logoutAction() {
    /* logout and clear the user entry from the session */
    unset($eNamespace->user);
  }
  
  /**public function addAction()
  {
    $form = new Application_Form_Album();
    $form->submit->setLabel('Add');
    $this->view->form = $form;

    if ($this->getRequest()->isPost()) {
      $formData = $this->getRequest()->getPost();
      if ($form->isValid($formData)) {
        $artist = $form->getValue('artist');
        $title = $form->getValue('title');
        $albums = new Application_Model_DbTable_Albums();
        $albums->addAlbum($artist, $title);

        $this->_helper->redirector('index');
      } else {
        $form->populate($formData);
      }
      }
      }**/


}
