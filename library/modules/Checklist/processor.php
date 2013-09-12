<?php
// this file handles all the output related processing
//  whether it is in Excel output or as reports and images
require_once 'modules/Checklist/logger.php';
require_once 'modules/Checklist/processCommon.php';
/** Include PHPExcel */
require_once 'modules/Classes/PHPExcel.php';
require_once 'modules/ChartDirector/lib/phpchartdir.php';

class Processing extends Process_Common {
  public $base ;

  public function init() {
    // initialize

  }
  public function process($base, $list, $name) {
    // this is the driver for processing the request for output generation
    // gets the config data from table report, converts it into a struct
    //    then creates SQL query to extract required data from DB into rows
    // base is the this pointer from the controller
    // $list - the list of selected audits
    // $name -
    $this->base = $base;
    $audit = new Application_Model_DbTable_Audit();
    $report = new Application_Model_DbTable_Report();
    $check = new Application_Model_DbTable_Checklist();
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
    $fname = '';
    logit("LIST: " . print_r($list, true));
    $audits = $audit->_mkList($list);
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
          $names = $audit->_mkList($fnames);
          logit("Heading: {$heading}");
          logit('fnames: ' . print_r($fnames, true) . '  ' . print_r($names, true));
            // logit('flabels: '. print_r($flabels, true).'  '. print_r($labels, true));
          logit("NAME: {$name}");
          if ($name == 'ncexcel') {
            // this is the non compliance excel report
            logit("$name");
            $audit_id = $list[0];
            $data = $this->genNCReport($audit_id);
            $i = 1;
            $flabels = array('Non Conformities','Recommendations/Comments',
                'Checklist Question','ISO 15189 References','Major/Minor');

            $fnames = array('comment','nc','question','isp','mm');
            $this->startWorkSheet($filehandle, $i, $heading, $flabels, $fnames, $data);
            $fname = "NC_SLIPTA_{$audit_id}.xlsx";
          } else {
            switch ($name) {

              case 'slmta2excel' :
                $fname = 'SLMTA_stats.xlsx';
                break;
              case 'slipta2excel' :
                $fname = 'SLIPTA_stats.xlsx';
                break;
              case 'bat2excel' :
                $fname = 'BAT_stats.xlsx';
                // $fnames = array('audit_id');//'audit_id', 'labname', 'labnum', 'end_date');
                // $flabels = array('Audit Id', 'Audit Date', 'Labname', 'Labnum');
                break;
              case 'tb2excel' :
                $fname = 'TB_stats.xlsx';
                // $fnames = array('audit_id'); //audit_id', 'labname', 'labnum', 'end_date');
                // $flabels = array('Audit Id', 'Audit Date', 'Labname', 'Labnum');
                break;
              default :
            }
            if ($query) {
              eval("\$sql = \"$query\"; ");
              logit("CALC Q: {$sql}");
            }
            $rows = $report->runQuery($sql);
            logit('ROWS: ' . print_r($rows, true));

            //if (in_array($name, array('slmta2excel', 'slipta2excel'))) {
              $data = $this->collectRows($rows);
            /*} else {
              // collect with implied fields
              $idata = $this->icollectRows($rows);
              $data = $idata[0];
              logit('D: '. print_r($data, true));
              $labels = $idata[1];
              $names = $idata[2];
              asort($labels);
              $flabels = array_merge($flabels, $labels);
              $fnames = array_merge($fnames, $names);
            }*/
            //logit("SQL: {$sql} " . print_r($rows, true));
            $this->startWorkSheet($filehandle, $i, $heading, $flabels, $fnames, $data);
          }
        }
        $filename = $this->saveFile($filehandle);
        logit("FN: $filename");

        logit("Filename: {$filename} {$fname}");
        //$this->_helper->layout->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $fstr = file_get_contents($filename);
        logit("LEN: " . strlen($fstr));
        header ("Content-type: octet/stream");
        header ("Content-disposition: attachment; filename=".$fname.";");
        header ("Content-Length: ".filesize($filename));
        readfile($filename);
        return;

        // return $filename;
        break;
      case 'html' :
        // this is going to be an image file
        // Generate an HTML file and insert in it a graphic image
        $this->createHTMLFile();
        break;
      case 'graph' :
        logit("Processing tab 1");

        $tabinfo = $tinfo[1];
        $fnames = array();
        $flabels = array();
        $sql = '';
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
        // $audits = $audit->_mkList($list);
        /*$names = $this->_mkList($fnames);
        $audits = $this->_mkList($list);
        if ($query) {
          eval("\$sql = \"$query\"; ");
          logit("CALC Q: {$sql}");
        }
        $rows = $report->runQuery($sql);
        logit('ROWS: ' . print_r($rows, true));
        $data = $this->collectRows($rows);
        */
        logit("Heading: {$heading}");
        // logit('fnames: ' . print_r($fnames, true) . '  ' . print_r($names, true));
        switch ($name) {
        	case 'spiderchart':
        	  logit("AUDITS: {$audits}");
        	  $sql = <<<"SQL"
select * from audit_data
 where audit_id {$audits}
   and (field_name like 's__\_total' or field_name like 'final_score')
 order by field_name
SQL;
        	  $arows = $check->queryRows($sql);
        	  logit("AR: " . print_r($arows, true));
        	  $data = $this->collectRows($arows);
        	  $mql = <<<"SQL"
select varname, score from template_row
 where (varname like 's__\_total' or varname like 'all_total')
 order by varname
SQL;
        	  $mrows = $check->queryRows($mql);
        	  logit("M: ". print_r($mrows, true));
        	  $totals = array();
        	  foreach ($mrows as $m) {
        	    $totals[] = $m['score'];
        	  }
        	  logit("SQL: {$sql}");
        	  // logit("AROWS: " . print_r($arows, true));
        	  logit("DATA: " . print_r($data, true));
        	  $img = $this->spider_chart($data, $totals);
        	  $this->base->view->img = $img;
        	  return 1;
        	  break;
        	case 'barchart':
        	  logit("AUDITS: {$audits}");
        	  $sql = <<<"SQL"
select * from audit_data
 where audit_id {$audits}
   and (field_name like 's__\_total' or field_name like 'final_score')
 order by field_name
SQL;
        	  $arows = $check->queryRows($sql);
        	  logit("AR: " . print_r($arows, true));
        	  $data = $this->collectRows($arows);
        	  $mql = <<<"SQL"
select varname, score from template_row
 where (varname like 's__\_total' or varname like 'all_total')
 order by varname
SQL;
        	  $mrows = $check->queryRows($mql);
        	  logit("M: ". print_r($mrows, true));
        	  $totals = array();
        	  foreach ($mrows as $m) {
        	    $totals[] = $m['score'];
        	  }
        	  logit("SQL: {$sql}");
        	  // logit("AROWS: " . print_r($arows, true));
        	  logit("DATA: " . print_r($data, true));
        	  $img = $this->parallel_barchart($data, $totals);
        	  $this->base->view->img = $img;
        	  return 1;
        	  break;
        	  break;
        	case 'incompletechart':
        	  break;
        	default:
        }
        break;
      default :
    }
  }

}


