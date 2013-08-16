<?php
require_once 'modules/Checklist/fillout.php';
require_once 'modules/Checklist/logger.php';
require_once '../application/controllers/ActionController.php';

class AuditController extends Application_Controller_Action {
  public $debug = 0;
  // private $mainpage = '';
  
  public function init() {
    /* Initialize action controller here */
    parent::init();
  }

  public function indexAction() {
  
  }

  public function getButtons($page) {
    /*
     * Given the page to paint, get buttons markup
     */
    $display = ($page['display_only'] == 't') ;
    $buttons = '';
    $thispage = $page['page_num'];
    $this->view->thispage = $thispage;
    $nextpage = $page['next_page_num'];
    if ($display) {
    $buttons = <<<"END"
<div style="width:100%;">
  <input type="hidden" name="nextpage" value="{$nextpage}" />
  <div style="float:right;">
    <input type="submit" value="Next" id="nextbutton" name="sbname">
</div></div>
END;
    } else {
    $buttons = <<<"END"
<div style="width:100%;">
  <input type="hidden" name="nextpage" value="{$nextpage}" />
  <div style="float:right;">
    <input type="submit" value="Cancel" id="cancelbutton" name="sbname">
    <input type="submit" value="Save" id="savebutton" name="sbname">
    <input type="submit" value="Save & Continue" id="continuebutton" name="sbname">
</div></div>
END;
    }
    $buttons .= <<<"END"
<script>
$(function() {
  d.closeAll();
  d.openTo({$thispage}, true);
});
</script>
END;
    $this->view->buttons = $buttons;
  }

  public function editAction() {
    //$template_id = 1; // FIXME
    $lang_default= 'EN';
    $baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
    $mainpage = "/";
    /*
      $mt = microtime(true);
      logit("Start: {$mt}");
    */
    $audit = new Application_Model_DbTable_AuditRows ();
    $data = new Application_Model_DbTable_AuditData ();
    $page = new Application_Model_DbTable_Page ();
    $lang = new Application_Model_DbTable_Language ();
    $lang_word = new Application_Model_DbTable_langword ();
    $vars = $this->_request->getPathInfo();
    //logit("VARS: {$vars}");
    $pinfo = explode("/", $vars);
    //logit('PARTS: '. print_r($pinfo, true));
    $audit_id = (int)  $pinfo[3];
    $template_id = $data->getTemplateId($audit_id);
    $langtag = $this->echecklistNamespace->lang;
    $thispage = (int) $pinfo[4];
    
    if ($thispage == '') {
      $fp = $page->getStartPage($template_id); //1 is the template id
        $thispage = $fp; // this is the first page of slipta template
      } else {
        $thispage = ( int ) $thispage;
      }
    //logit ( 'In slipta beginning' );
    $nav = $page->getNav ( $template_id, $thispage ); // 1 is the template_id
    $page_row = $nav ['row'];
    $display_only =$page_row['display_only'];
    $pageid = $page_row['page_id'];
    $nrows = $nav ['rows'];
    if (! $this->getRequest ()->isPost ()) {
      // write out the page
      //$tword = $lang_word->getWords ( $langtag );
      if ($this->debug) {
        logit ( "Got showpage value: {$thispage}" );
        logit ( "Got language value: {$langtag}" );
      }
      
      $rows = $audit->getrows ( $template_id, $thispage, $langtag ); // 1 is the template_id
      // if this page is display only we load values for page 0
      // page 0 has global data on it
      if ($display_only == 't') {
        $value = $data->getData ( $audit_id, 0 );
      } else {
        $value = $data->getData ( $audit_id, $pageid ); // 1 is the audit_id
      }
      
      if ($this->debug) {
        foreach ( $page_row as $a => $p ) {
          logit ( "Page data: {$a} => {$p}\n" );
        }
      }
      logit ( "Page Tag {$page_row['tag']}\n" );
      // Generate the entries to make a tree - using dtree
      $jsrows = array ();
      $page_url = "{$baseurl}/audit/edit/{$audit_id}"; 
      /*
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        logit("D1: {$mtx}");
        $mt = $mt2;
      */
      foreach ( $nrows as $r ) {
        if ($this->debug) {          
          foreach ( $r as $x => $y ) {
            logit ( "{$x} -- {$y}" );
          }
          logit ( "{$r['parent']} -> {$r['page_num']}" );
        }
        $purl = "{$page_url}/{$r['page_num']}";
        $line = "d.add({$r['page_num']},{$r['parent']}, '{$r['tag']}'";
        if ($r ['leaf'] == 't') { // draw a URL for a leaf node not otherwise
          $line = $line . ", '{$purl}'";
        }
        $line = $line . ");";
        $jsrows [] = $line;
        if ($this->debug) {
          logit ( "Line: {$line}" );
        }
      }
      /*
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        logit("D2: {$mtx}");
        $mt = $mt2;
      */
      if ($this->debug) {
        logit ( 'Dumping J' );
        foreach ( $jsrows as $j ) {
          logit ( "J: {$j}" );
        }
      }
      $tout = calculate_page ( $rows, $value, $langtag); //$tword );
      logit('VALUE: '. print_r($value, true));

      /*
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        logit("D3: {$mtx}");
        $mt = $mt2;
      */
      $next = $thispage + 1;
      $this->view->thispage = $thispage;
      $this->view->treelines = implode ( "\n", $jsrows );
      $olines = implode ( "\n", $tout );
      if ($display_only == 't') {
        //logit("OUT:\n$olines");
        $olines = str_replace('"', '\"', $olines);
        eval("\$olines = \"$olines\"; ");
      }
      $this->view->outlines = $olines;

      $this->getButtons($page_row);
     
      $this->view->hidden = implode ( "\n", array (
          "<input type=\"hidden\" name=\"audit_id\" value=\"{$audit_id}\">"
      ) );
      // logit("HEADER: {$this->view->header}");
      $this->view->flash = $this->echecklistNamespace->flash;
      $this->echecklistNamespace->flash = '';
      $this->_helper->layout->setLayout ('template');
    } else {                                        // Handle the POST request here
      logit ('In post for Audit');
      /*
        $mt = microtime(true);
        logit("P1: {$mt}");
      */
      //$mt = $mt2;
      $formData = $this->getRequest ()->getPost ();
      $dvalue = array ();
      $not_include = array (
          'sbname',
          'audit_id',
          'id'
      );
      //$thispage = 0;
      foreach ( $formData as $a => $b ) {
        //logit ( "FD: {$a} -- {$b}" );
        //f ($a == 'thispage') {$thispage = (int)$b; continue;}
        if (in_array ( $a, $not_include )) {
          continue;
        }
        if ($a == 'nextpage') {
          $nextpage = ( int ) $b;
        }
        $dvalue [$a] = $b;
      }
      $sbname = $formData ['sbname'];
      logit ( "action: {$sbname}" );
      $uri = Zend_Controller_Front::getInstance ()->getRequest ()->getRequestUri ();
      logit ( "URI: {$uri}" );
      $u = preg_split ( "/\//", $uri );
      if ($thi->debug) {
        foreach ( $u as $un ) {
          logit ( "U: {$un}" );
        }
      }
      /*
        $mt2 = microtime(true);
        $mtx = $mt2 - $mt;
        logit("P2: {$mtx}");
        $mt = $mt2;
      */
      $newuri = implode ( '/', array_slice ( $u, 3 ) );
      $pagerow = $page->getPage ( $template_id, $thispage );
      $pageid = $pagerow['page_id'];
      $nextpage = $pagerow['next_page_num'];
      
      $page_url = "/audit/edit/{$audit_id}/{$nextpage}"; 
      logit ( "URINEW: {$newuri}" );
      switch ($sbname) {
      case 'Cancel' :
        logit ( "Sbname: {$sbname} switch" );
        // refresh the page
        $this->redirect ( $newuri );
        break;
      case 'Save' :
        // save the data and goto main page
        // break;
      case 'Save & Continue' :
        // save data and go the next logical page
        // for now just save the data
        /*
          $mt2 = microtime(true);
          $mtx = $mt2 - $mt;
          logit("P3: {$mtx}");
          $mt = $mt2;
        */
        $did = $formData ['audit_id'];
        $data->updateData ( $dvalue, $did, $pageid );
        /*
          $mt2 = microtime(true);
          $mtx = $mt2 - $mt;
          logit("P4: {$mtx}");
          $mt = $mt2;
        */
        if ($sbname == 'Save') {
          $this->redirect ( $newuri );
        } else {
          if ($nextpage == 999) {
            $this->redirect($mainpage);
          }
          $this->redirect ( $page_url );
        }
        break;
      case 'Next':
        /* just go to the next page - nothing to save */
        if ($nextpage == 999) {
          $this->redirect($mainpage);
        }
        $this->redirect( $page_url );
      default :
      }
    }
  
  }

