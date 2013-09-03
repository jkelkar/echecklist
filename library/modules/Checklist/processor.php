<?php
// this file handles all the output related processing
//  whether it is in Excel output or as reports and images

require_once 'modules/Checklist/logger.php';
/** Include PHPExcel */
require_once 'modules/Classes/PHPExcel.php';

class Processing {

  /**
   * This function is used to generate the column names for excel file
   * i2a converts an integer into letters
   * 1-26 -> 'A' .. 'Z'
   * 27 -> AA ... 52 -> 'AZ'
   * 104 -> 'CZ' and so on
   * This function only handles up to ZZ or $v(1..26*27)
   */
  private function i2a($v) {
    if ($v < 0 || $v > 26 * 27) {
      return null;
    }
    if ($v > 0 && $v <= 26) {
      $o = 'A';
      for($i = 0; $i < $v - 1; $i ++) {
        $o ++;
      }
      return $o; //'A' + ($v - 1);
    } else {
      $rem = $v % 26;
      $rem = ($rem == 0) ? 26 : $rem;
      return i2a(int($v / 26)) + i2a($rem);
    }
  }

  private function rc($col, $row) {
    // echo 'I2A: ' . $col . ' ' . $row . ' ' . i2a($col, $row);
    return $this->i2a($col) . $row;
  }

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

  private function convertRow($row) {
    // convert the row to a value
    $format = 'm/d/Y';
    $ISOformat = 'Y-m-d';

    $val = '';
    $field_name = $row['field_name'];
    switch ($row['field_type']) {
      case 'integer' :
        $val = $row['int_val'];
        break;
      case 'text' :
        $val = $row['text_val'];
        break;
      case 'date' :
        $dt = date_parse_from_format($ISOformat, $row['date_val']);
        $date = new DateTime();
        $date->setDate($dt['year'], $dt['month'], $dt['day']);
        $val = $date->format($format);
        break;
      case 'bool' :
        $val = $row['bool_val'];
        break;
      case 'string' :
      default :
        $val = $row['string_val'];
    }
    return $val;
  }

  private function collectRows($rows) {
    $data = array();
    foreach($rows as $row) {
      $audit_id = $row['audit_id'];
      $fname = $row['field_name'];
      if (! key_exists($audit_id, $data)) {
        $data[$audit_id] = array();
      }
      $data[$audit_id][$fname] = $this->convertRow($row);
    }
    return $data;
  }

  private function _mkList($data) {
    $out = '';
    switch (count($data)) {
      case 0 :
        break;
      case 1 :
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
        return "in ({$out})";
    }
  }

  public function process($list, $name) {
    // this is the driver for processing the request for output generation
    // $list - the list of selected audits
    // $name -
    $report = new Application_Model_DbTable_Report();
    $tinfo = $this->collect($name);
    $numtabs = count($tinfo) - 1;
    logit("Tabs: {$numtabs}");
    $start = 0;
    $excelfile = null;
    $heading = $tinfo['heading'];
    for($i = 1; $i <= $numtabs; $i ++) {
      logit("Processing tab {$i}");
      if ($start == 0) {
        $excelfile = $this->startExcelDoc($heading);
      }
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
      $this->startWorkSheet($excelfile, $i, $heading, $flabels, $fnames, $data);
      $this->saveFile($excelfile);
    }
  }

  public function startExcelDoc($heading) {
    //
    //define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    // Create new PHPExcel object
    // echo date('H:i:s') , " Create new PHPExcel object" , EOL;
    $objPHPExcel = new PHPExcel();

    // Set document properties
    // echo date('H:i:s') , " Set document properties" , EOL;
    $objPHPExcel->getProperties()->setCreator("Jay Kelkar")
    ->setLastModifiedBy("Jay Kelkar")
    ->setTitle("{$heading}");
    //->setSubject("All the rows")
    //->setDescription("This is a list of all the rows in the albums table")
    //->setKeywords("office PHPExcel php")
    //->setCategory("Test result file");
    return $objPHPExcel;
  }

  public function startWorkSheet($objPHPExcel, $tabnum, $heading, $labels, $names, $data) {
    $styleArray = array(
        'borders'=> array(
            'outline'=> array(
                'style'=> PHPExcel_Style_Border::BORDER_THICK,
                'color'=> array(
                    'argb'=> 'FF000000'
                )
            )
        )
    );
    logit('Add header');
    // echo date('H:i:s'), " Add some data", EOL;
    $s = $objPHPExcel->setActiveSheetIndex($tabnum-1);
    // Rename worksheet
    // echo date('H:i:s') , " Rename worksheet" , EOL;
    logit("Rename worksheet: {$heading}");
    $objPHPExcel->getActiveSheet()->setTitle($heading);

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    // $objPHPExcel->setActiveSheetIndex($tabnum-1);

    $i = 0;
    $col = 1;
    $row = 1;
    // $s0->setCellValue(rc($col, $row), 'Table data: Albums');
    $s->mergeCells('A1:H1');
    $s->getStyle('A1')->applyFromArray($styleArray);
    $s->setCellValue('A1', $heading);
    $col = 1;
    $row = 3;
    // Insert the header line
    foreach($labels as $name) {
      $s->setCellValue($this->rc($col, $row), $name);
      $s->getStyle($this->rc($col, $row))->getAlignment()->setHorizontal(
          PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

      $s->getStyle($this->rc($col, $row))->getFont()->setBold(true);
      $col ++;
    }
    $row ++;
    $i++;

    $col = 1;
    logit('NAMES: ' . print_r($names, true));
    foreach($data as $d) {
      $col = 1;
      foreach($names as $name) {
        if ($name != '') {
          if (key_exists($name, $d)) {
            $s->setCellValue($this->rc($col, $row), $d[$name]);
          }
          /*if ($name == 'id') {
          $s->getStyle(rc($col, $row))->getAlignment()->setHorizontal(
              PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }*/
        }
        $col ++;
      }
      $row ++;
    }
  }

  private function randFileName($suffix) {
    $filename = "checklist_" . uniqid() . '.' . $suffix;
    return $filename;
  }

  public function saveFile($objPHPExcel) {
    // write the file out
    logit(" Write to Excel5 format");
    // $callStartTime = microtime(true);

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $path = dirname(__DIR__) . '/../../public/tmp/';
    $filename = $this->randFileName('xls');
    $fileloc = "{$path}{$filename}";
    logit("FilePath: {$fileloc}");
    // $objWriter->save(str_replace('.php', '.xls', __FILE__)); // file name here
    // $callEndTime = microtime(true);
    // $callTime = $callEndTime - $callStartTime;
    $objWriter->save($fileloc);
    // Echo done
    logit("Done writing files");
    logit("Files have been created in {$fileloc}.");

  }
}