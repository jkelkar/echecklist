<?php
// this file handles all the output related processing
//  whether it is in Excel output or as reports and images

/** Include PHPExcel */
require_once 'Classes/PHPExcel.php';
/* include ChartDirector */
require_once 'ChartDirector/lib/phpchartdir.php';

class Checklist_Modules_Processor extends Checklist_Modules_ProcessCommon {
  public $base ;

  public function process($base, $list, $name) {
    // this is the driver for processing the request for output generation
    // gets the config data from table report, converts it into a struct
    //    then creates SQL query to extract required data from DB into rows
    // base is the this pointer from the controller
    // $list - the list of selected audits
    // $name -
    $this->base = $base;
    //$check = new Checklist_controller_action();
    //$audit = new Application_Model_DbTable_Audit();
    $report = new Application_Model_DbTable_Report();
    $check = new Checklist_Model_Base();
    $name = $this->getAuditName($name, $base->data['audit_type']);
    $tinfo = $this->collect($name);
    // $this->log->logit("tinfo: ".print_r($tinfo, true));
    $numtabs = count($tinfo) - 3; // -3 to ignore the heading, report_type, and file_type
    $this->log->logit("Tabs: {$numtabs}");
    //$start = 0;
    $filehandle = null;
    $heading = $tinfo['heading'];
    $report_type = $tinfo['report_type'];
    $file_type = $tinfo['file_type'];
    $this->log->logit("ft: {$file_type}");
    $fname = '';
    $this->log->logit("LIST: " . print_r($list, true));
    $audits = $check->_mkList($list);
    switch ($file_type) {
      case 'excel' :
        // this is going to be an excel file
        $filehandle = $this->startExcelDoc($heading);
        for($i = 1; $i <= $numtabs; $i ++) {
          $this->log->logit("Processing tab {$i}");

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
                $flabels[] = $v['field_label'];
                $fnames[] = $v['field_name'];
            }
          }
          $names = $check->_mkList($fnames);
          $this->log->logit("Heading: {$heading}");
          //$this->log->logit('fnames: ' . print_r($fnames, true) . '  ' . print_r($names, true));
          // $this->log->logit('flabels: '. print_r($flabels, true).'  '. print_r($labels, true));
          $this->log->logit("NAME:  {$name}");
          if ($name == 'ncexcel') {
            // this is the non compliance excel report
            $this->log->logit("$name");
            $audit_id = $list[0];
            $data = $this->genNCReport($audit_id, $base->data['audit_type']);
            $i = 1;
            $flabels = array('Non Conformities','Recommendations/Comments',
                'Checklist Question','ISO 15189 References','Major/Minor');

            $fnames = array('comment','nc','question','isp','mm');
            $this->startWorkSheet($filehandle, $i, $heading, $flabels, $fnames, $data);
            $ucatype = strtoupper($base->data['audit_type']);
            $fname = "NC_{$ucatype}_{$audit_id}.xlsx";
          } else {
            switch ($name) {
              case 'slmta2excel' :
                $fname = 'SLMTA_stats.xlsx';
                break;
              case 'bat2excel' :
                $fname = 'BAT_stats.xlsx';
                break;
              case 'slipta2excel' :
                $this->log->logit('SLPTA2EXCEL');
                $fname = 'SLIPTA_stats.xlsx';
                break;
              case 'tb2excel' :
                $fname = 'TB_stats.xlsx';

              default :
            }
            if ($query) {
              eval("\$sql = \"$query\"; ");
              // $this->log->logit("CALC Q: {$sql}");
            }
            $rows = $report->runQuery($sql);
            //$this->log->logit('ROWS: ' . print_r($rows, true));
            $data = $this->collectRows($rows);
            $this->startWorkSheet($filehandle, $i, $heading, $flabels, $fnames, $data);
          }
        }
        $filename = $this->saveFile($filehandle);
        $this->log->logit("FN: $filename");

        $this->log->logit("Filename: {$filename}-->{$fname}");
        $fstr = file_get_contents($filename);
        $this->log->logit("LEN: " . strlen($fstr));
        header ("Content-type: octet/stream");
        header ("Content-disposition: attachment; filename=".$fname.";");
        header ("Content-Length: ".filesize($filename));
        readfile($filename);
        return;
        break;
      case 'html' :
        // this is going to be an image file
        // Generate an HTML file and insert in it a graphic image
        $this->createHTMLFile();
        break;
      case 'graph' :
        $this->log->logit("Processing tab 1");

