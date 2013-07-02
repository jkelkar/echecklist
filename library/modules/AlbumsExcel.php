<?php
echo 'In AlbumsExcel.php';


/** Include PHPExcel */
require_once 'Classes/PHPExcel.php';

/**
 * This function is used to generate the column names for excel file
 * i2a converts an integer into letters
 * 1-26 -> 'A' .. 'Z'
 * 27 -> AA ... 52 -> 'AZ'
 * 104 -> 'CZ' and so on 
 * This function only handles up to ZZ or $v(1..26*27)
 */
function i2a($v) {
  if ($v < 0 || $v > 26 * 27) {
    return null;
  }
  if ($v > 0 && $v<= 26) {
    $o = 'A';
    for($i = 0; $i < $v-1; $i++) {
      $o++;
    }
    return $o; //'A' + ($v - 1);
  } else {
    $rem = $v % 26;
    $rem = ($rem == 0) ? 26 : $rem;
    return i2a(int($v/26)) + i2a($rem);
  }
}

function rc ($col, $row) {
  // echo 'I2A: ' . $col . ' ' . $row . ' ' . i2a($col, $row);
  return i2a($col) . $row;
}

function doit($rows)
{
  /** Error reporting */
  error_reporting(E_ALL);
  ini_set('display_errors', TRUE);
  ini_set('display_startup_errors', TRUE);
  date_default_timezone_set('America/New_York');
  
  define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

  // Create new PHPExcel object
  echo date('H:i:s') , " Create new PHPExcel object" , EOL;
  $objPHPExcel = new PHPExcel();

  // Set document properties
  echo date('H:i:s') , " Set document properties" , EOL;
  $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Jay Kelkar")
    ->setTitle("Albums in DB")
    ->setSubject("All the rows")
    ->setDescription("This is a list of all the rows in the albums table")
    ->setKeywords("office PHPExcel php")
    ->setCategory("Test result file");

  /**
   * styling borders
   */
  $styleArray = array
    (
     'borders' => array
     (
      'outline' => array
      (
       'style' =>PHPExcel_Style_Border::BORDER_THICK,
       'color' => array('argb' => 'FF000000'),
       )
      )
     );

  // Add some data
  echo date('H:i:s') , " Add some data" , EOL;
  $s0 = $objPHPExcel->setActiveSheetIndex(0);
  $col = 1; $row = 1;
  // $s0->setCellValue(rc($col, $row), 'Table data: Albums');
  $s0->mergeCells('A1:E1');
  $s0->getStyle('A1')->applyFromArray($styleArray);
  $s0->setCellValue('A1', 'Table data: Albums');
  $col = 1; $row = 3;
  foreach (array('id', 'title', 'artist') as $name) {
    $s0->setCellValue(rc($col, $row), $name);
    $s0->getStyle(rc($col, $row))->getAlignment()->setHorizontal
	(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
    
    $s0->getStyle(rc($col, $row))->getFont()->setBold(true);
    $col ++;
  }
  $row++;
 
  $col = 1;
  foreach($rows as $drow) {
    $col = 1;
    foreach (array('id', 'title', 'artist') as $name) {
      $s0->setCellValue(rc($col, $row), $drow[$name]);
      if ($name == 'id') {
	$s0->getStyle(rc($col, $row))->getAlignment()->setHorizontal
	  (PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      }
      $col ++;
    }
    $row++;
  }    
  /**
   // $s0->setCellValue(i2a($col) + $row, //'A1'
   $s0->setCellValue(rc($col, $row), 'Hello');
   $col = 2; $row =2;
   $s0->setCellValue(rc($col, $row), 'world!');
   $col = 3; $row = 1;
   $s0->setCellValue(rc($col, $row), 'Hello');
   $col = 4; $row = 2;
   $s0->setCellValue(rc($col, $row), 'world!');
  */
  // Miscellaneous glyphs, UTF-8
  /*$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A4', 'Miscellaneous glyphs')
    ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
  */

  $objPHPExcel->getActiveSheet()->setCellValue('A25',"Hello\nWorld");
  $objPHPExcel->getActiveSheet()->getRowDimension(25)->setRowHeight(-1);
  $objPHPExcel->getActiveSheet()->getStyle('A25')->getAlignment()->setWrapText(true);


  // Rename worksheet
  echo date('H:i:s') , " Rename worksheet" , EOL;
  $objPHPExcel->getActiveSheet()->setTitle('Simple');


  // Set active sheet index to the first sheet, so Excel opens this as the first sheet
  $objPHPExcel->setActiveSheetIndex(0);

  /**
   // Save Excel 2007 file
   echo date('H:i:s') , " Write to Excel2007 format" , EOL;
   $callStartTime = microtime(true);
   
   $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
   $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
   $callEndTime = microtime(true);
   $callTime = $callEndTime - $callStartTime;
   
   echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
   echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
   // Echo memory usage
   echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
  */

  // Save Excel 95 file
  echo date('H:i:s') , " Write to Excel5 format" , EOL;
  $callStartTime = microtime(true);

  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
  $objWriter->save(str_replace('.php', '.xls', __FILE__));
  $callEndTime = microtime(true);
  $callTime = $callEndTime - $callStartTime;

  /**
     echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
     echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
     // Echo memory usage
     echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
     
     
     // Echo memory peak usage
     echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;
  */
  // Echo done
  echo date('H:i:s') , " Done writing files" , EOL;
  echo 'Files have been created in ' , getcwd() , EOL;

}