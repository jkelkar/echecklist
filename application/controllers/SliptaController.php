<?php
require_once 'modules/Checklist/fillout.php';
require_once 'modules/Checklist/logger.php';

class SliptaController extends Zend_Controller_Action {
  private $debug = 0;
  private $mainpage = '';
  public function init() {
    /* Initialize action controller here */
    // $debug = 0;
  }

  public function indexAction() {
  
  }

  public function editAction() {
    $template_id = 1; // FIXME
    $lang_default= 'EN';
    $baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
    $mainpage = "/";
    
    $slipta = new Application_Model_DbTable_Slipta ();
    $data = new Application_Model_DbTable_AuditData ();
    $page = new Application_Model_DbTable_Page ();
    $lang = new Application_Model_DbTable_Language ();
    $lang_word = new Application_Model_DbTable_langword ();
    $urldata = $this->getRequest ()->getParams ();
    $thispage = get_arrval ( $urldata, 'showpage', '' );
    $langtag = get_arrval ( $urldata, 'language', $lang_default );
    logit ( 'In slipta beginning' );
    if (! $this->getRequest ()->isPost ()) {
      // write out the page
      // $urldata = $this->getRequest()->getParams();
      if ($this->debug != 0) {
        foreach ( $urldata as $n => $v ) {
          logit ( "{$n} ==>x {$v}" );
        }
        logit ( "\n" );
      }
      //$lang_default = 'EN';
      // $thispage = get_arrval($urldata, 'showpage', '');
      // $langtag = get_arrval($urldata, 'language', $lang_default);
      
      $tword = $lang_word->get_words ( $langtag );
      if ($this->debug) {
        logit ( "Got showpage value: {$thispage}" );
        logit ( "Got language value: {$langtag}" );
      }
      if ($thispage == '') {
        $thispage = 20; // this is the first page of slipta template
      } else {
        $thispage = ( int ) $thispage;
      }
      $rows = $slipta->getrows ( 1, $thispage, $langtag ); // 1 is the template_id
      $value = $data->get_data ( 1 ); // 1 is the audit_id
      $nav = $page->getNav ( 1, $thispage ); // 1 is the template_id
      $page_row = $nav ['row'];
      $nrows = $nav ['rows'];
      
      if ($this->debug) {
        foreach ( $page_row as $a => $p ) {
          logit ( "Page data: {$a} => {$p}\n" );
        }
      }
      logit ( "Page Tag {$page_row['tag']}\n" );
      // Generate the entries to make a tree - using dtree
      $jsrows = array ();
      $page_url = "{$baseurl}/slipta/edit?language={$langtag}";
      foreach ( $nrows as $r ) {
        if ($this->debug) {
          
          if ($this->debug) {
            foreach ( $r as $x => $y ) {
              logit ( "{$x} -- {$y}" );
            }
            logit ( "{$r['parent']} -> {$r['page_num']}" );
          }
        }
        $line = "d.add({$r['page_num']},{$r['parent']}, '{$r['tag']}'";
        if ($r ['leaf'] == 't') { // draw a URL for a leaf node not otherwise
          $line = $line . ", '{$page_url}&showpage={$r['page_num']}'";
        }
        $line = $line . ");";
        $jsrows [] = $line;
        if ($this->debug) {
          logit ( "Line: {$line}" );
        }
      }
      if ($this->debug) {
        logit ( 'Dumping J' );
        foreach ( $jsrows as $j ) {
          logit ( "J: {$j}" );
        }
      }
      $tout = calculate_page ( $rows, $value, $tword );
      $next = $thispage + 1;
      $this->view->treelines = implode ( "\n", $jsrows );
      $this->view->outlines = implode ( "\n", $tout );
      $this->view->buttons = <<<"END"
<div style="width:100%;">
          <input type="hidden" name="nextpage" value="{$next}" />
  <div style="float:right;">
    <input type="submit" value="Cancel" id="cancelbutton" name="sbname">
    <input type="submit" value="Save" id="savebutton" name="sbname">
    <input type="submit" value="Save & Continue" id="continuebutton" name="sbname">
</div></div>
<script>
$(function() {
  d.closeAll();
  d.openTo({$thispage}, true);
});
</script>
END;
      $this->view->hidden = implode ( "\n", array (
          "<input type=\"hidden\" name=\"audit_id\" value=\"1\">"
      ) );
      $this->_helper->layout->setLayout ( 'template' );
    } else {
      // Handle the POST request here
      logit ( 'In post for slipta' );
      // $nextpage = 0;
      $formData = $this->getRequest ()->getPost ();
      $dvalue = array ();
      $not_include = array (
          'sbname',
          'audit_id',
          'id'
      );
      foreach ( $formData as $a => $b ) {
        logit ( "FD: {$a} -- {$b}" );
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
      foreach ( $u as $un ) {
        logit ( "U: {$un}" );
      }
      $newuri = implode ( '/', array_slice ( $u, 3 ) );
      $pagerow = $page->getPage ( $template_id, $thispage );
      $nextpage = $pagerow['next_page_num'];
      
      $page_url = "/slipta/edit?language={$langtag}&showpage={$nextpage}";
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
           * logit("S&C: {$formData['action']}"); logit("S&C: {$formData['audit_id']}");
           */
          $did = $formData ['audit_id'];
          $data->updateData ( $dvalue, $did );
          if ($sbname == 'Save') {
            $this->redirect ( $newuri );
          } else {
            if ($nextpage == 999) {
              $this->redirect($mainpage);
            }
            $this->redirect ( $page_url );
          }
          break;
        
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
 
