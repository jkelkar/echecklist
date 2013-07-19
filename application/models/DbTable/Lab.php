<?php

/**
 * This implements the model for lab data
 * 
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Lab extends Application_Model_DbTable_Checklist
{
  protected $_name = 'lab';

  public function get_labs($id)
  {
    // $log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
    $debug = 1;
    $db = $this->getDb();
    $id = (int)$id;
    // Read the following sql with $id == tmpl_head_id
    $sql = "select * from data_item where data_head_id = " . $id ;
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      throw new Exception("These is no data");
    }
    return $rows;
  }

  public function get_a_lab($lab_name='', $lab_num='')
  {
    $db = $this->getDb();
    $namesql = "labname like '{$labname}%' ";
    $numsql  = "lannum = '{$lab_num}' "
    $sql = "select * from lab ";
    if ($lab_name != '') {
      $sql = $sql . $namesql;
    }
    if ($lab_num != '') {
      $sql = $sql . $numsql;
    }
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      throw new Exception("These is no data.");
    }
    return $rows;
  }
}

