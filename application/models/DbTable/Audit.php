<?php

/**
 * This implements the model for template data
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Audit extends Application_Model_DbTable_Checklist {
  protected $_name = 'audit';
  private $debug = 0;
  private $format = 'Y-m-d H:i:s';

  public function UpdateTS($id) {
    $id - (int) $id;
    $dt = new DateTime();
    logit('TS: '. $dt->getTimestamp());
    $fdt = $dt->format($this->format);
    $data = array(
                  'updated_at' => "${fdt}"
                  );
    logit("AUDIT_DT: {$id} " . print_r($data, true));
    $this->update($data, "id = {$id}");
  }
  
  public function getAudit($id) {
    // get audit from this audit id
    $id = (int) $id;
    $sql = "select tt.tag, a.id, a.created_at, a.updated_at, l.id lab_id, l.labname ".
      " from audit a, template_type tt, lab l " .
      " where a.id = {$id} and a.template_id = tt.id " .
      " and l.id = a.lab_id";
    $rows = $this->queryRows($sql);
    if (!$rows) {
      throw new Exception("Could not find the audit.");
    }
    return $rows[0];
  }

  public function getAudits($id) {
    /*
     * Get all incomplete audits for this user
     */
    $sql = <<<"END"
SELECT a.id audit_id, a.updated_at, a.lab_id, a.status, tt.tag, l.labname, l.labnum 
  FROM audit a, audit_owner ao, template_type tt, lab l 
 WHERE a.id = ao.audit_id 
   and a.status = 'INCOMPLETE' 
   and ao.owner = {$id}
   and tt.id = a.template_id 
   and l.id = a.lab_id
END;

    $rows = $this->queryRows($sql);
    return $rows;
  }

}