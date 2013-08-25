<?php

/**
 * This implements the model for template data
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_ToImport extends Application_Model_DbTable_Checklist {
  protected $_name = 'toimport';

    public function getByOwner($owner_id) {
      // get a row by owner_id
      $sql = "select * from toimport where owner_id = {$owner_id}";
      $ct = $this->queryRowcount($sql);
      if ($ct == 0) {
          return null;
      }
      $rows = $this->queryRows($sql);
      return $rows[0];
    }
}