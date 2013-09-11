<?php

/**
 * This implements the model for lab data
 *
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_AuditOwner extends Application_Model_DbTable_Checklist
{
  protected $_name = 'audit_owner';

  public function isOwned($audit_id, $user_id) {
    // check if this audit is owned by the user
    $audit_id = (int) $audit_id;
    $user_id = (int) $user_id;
    $sql = "select * from audit_owner where audit_id = {$audit_id} and owner = {$user_id}";
    $ct = $this->queryRowcount($sql);
    return ($ct > 0) ? true: false;
  }
}

