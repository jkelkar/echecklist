<?php

/**
 * This implements the model for template data
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Template extends Application_Model_DbTable_Checklist {
  protected $_name = 'template';

  /*public function get($id) {
    /// get row by id
    $id = (int) $id;
    $sql = "select * from template where id = {$id}";
    logit('TMPL: '. $sql);
    $rows = $this->queryRows($sql);
    if (! $rows) {
      throw new Exception("Could not find template row $id");
    }
    return $rows[0];
  }*/
}