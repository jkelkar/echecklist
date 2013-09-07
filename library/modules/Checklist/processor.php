<?php
// this file handles all the output related processing
//  whether it is in Excel output or as reports and images
require_once 'modules/Checklist/logger.php';
require_once 'modules/Checklist/processCommon.php';
/** Include PHPExcel */
require_once 'modules/Classes/PHPExcel.php';

class Processing extends Process_Common {

  public function process($list, $name) {
    // this is the driver for processing the request for output generation
    // gets the config data from table report, converts it into a struct
    //    then creates SQL query to extract required data from DB into rows
    // $list - the list of selected audits
    // $name -
    $report = new Application_Model_DbTable_Report();
    $tinfo = $this->collect($name);
    logit("tinfo: ".print_r($tinfo, true));
    $numtabs = count($tinfo) - 3; // -3 to ignore the heading, report_type, and file_type
    logit("Tabs: {$numtabs}");
    //$start = 0;
    $filehandle = null;
    $heading = $tinfo['heading'];
    $report_type = $tinfo['report_type'];
    $file_type = $tinfo['file_type'];
    logit("ft: {$file_type}");
    switch ($file_type) {
      case 'excel' :
        // this is going to be an excel file
        $filehandle = $this->startExcelDoc($heading);
        for($i = 1; $i <= $numtabs; $i ++) {
          logit("Processing tab {$i}");

          $tabinfo = $tinfo[$i];
          $fnames = array();
          $flabels = array();
          foreach($tabinfo as $n => $v) {
            switch ($n) {
              case 'heading' :
                $heading = $v;
                break;
              case 'query' :
                $query = $v;
                break;
              default :
                $flabels[] = $v[0];
                $fnames[] = $v[2];
            }
          }
          // $flabels;
          $names = $this->_mkList($fnames);
          $audits = $this->_mkList($list);
          logit("Heading: {$heading}");
          logit('fnames: ' . print_r($fnames, true) . '  ' . print_r($names, true));
          // logit('flabels: '. print_r($flabels, true).'  '. print_r($labels, true));
          eval("\$sql = \"$query\"; ");
          logit("CALC Q: {$sql}");
          $rows = $report->runQuery($sql);
          logit('ROWS: ' . print_r($rows, true));
          $data = $this->collectRows($rows);
          $this->startWorkSheet($filehandle, $i, $heading, $flabels, $fnames, $data);
        }
        $this->saveFile($filehandle);
        break;
      case 'html' :
        // this is going to be an image file
        // Generate an HTML file and insert in it a graphic image
        $this->createHTMLFile();
        break;
      case 'graphic' :
        break;
      default :
    }
  }
}


