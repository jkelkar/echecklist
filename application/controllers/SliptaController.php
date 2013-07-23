<?php
require_once 'modules/Checklist/fillout.php';
require_once 'modules/Checklist/logger.php';

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
        logit("{$n} ==> {$v}");
      }
      ("\n");
      $lang_default = 'EN';
      $nextpage = get_arrval($urldata, 'showpage', '');
      $langtag = get_arrval($urldata, 'language', $lang_default);
      $tword = $lang_word->get_words($langtag);
      logit("Got showpage value: {$nextpage}");
      logit("Got language value: {$langtag}");
      if ($nextpage == '') {
        $nextpage = 1;
      } else {
        $nextpage = (int)$nextpage;
      }
      // $langtag = $lang->get_tag($language);
      $rows = $slipta->getrows(1, $nextpage, $langtag); // 1 is the tmpl_head_id
      $value = $data->get_data(1); // 1 is the data_head_id
      $nav = $page->getNav(1, $nextpage);
      $page_row = $nav['row'];
      $nrows = $nav['rows'];
      //$page_row = $page->getPage(1, $nextpage); // FIXME
      foreach($page_row as $a => $p) {
        logit("Page data: {$a} => {$p}\n");
      }
      logit("Page Tag {$page_row['tag']}\n");
      //$nav = $page->getNav(1, $nextpage);
      //$value = array('notme' => 'no');
      // Generate the entries to make a tree - using dtree
      $jsrows = array();
      $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
      $page_url = "{$baseurl}/slipta/edit?language={$langtag}";
      foreach ($nrows as $r) {
        //foreach($r as $x => $y) {
        //logit("{$x} -- {$y}");
        //}
        logit("{$r['parent']} -> {$r['page_num']}");
        $line = "d.add({$r['page_num']},{$r['parent']}, '{$r['tag']}'";
        if ($r['leaf'] == 't') {
          $line = $line . ", '{$page_url}&showpage={$r['page_num']}'";
        }
        $line = $line . ");";
        $jsrows[] = $line;
        logit("Line: {$line}");
      }
      //logit('Dumping J');
      //foreach ($jsrows as $j){
      //  logit("J: {$j}");
      //}
      $tout = calculate_page($rows, $value, $tword);
      $next = $nextpage +1;
      //$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
      //  $this->_baseUrl =  $fc->getBaseUrl();
      $tout[] = "<a href=\"{$baseurl}/slipta/edit?showpage={$next}&language={$langtag}\">Next page</a><br />";
      $this ->view->treelines = implode("\n", $jsrows);
      $this->view->outlines = implode("\n", $tout);
      $this->_helper->layout->setLayout('template');
    }
  }

}