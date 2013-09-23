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

  public function getOwnersByAuditId($audit_id) {
    // get all owners by audit id
    $audit_id = (int) $audit_id;
    $sql = <<<"SQL"
select user.* from `user`, audit_owner ao
 where ao.audit_id = {$audit_id} and ao.owner = user.id
SQL;
    $rows = $this->queryRows($sql);
    return $rows;
  }
}

