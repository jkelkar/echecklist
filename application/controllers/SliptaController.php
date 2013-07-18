<?php
require_once 'modules/KLogger.php';
require_once 'modules/Checklist/fillout.php';

class SliptaController extends Zend_Controller_Action
{
  public function init()
  {
    /* Initialize action controller here */
  }

  public function indexAction()
  {

  }

  public function editAction()
  {
    $log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
    $slipta = new Application_Model_DbTable_Slipta();
    $data = new Application_Model_DbTable_Data();
    $page = new Application_Model_DbTable_Page();
    $lang = new Application_Model_DbTable_Language();
    $lang_word = new Application_Model_DbTable_langword();

    if ($this->getRequest()->isPost()) {
      $formData =  $this->getRequest()->getPost();
    } else {
      $urldata = $this->getRequest()->getParams();
      
      foreach($urldata as $n => $v) {
        $log->LogInfo("{$n} ==> {$v}");
      }
      $log->LogInfo("\n");
      $lang_default = 'EN';
      $nextpage = get_arrval($urldata, 'showpage', '');
      $langtag = get_arrval($urldata, 'language', $lang_default);
      $tword = $lang_word->get_words($langtag);
      $log->LogInfo("Got showpage value: {$nextpage}");
      $log->LogInfo("Got language value: {$langtag}");
      if ($nextpage == '') {
        $nextpage = 1;
      } else {
        $nextpage = (int)$nextpage;
      }
      // $langtag = $lang->get_tag($language);
      $rows = $slipta->getrows(1, $nextpage, $langtag); // 1 is the tmpl_head_id
      $value = $data->get_data(1); // 1 is the data_head_id
      $page_tag = $page->get_page_tag(1, $nextpage); // FIXME
      //$value = array('notme' => 'no');
      $tout = calculate_page($rows, $value, $tword);
      $next = $nextpage +1;
      $tout[] = "<a href=\"/zftest/public/slipta/edit?showpage={$next}&language={$langtag}\">Next page</a><br />";
      $this->view->outlines = implode("\n", $tout);
      $this->_helper->layout->setLayout('pagewrapper');
    }
  }

}