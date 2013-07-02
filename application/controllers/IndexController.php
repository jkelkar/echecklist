<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
      $albums = new Application_Model_DbTable_Albums();
      $sql = "order by artist name";
      // $this->view->albums = $albums->fetchAll();
      $this->view->albums = $albums->getAlbums();
    }

    public function addAction()
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
    }

    public function editAction()
    {
      $form = new Application_Form_Album();
      $form->submit->setLabel('Edit');
      $this->view->form = $form;

      if ($this->getRequest()->isPost()) {
	$formData = $this->getRequest()->getPost();
	if ($form->isValid($formData)) {
	  $id = (int)$form->getValue('id');
	  $artist = $form->getValue('artist');
	  $title = $form->getValue('title');
	  $albums = new Application_Model_DbTable_Albums();
	  $albums->updateAlbum($id, $artist, $title);
	  
	  $this->_helper->redirector('index');
	}
      } else {
	$id = $this->getParam('id', 0);
	if ($id > 0) {
	  $albums = new Application_Model_DbTable_Albums();
	  $form->populate($albums->getAlbum($id));
	}
      }
    }

    public function deleteAction()
    {
      if ($this->getRequest()->isPost()) {
	$del = $this->getRequest()->getPost('del');
	if ($del == 'Yes') {
	  $id = $this->getRequest()->getPost('id');
	  $albums = new Application_Model_DbTable_Albums();
	  $albums->deleteAlbum($id);
	}
	$this->_helper->redirector('index');
      } else {
	$id = $this->getparam('id', 0);
	$albums = new Application_Model_DbTable_Albums();
	$this->view->album = $albums->getAlbum($id);
      }
    }

    /**
     * This shows the rows in excel format and makes it available for download
     */
    public function inexcelAction() 
    {
      echo 'Starting the excel conversion' ;
      require_once 'modules/AlbumsExcel.php';

      $albums = new Application_Model_DbTable_Albums();
      $sql = "order by artist name";
      // $this->view->albums = $albums->fetchAll();                                                     
      $rows = $albums->getAlbums();

      doit($rows);
    }
}







