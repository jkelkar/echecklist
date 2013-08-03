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
    //$sql = "order by artist name";
    // $this->view->albums = $albums->fetchAll();
    $this->view->albums = $albums->getAlbums();
    $this->_helper->layout->setLayout('mainpage');
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
    $albums = new Application_Model_DbTable_Albums();
    $form->submit->setLabel('Edit');
    $this->view->form = $form;

    if ($this->getRequest()->isPost()) {
      $formData = $this->getRequest()->getPost();
      if ($form->isValid($formData)) {
        $id = (int)$form->getValue('id');
        $artist = $form->getValue('artist');
        $title = $form->getValue('title');
        //$albums = new Application_Model_DbTable_Albums();
        $albums->updateAlbum($id, $artist, $title);
	  
        $this->_helper->redirector('index');
      }
    } else {
      $id = $this->getParam('id', 0);
      if ($id > 0) {
        // $albums = new Application_Model_DbTable_Albums();
        $form->populate($albums->getAlbum($id));
        $this->_helper->layout->setLayout('mainpage');
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
    
  /**
   * This shows an html page converted to PDF using mpdf
   */

  public function inpdfAction()
  {
    echo 'Starting PDF conversion';
    require_once 'modules/mpdf56/examples/example01_basic.php';
      
      
  }
    
  public function renderPhpToString() //$file, $vars=null)
  {
    /*if (is_array($vars) && !empty($vars)) {
      extract($vars);
      }*/
    ob_start();
    //include $file;
    $code = '$albums = new Application_Model_DbTable_Albums();' .
      '$sql = "order by artist name";' .
      /* $this->view->albums = $albums->fetchAll();*/
      '$this->view->albums = $albums->getAlbums();' ;
    return ob_get_clean();
  }

  public function renderZendToString()
  {
    $data = $this->render('index/index', 'index', true);
    return $data;
  }
  
  public function inpdf2Action()
  {
    echo 'Create HTML & then convert it to PDF!';
    /* $data = $this->renderPhpToString(); */
    $albums = new Application_Model_DbTable_Albums();
    $sql = "order by artist name";
    // $this->view->albums = $albums->fetchAll();
    $this->view->albums = $albums->getAlbums();
    $html = $this->view->render('index/index.phtml');
    // echo 'Data: ' . $html;
    require_once 'modules/mpdf56/examples/testmpdf.php';
    
    html2pdf($html);
    /**
     * $data = $this->renderZendToString();
     * echo "Rendered: " . $data ."\n";
     */
  }
  
  public function html2pdfdomAction() {
    require_once("modules/dompdf/dompdf_config.inc.php");
    /*$html = ob_get_contents();
    ob_end_flush();
    */
    //$html = $this->render('index/index', 'index', true);
    $albums = new Application_Model_DbTable_Albums();
    $sql = "order by artist name";
    // $this->view->albums = $albums->fetchAll();
    $this->view->albums = $albums->getAlbums();
    $html = $this->view->render('index/index.phtml');
    logit("HTML: {$html}");
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->render();
    $dompdf->stream("Time Table.pdf");
    
  }
}
