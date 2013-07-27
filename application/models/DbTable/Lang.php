<?php

/**
 * this implements the lang table - where all the translations are maintained
 */

require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Lang extends Application_Model_DbTable_Checklist {
  protected $_name = 'lang';

  public function getLang($lname) {
    $rows = $this->fetchAll ( $this->select ( "row_id, def, {$lname}" ) );
    if (! $rows) {
      throw new Exception ( "Could not find language data" );
    }
    $out = array ();
    foreach ( $rows as $row ) {
      if ($row ['default']) {
        $val = $row ['default'];
        if ($row[$lang]) {
          $val = $row [$lang];
          $out ["A{$row['row_id']}"] = val;
        }
      }
    }
    return $out;
  
  }
}