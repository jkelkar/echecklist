<?php
require_once 'modules/Checklist/fillout.php';
require_once 'modules/Checklist/logger.php';

class SliptaController extends Zend_Controller_Action
{
  private $debug = 0;

  public function init()
  {
    /* Initialize action controller here */
    // $debug = 0;
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

    if (!$this->getRequest()->isPost()) {
      // write out the page
      $urldata = $this->getRequest()->getParams();
      if ($this->debug) {
        foreach($urldata as $n => $v) {
          logit("{$n} ==> {$v}");
        }
        logit("\n");
      }
      $lang_default = 'EN';
      $nextpage = get_arrval($urldata, 'showpage', '');
      $langtag = get_arrval($urldata, 'language', $lang_default);

      $tword = $lang_word->get_words($langtag);
      if ($this->debug) {
        logit("Got showpage value: {$nextpage}");
        logit("Got language value: {$langtag}");
      }
      if ($nextpage == '') {
        $nextpage = 1;
      } else {
        $nextpage = (int)$nextpage;
      }
      $rows = $slipta->getrows(1, $nextpage, $langtag); // 1 is the tmpl_head_id
      $value = $data->get_data(1);                      // 1 is the data_head_id
      $nav = $page->getNav(1, $nextpage);               // 1 is the tmpl_head_id
      $page_row = $nav['row'];
      $nrows = $nav['rows'];

      if ($this->debug) {
        foreach($page_row as $a => $p) {
          logit("Page data: {$a} => {$p}\n");
        }
      }
      logit("Page Tag {$page_row['tag']}\n");
      // Generate the entries to make a tree - using dtree
      $jsrows = array();
      $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
      $page_url = "{$baseurl}/slipta/edit?language={$langtag}";
      foreach ($nrows as $r) {
        if ($this->debug) {
          foreach($r as $x => $y) {
            logit("{$x} -- {$y}");
          }
          logit("{$r['parent']} -> {$r['page_num']}");
        }
        $line = "d.add({$r['page_num']},{$r['parent']}, '{$r['tag']}'";
        if ($r['leaf'] == 't') { // draw a URL for a leaf node not otherwise
          $line = $line . ", '{$page_url}&showpage={$r['page_num']}'";
        }
        $line = $line . ");";
        $jsrows[] = $line;
        if ($this->debug) { logit("Line: {$line}");}
      }
      if ($this->debug) {
        logit('Dumping J');
        foreach ($jsrows as $j){
          logit("J: {$j}");
        }
      }
      $tout = calculate_page($rows, $value, $tword);
      $next = $nextpage +1;
      // $tout[] = "<a href=\"{$baseurl}/slipta/edit?showpage={$next}&language={$langtag}\">Next page</a><br />";
      $this->view->treelines = implode("\n", $jsrows);
      $this->view->outlines = implode("\n", $tout);
      $this->view->hidden = implode("\n", array(
      		"<input type=\"hidden\" name=\"data_head_id\" value=\"1\">"
      ));
      $this->_helper->layout->setLayout('template');
    } else {
      // Handle the POST request here
      $formData =  $this->getRequest()->getPost();
      $dvalue = array();
      $not_include = array('sbname', 'data_head_id', 'id');
      foreach($formData as $a => $b) {
        //logit("FD: {$a} -- {$b}");
        if (in_array($a, $not_include) ) { continue;}
        $dvalue[$a] = $b;
      }
      $sbname = $formData['sbname'];
      logit("Sbname: {$sbname}");
      switch($sbname) {
      	case 'Cancel':
      		logit("Sbname: {$sbname} switch");
      		// refresh the page
      		$uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      		logit("URI: {$uri}");
      		$u = preg_split("/\//", $uri);
      		foreach($u as $un) { logit("U: {$un}"); }
      		$newuri = implode('/', array_slice($u, 3));
      		logit("URINEW: {$newuri}");
      		$this->redirect($newuri);
      		break;
      	case 'Save & Exit':
      		//save the data and goto main page
      		break;
      	case 'Save & Continue':
      		// save data and go the next logical page
      		// for now just save the data
      		$did = $formData['data_head_id'];
      		$data->updateData($dvalue, $did);
      		break;
      	
      	default:
      }
      }
    }
  }
