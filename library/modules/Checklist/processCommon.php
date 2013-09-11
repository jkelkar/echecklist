<?php

class Process_Common {
  // This is the base class for all the processing


  /**
   * This function is used to generate the column names for excel file
   * i2a converts an integer into letters
   * 1-26 -> 'A' .. 'Z'
   * 27 -> AA ... 52 -> 'AZ'
   * 104 -> 'CZ' and so on
   * This function only handles up to ZZ or $v(1..26*27)
   */
  public function i2a($v) {
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
      if ($rem == 0) {
        $rem = 26;
        return $this->i2a(intval($v/26)-1) . $this->i2a($rem);
      } else {
        return $this->i2a(intval($v / 26)) . $this->i2a($rem);
      }
    }
  }

  public function rc($col, $row) {
    // echo 'I2A: ' . $col . ' ' . $row . ' ' . i2a($col, $row);
    return $this->i2a($col) . $row;
  }

  public function collect($name) {
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
        $guide['report_type'] = $row['report_type'];
        $guide['file_type'] = $row['file_type'];
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

  public function convertRow($row) {
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

  public function collectRows($rows) {
    $data = array();
    foreach($rows as $row) {
      $audit_id = $row['audit_id'];
      $fname = $row['field_name'];
      if (! key_exists($audit_id, $data)) {
        $data[$audit_id] = array('audit_id' => $audit_id);
      }
      $data[$audit_id][$fname] = $this->convertRow($row);
    }
    return $data;
  }

  /*public function x_mkList($data) {
    logit("MKL: {$data} " . print_r($data, true));
    $out = '';
    // if (count($data) == 0) {
    //  return
    if (is_string($data)) {
      logit('STR');
      return "= '{$data}' ";
    } else {
      logit('ARR');
      switch (count($data)) {
        case 0 :
          //logit("0: {$data} --". print_r($data, true));
          break;
        case 1 :
          //logit("A: = '{$data[0]}' ");
          if ($data[0] == '-')
            return "= '{$data[0]}' ";
          break;
        default :
          foreach($data as $d) {
            if ($out != '')
              $out .= ',';
            if (is_string($d))
              $out .= "'{$d}'";
          }
          //logit("A: = in ({$out}) ");
          return "in ({$out})";
      }
    }
  }
  */

  public function randFileName($suffix, $prefix = '') {
    $uniq = uniqid();
    if ($prefix != '') {
      $filename = "{$prefix}_{$uniq}.{$suffix}";
    } else {
      $filename = "checklist_{$uniq}.$suffix";
    }
    return $filename;
  }

  public function startExcelDoc($heading) {
    // Create new PHPExcel object
    global $user;
    $objPHPExcel = new PHPExcel();

      // Set document properties
    $objPHPExcel->getProperties()
      ->setCreator($user['name'])
      ->setLastModifiedBy($user['name'])
      ->setTitle("{$heading}");
    // ->setSubject("All the rows")
    // ->setDescription("This is a list of all the rows in the albums table")
    // ->setKeywords("office PHPExcel php")
    // ->setCategory("Test result file");
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
    if ($tabnum > 1)
      $objPHPExcel->createSheet($tabnum - 1);
    $s = $objPHPExcel->setActiveSheetIndex($tabnum - 1);
    // Rename worksheet
    logit("Rename worksheet: {$heading}");
    $objPHPExcel->getActiveSheet()->setTitle($heading);

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    // $objPHPExcel->setActiveSheetIndex($tabnum-1);


    $i = 0;
    $col = 1;
    $row = 1;
    // $s->setCellValue($this->rc($col, $row), 'Table data: Albums');
    $s->mergeCells('A1:E1');
    $s->getStyle('A1')->applyFromArray($styleArray);
    $s->setCellValue('A1', $heading);
    $col = 1;
    $row = 3;
    // Insert the header line
    foreach($labels as $name) {
      logit("SCV: {$name} {$row} {$col} ". $this->rc($col, $row));
      $s->setCellValue($this->rc($col, $row), $name);
      $s->getStyle($this->rc($col, $row))->getAlignment()->setHorizontal(
          PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

      $s->getStyle($this->rc($col, $row))->getFont()->setBold(true);
      $col ++;
    }
    $row ++;
    $i ++;

    $col = 1;
    logit('NAMES: ' . print_r($names, true));
    //logit('DATA EXCEL: ' . print_r($data, true));
    foreach($data as $d) {
      $col = 1;
      foreach($names as $name) {
        if ($name != '') {
          $dn = get_arrval($d, $name, '');
          $cn = $this->rc($col, $row);
          if (key_exists($name, $d)) {
            // logit("ED: {$row} {$col} {$cn} = '{$name}' : '{$dn}'");
            $s->setCellValue($this->rc($col, $row), $d[$name]);
          }
        }
        $col ++;
      }
      $row ++;
    }
  }

  public function saveFile($objPHPExcel) {
    // write the file out
    logit(" Write to Excel2007 format");
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $path = dirname(__DIR__) . '/../../public/tmp/';
    $secs = 3600;
    $this->rmOldFiles($path, $secs);
    // FIXME: add in code to delete any files that are over an hour old
    $filename = $this->randFileName('xlsx');
    $fileloc = "{$path}{$filename}";
    logit("FilePath: {$fileloc}");
    $objWriter->save($fileloc);
    // Echo done
    // logit("Done writing files");
    logit("File has been created in {$fileloc}.");
    return $fileloc;
  }

  public function genNCReport($audit_id) {
    // generate the non compliance report$v['varname']
    logit("AI: {$audit_id}");
    global $langtag;
    $ar = new Application_Model_DbTable_AuditData();
    $au = new Application_Model_DbTable_Audit();
    $tr = new Application_Model_DbTable_TemplateRows();
    $audit = $au->get($audit_id);
    $tid = $audit['template_id'];
    $regex = '^s[0-9]{4,6}$';
    $trows = $tr->getByVarname($tid, $regex);
    $vlist = array();
    foreach($trows as $tx) {
      if (! $tx['varname'])
        continue;
      // logit("- {$tx['varname']}");
      $vlist[$tx['varname']] = $tx;
    }
    $arows = $ar->getAllData($audit_id);
    $all = array();
    foreach($vlist as $v) {
      $vname = $v['varname'];
      // logit("V: {$vname}");
      $vlen = strlen($vname);
      $val = $q = '';
      switch ($vlen) {
      	case 5:
      	  $q = (int) substr($vname, 1, 2) . '.' . (int) substr($vname, 3, 2);
      	  $key = $vname;
      	  if (key_exists($key, $arows))  $val = get_arrval($arows, $key, '');
      	  $key = "{$vname}_ynp";
      	  if (key_exists($key, $arows))  $val = get_arrval($arows, $key, '');
      	  break;
      	case 7:
      	  $q = (int) substr($vname, 1, 2) . '.' . (int) substr($vname, 3, 2) . '.' . (int) substr($vname, 5, 2);
      	  $key = "{$vname}_yn";
      	  if (key_exists($key, $arows))  $val = get_arrval($arows, $key, '');
      	  $key = "{$vname}_yna";
      	  if (key_exists($key, $arows))  $val = get_arrval($arows, $key, '');
      	  break;
      	default;
      }
      logit("Question: {$q}");
      if ($val != 'YES'){
        $comment = get_arrval($arows, "{$vname}_comment", '');
        $nc = get_arrval($arows, "{$vname}_nc", '');
        $ncnote = '';
        if ($nc == 'T')
          $ncnote = get_arrval($arows, "{$vname}_note", '');
        $all[] = array(
            'comment' => $comment,
            'nc' => $ncnote,
            'mm' => '',
            'question' => "Q$q",
            'iso' => ''
        );
      }

    }
    //logit("ALL: " . print_r($all, true));
    return $all;
  }

  public function rmOldFiles($path, $secs) {
    // from the path remove all files older than secs seconds
    // logit("DP: {$path} -- {$secs}");
    if ($handle = opendir($path)) {
      while (false !== ($file = readdir($handle))) {
        if ($file == '.' || $file == '..') continue;
        if ((time() - filemtime($path . $file)) > $secs) {
          unlink($path . $file);
        }
      }
    }
  }

  // generate graphs here
  public function spider_chart($data, $totals) {
    // this is a fully localized test image - uses not outside info
    $labels = array("Total<*br*>Score","Section<*br*>1","Section<*br*>2","Section<*br*>3",
        "Section<*br*>4","Section<*br*>5","Section<*br*>6","Section<*br*>7",
        "Section<*br*>8","Section<*br*>9","Section<*br*>10","Section<*br*>11",
        "Section<*br*>12");
    $c = new PolarChart(860, 600, 0xe0e0e0, 0x000000, 1);

    $textBoxObj = $c->addTitle("SLIPTA Audit Scoring", "arialbi.ttf", 15);
    //$textBoxObj->setBackground($c->patternColor(dirname(__FILE__)."/wood.png"));


    # Set center of plot area at (230, 280) with radius 180 pixels, and white (ffffff)
    # background.
    $c->setPlotArea(330, 350, 235, 0xffffff);

    # Set the grid style to circular grid
    $c->setGridStyle(false);

    # Add a legend box at top-center of plot area (230, 35) using horizontal layout. Use
    # 10 pts Arial Bold font, with 1 pixel 3D border effect.
    $b = $c->addLegend(700, 35, true, "arialbd.ttf", 9);
    $b->setAlignment(TopCenter);
    $b->setBackground(Transparent, Transparent, 1);

    # Set angular axis using the given labels
    $c->angularAxis->setLabels($labels);

    # Specify the label format for the radial axis
    $c->radialAxis->setLabelFormat("{value}%");

    # Set radial axis label background to semi-transparent grey (40cccccc)
    $textBoxObj = $c->radialAxis->setLabelStyle();
    $textBoxObj->setBackground(0x40cccccc, 0);

    # Add the data as area layers
    $colrs = array(
      0x66ff0000, 0x6600ff00, 0x660000ff #0xf0cc0000, 0xf000cc00, 0xf00000cc
    );
    $i = -1;
    foreach ($data as $id => $d) {
      $i++;
      logit("Data: ". print_r($d, true));
      $dx = array();
      $j = -1;
      foreach (array_slice($d, 1) as $n => $v) {
        $j++;
        $dx[] = (int) ($v/(int)$totals[$j] * 100);
      }
      $c->addAreaLayer($dx, $colrs[$i], "Audit {$id}");
    }
    # Output the chart
    $path = dirname(__DIR__) . '/../../public/tmp/';
    $filename = $this->randFileName('png', 'spiderchart');
    $fname = "{$path}{$filename}";
    $imgpath = "{$this->base->baseurl}/tmp/{$filename}";
    logit("IMG: {$this->base->baseurl}-{$path}-{$filename}-{$fname}-{$imgpath}");
    logit("FN: {$fname}");
    $c->makeChart($fname);
    return $imgpath;
  }

  public function parallel_barchart($data, $totals) {
    # The data for the bar chart
    /*

    $data0 = array(70, 22, 45, 67, 23, 13, 59, 63, 34, 12, 13, 15, 17);
    $data1 = array(23, 13, 59, 63, 34, 12, 13, 15, 17, 70, 22, 45, 67);
    $data2 = array(34, 12, 13, 15, 17, 70, 22, 45, 67, 23, 13, 59, 63);
    */
    # The labels for the bar chart
    $labels = array("All","Section 1","Section 2","Section 3","Section 4","Section 5",
    "Section 6","Section 7","Section 8","Section 9","Section 10","Section 11",
    "Section 12");

    # Create a XYChart object of size 500 x 320 pixels
    $c = new XYChart(700, 420);

    # Set the plotarea at (100, 40) and of size 280 x 240 pixels
    $c->setPlotArea(80, 90, 580, 240);

    # Add a legend box at (400, 100)
    $c->addLegend(80, 40)->setCols(3); //400, 100);

    # Add a title to the chart using 14 points Times Bold Itatic font
    $c->addTitle("Completeness Levels - by section", "timesbi.ttf", 14);

    # Add a title to the y axis. Draw the title upright (font angle = 0)
    $textBoxObj = $c->yAxis->setTitle("Items Counts");
    $textBoxObj->setFontAngle(90);

    # Set the labels on the x axis
    $c->xAxis->setLabels($labels)->setFontAngle(45);

    # Add a stacked bar layer and set the layer 3D depth to 8 pixels
    $layer = $c->addBarLayer2();
    // set the bar gap
    $layer->setBarGap(0.4, TouchBar);
    $colrs = array(
        0xe0ff0000, 0xe000ff00, 0xe00000ff,
        0xf0cc0000, 0xf000cc00, 0xf00000cc
    );
    $i = -1;
    foreach ($data as $id => $d) {
      $i++;
      logit("Data: ". print_r($d, true));
      $dx = array();
      $j = -1;
      foreach (array_slice($d, 1) as $n => $v) {
        $j++;
        $dx[] = (int) ($v/(int)$totals[$j] * 100);
      }
      $layer->addDataSet($dx, $colrs[$i], "Audit {$id}");
    }
    # Enable bar label for the whole bar
    $layer->setAggregateLabelStyle();

    # Enable bar label for each segment of the stacked bar
    $layer->setDataLabelStyle();

    # Output the chart
    $path = dirname(__DIR__) . '/../../public/tmp/';
    $filename = $this->randFileName('png', 'barchart');
    $fname = "{$path}{$filename}";
    $imgpath = "{$this->base->baseurl}/tmp/{$filename}";
    logit("IMG: {$this->base->baseurl}-{$path}-{$filename}-{$fname}-{$imgpath}");
    logit("FN: {$fname}");
    $c->makeChart($fname);
    return $imgpath;
    /*# Add the three data sets to the bar layer

    $layer->addDataSet($data2, 0x00ff00, "Yes");
    $layer->addDataSet($data1, 0xff0000, "No");
    $layer->addDataSet($data0, 0xffffff, "Not Answered");

    # Enable bar label for the whole bar
    $layer->setAggregateLabelStyle();

    # Enable bar label for each segment of the stacked bar
    $layer->setDataLabelStyle();

    # Output the chart
    header("Content-type: image/png");
    print($c->makeChart2(PNG));*/
  }

}