<?php

/**
 * This implements the model for template data
 * 
 */
//require_once 'modules/KLogger.php';
// $log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
class Application_Model_DbTable_Data extends Application_Model_DbTable_Checklist
{
  protected $_name = 'data';

  public function get_data($id)
  {
    //global $log;
    $log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
    $debug = 1;
    $db = $this->getDb();
    $id = (int)$id;
    // Read the following sql with $id == tmpl_head_id
    $sql = "select * from data_item where data_head_id = " . $id ;
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      throw new Exception("These is no data for this audit.");
    }
    $value = array();
    foreach($rows as $row) {
      $val = '';
      $field_name = $row['field_name'];
      switch($row['field_type']) {
      case 'integer':
        $val = $row['int_val']; 
        break;
      case 'text':
        $val = $row['text_val'];
        break;
      case 'date':
        $val = $row['date_val']; 
        break;
      case 'string':
      default:
        $val = $row['string_val'];        
      }
      $value[$field_name] = $val;
      $log->LogInfo("{$field_name} ==> {$val}");
    }
    return $value;
  }
}

