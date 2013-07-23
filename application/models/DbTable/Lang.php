<?php

/**
 * this implements the lang table - where all the translations are maintained
 */

require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Lang extends Application_Model_DbTable_Checklist
{
  protected $_name = 'lang';

  public function getLang($lname)
  {
    $rows = $this->fetchAll($this->select("row_id, default, {$lname}"));
    if (!$rows) {
      throw new Exception("Could not find language data");
    }
    $out = array();
    foreach($rows as $row) {
      if ($row['default'] != '' && $row['default'] != 0) {
        $val = $row['default'];
        if ($row[$lang] != '' && $row[$lang] != 0) {
        $val = $row[$lang];
        $out["A{$row['row_id']}"] = val;
    }
    return $out; 
  }

  /*public function getUsers() {
    $rows = $this->fetchAll($this->select()
			    ->order('name'));
    return $rows;
    }*/


}

