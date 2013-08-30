<?php
// this file handles all the output related processing
//  whether it is in Excel output or as reports and images
// require_once "../models/DbTable/Checklist.php";
require_once 'modules/Checklist/logger.php';

class processing {

  public function process($list, $name) {
    // this is the driver for processing the request for output generation
    $xrows = $this->getData($name);
  }

  public function getData($name) {
   // given a name get the details from the report and report_row tables
   $checkdb = new Application_Model_DbTable_Checklist();
   $sql = <<<"END"
select r.id, r.name, r.query,
       rr.tabpos, rr.tabname, rr.position, rr.field_label,
       rr.table_name, rr.field_name
  from report e, report_row rr
 where r.name like '{$name}'
 order by rr.tabpos, rr.position
END;
   $xrows = $checkdb->queryRows($sql);
  logit('Xrows: '. print_r($xrows, true));

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