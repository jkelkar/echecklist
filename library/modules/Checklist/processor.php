<?php
// this file handles all the output related processing
//  whether it is in Excel output or as reports and images
// require_once "../models/DbTable/Checklist.php";
require_once 'modules/Checklist/logger.php';

class Processing {

  private function collect($name) {
    // get data from table report for name
    // and package the data in a structure and return
    $report = new Application_Model_DbTable_Report();
    $rid = $report->getReportId($name);
    //logit("Report Id: {$rid}");
    $rows = $report->getReportRows($rid);
    //logit("report: " . print_r($rows, true));

    $namelist = array();
    $heading = $tabheading = '';
    $guide = array();
    foreach($rows as $row) {
      // the overall heading
      if ($row['tabpos'] == 0) {
        $guide['heading'] = $row['field_label'];
      } else {
        // in non tab 0
        $tabpos = $row['tabpos'];
        $pos = $row['position'];
        if (! key_exists($tabpos, $guide)) {
          $guide[$tabpos] = array();
        }
        if ($row['position'] == 0) {
          $guide[$tabpos]['heading'] = $row['field_label'];
          $guide[$tabpos]['query'] = $row['query'];
        } else {
          $guide[$tabpos][$pos] = array(
              $row['field_label'],
              $row['table_name'],
              $row['field_name']
          );
        }
      }
    }
   // logit('GUIDE: ' . print_r($guide, true));
    return $guide;
  }

  private function _mkList($data) {
    $out = '';
    // if (count($data) == 0) {
    //  return
    switch (count($data)) {
      case 0 :
        //logit("0: {$data} --". print_r($data, true));
        break;
      case 1 :
        //logit("A: = '{$data[0]}' ");
        if (is_string($data[0]))
          return "= '{$data[0]}' ";
        else
          return "= {$data[0]} ";
        break;
      default :
        foreach($data as $d) {
          if ($d == '')
            continue;
          if ($out != '')
            $out .= ',';
          if (is_string($d))
            $out .= "'{$d}'";
          else
            $out .= " {$d}";
        }
        //logit("A: = in ({$out}) ");
        return "in ({$out})";
    }
  }

  public function process($list, $name) {
    // this is the driver for processing the request for output generation
    // $list - the list of selected audits
    // $name -
    $report = new Application_Model_DbTable_Report();
    $tinfo = $this->collect($name);
    $numtabs = count($tinfo) -1;
    logit("Tabs: {$numtabs}");
    $heading = $tinfo['heading'];
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
      $labels = $this->_mkList($flabels);
      $names = $this->_mkList($fnames);
      $audits = $this->_mkList($list);
      logit("Heading: {$heading}");
      logit('fnames: '. print_r($fnames, true) .'  '.print_r($names, true));
      logit('flabels: '. print_r($flabels, true).'  '. print_r($labels, true));
      eval("\$sql = \"$query\"; ");
      logit("CALC Q: {$sql}");
      $rows = $report->runQuery($sql);
      logit('ROWS: '. print_r($rows, true));
    }
    exit();
  }


  public function startExcelDoc() {
  }

  public function startWorkSheet() {
  }

  public function writeHeadingRow($row) {
  }

  public function writeDataRows($rows) {
  }

  public function generateFile($name, $type) {
  }
}