  public function inpdf2Action() {
    // echo 'Create HTML & then convert it to PDF!';
    /* $data = $this->renderPhpToString(); */
      /*$albums = new Application_Model_DbTable_Albums();
      $sql = "order by artist name";
      $this->view->albums = $albums->getAlbums();
      $html = $this->view->render('index/index.phtml');
      */
      
      $html = file_get_contents ( './slipta_1_saved.html' );
    // logit("Data: {strlen($html)}");
    // echo strlen($html);
    // exit();
    
    require_once 'modules/mpdf56/examples/testmpdf.php';
    html2pdf ( $html );
  
  /**
   * $data = $this->renderZendToString();
   * echo "Rendered: " .
   * $data ."\n";
   */
  }

  public function findAction() {
    /*
     * either show open audits OR show select audit type + lab
     */
    $this->dialog_name = 'audit/find';
    logit ("{$this->dialog_name}" );
    $audit = new Application_Model_DbTable_User();
    $vars = $this->_request->getPathInfo();
    $pinfo = explode("/", $vars);
    $id = (int)  $pinfo[3];
    $langtag = $this->echecklistNamespace->lang;
    // $urldata = $this->getRequest()->getParams();
    if (!$this->getRequest()->isPost()) {
      $row = $user->getUser($id);
      // logit('LAB: '. print_r($row, true));
      $this->makeDialog($row);
    } else {
      // display the form here
      $this->collectData();
      // logit('Data: ' . print_r($this->data));
      $user->updateData($data, $id); 
      $this->_redirector->gotoUrl($this->mainpage);
    }
  }

  public function html2pdfdomAction() {
    // get the HTML
    /*
     * ob_start(); include(dirname(__FILE__).'/res/exemple00.php'); $content = ob_get_clean();
     */
    // convert in PDF
    $html = file_get_contents ( './slipta_1_saved.html' );
    logit ( "HTML: {$html}" );
    
    require_once 'modules/html2pdf_v4.03/html2pdf.class.php';
    try {
      $html2pdf = new HTML2PDF ( 'P', 'Letter', 'en' );
      // $html2pdf->setModeDebug();
      $html2pdf->setDefaultFont ( 'Arial' );
      $html2pdf->writeHTML ( $html, isset ( $_GET ['vuehtml'] ) );
      $html2pdf->Output ( 'test_slipta.pdf' );
    } catch ( HTML2PDF_exception $e ) {
      echo $e;
      exit ();
    }
  
  }
}
 