        $tabinfo = $tinfo[1];
        $fnames = array();
        $flabels = array();
        $series = array();
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
          	  $flabels[] = $v['field_label'];
          	  $fnames[] = $v['field_name'];
          	  $series[] = $v['series'];
          }
        }
        $names = $check->_mkList($fnames);
        $this->log->logit("NAME:  {$name}");
        $this->log->logit("Heading: {$heading}");
        $this->log->logit('G fnames: ' . print_r($fnames, true)  . '  ' . print_r($names, true));
        $this->log->logit('flabels: '  . print_r($flabels, true) );
        $this->log->logit('series ; ' . print_r($series, true));
        $this->log->logit("Heading: {$heading}");
        if ($query) {
          eval("\$sql = \"$query\"; ");
          // $this->log->logit("CALC Q: {$sql}");
        }
        $rows = $report->runQuery($sql);
        //$this->log->logit('ROWS: ' . print_r($rows, true));
        $data = $this->collectRows($rows);
        $this->log->logit('DATA: '. print_r($data, true));
        switch ($name) {
          case 'spiderchart' :
            $this->log->logit("AUDITS: {$audits}");
            $sql = <<<"SQL"
select * from audit_data
 where audit_id {$audits}
   and (field_name like 's__\_total' or field_name like 'final_score')
 order by field_name
SQL;
            $arows = $check->queryRows($sql);
            $this->log->logit("AR: " . print_r($arows, true));
            $indata = $this->collectRows($arows);
            $mql = <<<"SQL"
select varname, score from template_row
 where (varname like 's__\_total' or varname like 'all_total')
 order by varname
SQL;
            $mrows = $check->queryRows($mql);
            $this->log->logit("M: " . print_r($mrows, true));
            $totals = array();
            foreach($mrows as $m) {
              if ($m['varname'] == 'all_total') {
                $totals['final_score'] = $m['score'];
              } else {
                $totals[$m['varname']] = $m['score'];
              }
            }
            $this->log->logit("Labels: " . print_r($totals, true));
            $data = array();
            foreach($indata as $id => $d) {
              $thisrow = array();
              foreach($totals as $tn => $tv) {
                $val = (key_exists($tn, $d)) ? $d[$tn] : 0;
                $this->log->logit("TOT: {$tn} => {$tv} : {$val}");
                $thisrow[$tn] = $val;
              }
              $data[$id] = $thisrow;
            }
            $this->log->logit("SQL: {$sql}");
            // $this->log->logit("AROWS: " . print_r($arows, true));
            $this->log->logit("DATA: " . print_r($data, true));
            $img = $this->spider_chart($data, $totals);
            $this->base->view->img = $img;
            return 1;
            break;
          case 'barchart' :
            $this->log->logit("AUDITS: {$audits}");
            $sql = <<<"SQL"
select * from audit_data
 where audit_id {$audits}
   and (field_name like 's__\_total' or field_name like 'final_score')
 order by field_name
SQL;
            $arows = $check->queryRows($sql);
            $this->log->logit("AR: " . print_r($arows, true));
            $indata = $this->collectRows($arows);
            $mql = <<<"SQL"
select varname, score from template_row
 where (varname like 's__\_total' or varname like 'all_total')
 order by varname
SQL;
            $mrows = $check->queryRows($mql);
            $this->log->logit("M: " . print_r($mrows, true));
            $totals = array();
            foreach($mrows as $m) {
              if ($m['varname'] == 'all_total') {
                $totals['final_score'] = $m['score'];
              } else {
                $totals[$m['varname']] = $m['score'];
              }
            }
            $this->log->logit("Labels: " . print_r($totals, true));
            $data = array();
            foreach($indata as $id => $d) {
              $thisrow = array();
              foreach($totals as $tn => $tv) {
                $val = (key_exists($tn, $d)) ? $d[$tn] : 0;
                $this->log->logit("TOT: {$tn} => {$tv} : {$val}");
                $thisrow[$tn] = $val;
              }
              $data[$id] = $thisrow;
            }
            /*
            $totals = array();
            foreach($mrows as $m) {
              $totals[] = $m['score'];
            }
            */
            $this->log->logit("SQL: {$sql}");
            // $this->log->logit("AROWS: " . print_r($arows, true));
            $this->log->logit("DATA: " . print_r($data, true));
            $img = $this->parallel_barchart($data, $totals);
            $this->base->view->img = $img;
            return 1;
            break;
            break;
          case 'slipta2inc' :
            $this->log->logit('S');
            break;
          case 'bat2inc' :
            $this->log->logit('B');
            $mql = <<<"SQL"
select sum(1) ct, substr(varname, 1,3) name
  from template_row
 where template_id =2 and varname like 's____'
   group by name
SQL;
            $mrows = $check->queryRows($mql);
            $counts = array();
            foreach($mrows as $n => $v) {
              $counts[$n] = $v;
            }
            $this->log->logit('Counts: ' . print_r($counts, true));
            $img = $this->stacked_barchart($data, $fnames, $flabels, $series, $counts);
            $this->base->view->img = $img;
            return 1;
            break;
          case 'tb2inc' :
            $this->log->logit('T');
            $mql = <<<"SQL"
select sum(1) ct, substr(varname, 1,3) name
  from template_row
 where template_id =3 and varname like 's____'
   group by name
SQL;
            $mrows = $check->queryRows($mql);
            $counts = array();
            foreach($mrows as $n => $v) {
              $counts[$n] = $v;
            }
            $this->log->logit('Counts: ' . print_r($counts, true));
            $img = $this->stacked_barchart2($data, $fnames, $flabels, $series, $counts);
            $this->base->view->img = $img;
            return 1;
            break;
          default :
        }

        break;
      default :
    }
  }

}


