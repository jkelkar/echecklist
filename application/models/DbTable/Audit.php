<?php

/**
 * This implements the model for audit table
 */

class Application_Model_DbTable_Audit extends Checklist_Model_Base {
  protected $_name = 'audit';
  private $debug = 0;
  private $format = 'Y-m-d H:i:s';
  private $ISOformat = 'Y-m-d';
  public $log;

  public function init()
  {
    parent::init();
  }

  public function UpdateTS_SLMTA($id, $sstatus) {
    $id = (int) $id;
    $dt = new DateTime();
    $this->log->logit('TS: '. $dt->getTimestamp());
    $fdt = $dt->format($this->format);
    $data = array(
                  'updated_at' => $fdt,
                  'slmta_status' => $sstatus
                  );
    $this->update($data, "id = {$id}");
  }

  public function get($id) {
    // get audit from this audit id
    $id = (int) $id;
    $sql = "select * from audit where id = {$id}";
    $rows = $this->queryRows($sql);
    if (!$rows) {
      throw new Exception("Could not find the audit.");
    }
    return $rows[0];
  }

  public function getAudit($id) {
    // get audit from this audit id
    global $userid;
    if (! $userid) $userid = 99999;
    $id = (int) $id;
    $sql = <<<"END"
 select a.id audit_id, a.end_date, a.cohort_id, a.status,
        a.slmta_type, l.id lab_id, a.audit_type tag,
        l.labname, l.labnum, l.country, l.lablevel, l.labaffil,
        ao.owner
   from lab l, audit a left join audit_owner ao on
        (ao.audit_id = a.id and ao.owner = {$userid})
  where a.id = {$id} and l.id = a.lab_id
END;
    $rows = $this->queryRows($sql);
    return ($rows) ? $rows[0] : null;
  }

  public function getIncompleteAudits($id) {
    /*
     * Get all incomplete audits for this user
     */
    $sql = <<<"END"
select a.id audit_id, a.end_date, a.cohort_id, a.status, a.audit_type,
       a.slipta_official, a.slmta_type, l.id lab_id,
       l.labname, l.labnum, l.country, l.lablevel, l.labaffil, ao.owner
  from lab l, audit a, audit_owner ao
 where a.id = ao.audit_id and ao.owner = {$id}
   and l.id = a.lab_id and a.status = 'INCOMPLETE'
END;

    $rows = $this->queryRows($sql);
    return $rows;
  }

  public function selectAudits($data) {
    global $userid;
    if (! $userid) $userid = 99999;

    foreach($data as $n => $v) {
      $this->log->logit("BEG $n -> $v " . print_r($v, true));
      if (is_string($v) && $v == '-') {
        $this->log->logit('unset');
        unset($data[$n]);
        continue;
      }
      if (is_array($v) && count($v) == 1 && $v[0] == '-') {
        $this->log->logit('unset');
        unset($data[$n]);
        continue;
      }
    }
    $sql = <<<"END"
select a.id audit_id, a.end_date, a.cohort_id, a.status, a.slipta_official,
       a.slmta_type, a.audit_type, l.id labid, a.audit_type tag,
       l.labname, l.labnum, l.country, l.lablevel, l.labaffil, ao.owner
  from lab l, audit a left join audit_owner ao on
       (ao.owner = {$userid} and ao.audit_id = a.id)
 where l.id = a.lab_id
END;
    foreach($data as $a => $b) {
      if (! is_null($b) and $b != '') {
        //$this->log->logit("IN: {$a} = {$b} " . print_r($b, true));
          $this->log->logit("LIST: {$a} -> {$b}", $this->_mkList($b));
        switch ($a) {
          case 'country' :
            $sql .= " and l.country " . $this->_mkList($b);
            break;
          case 'lablevel' :
            $sql .= " and l.lablevel " . $this->_mkList($b);
            break;
          case 'labaffil' :
            $sql .= " and l.labaffil " . $this->_mkList($b);
            break;
          case 'labnum' :
            if ($b != '') {
              $sql .= " and l.labnum = '$b' ";
            }
            break;
          case 'labname' :
            if ($b != '') {
              $sql .= " and l.labname = '$b' ";
            }
            break;
          case 'audit_type' :
            $sql .= " and a.audit_type " . $this->_mkList($b);
            break;
          case 'audit_status' :
            $sql .= " and a.status " . $this->_mkList($b);
            break;
          case 'slmta_type' :
            $sql .= " and a.slmta_type " . $this->_mkList($b);
            break;
          case 'cohortid' :
            $sql .= " and a.cohort_id " . $this->_mkList($b);
            break;
          case 'stdate' :
            if ($b != '') {
              $dval = Checklist_Modules_Datefns::convert_ISO($b);
              $this->log->logit("Date: {$dval->format($this->ISOformat)}");
              $sql .= " and a.end_date >= '{$dval->format($this->ISOformat)}' ";
            }
            break;
          case 'enddate' :
            if ($b != '') {
              $dval = convert_ISO($b);
              $this->log->logit("Date: {$dval->format($this->ISOformat)}");
              $sql .= " and a.end_date <= '{$dval->format($this->ISOformat)}' ";
            }
            break;
          default :
        }
      }
    }
    $this->log->logit("SQL: {$sql}");
    $sql .= " order by a.end_date desc, a.audit_type";
    $this->log->logit("CSQL: {$sql}");
    $rows = $this->queryRows($sql);
    return $rows;
  }

  public function getDistinctCohorts() {
    $sql = "select distinct cohort_id from audit";
    $rows = $this->queryRows($sql);
    if (!$rows) {
      throw new Exception("No cohortids found");
    }
    return $rows;
  }

  public function getAuditTypes() {
    $sql = "select distinct tag from template_type";
    $rows = $this->queryRows($sql);
    if (!$rows) {
      throw new Exception("No audit types found");
    }
    return $rows;
  }

  public function deleteAudit($audit_id) {
    // delete audit with id = $audi_id
    $audit_id = (int) $audit_id;
    $sql = "delete from audit where id = {$audit_id}";
    $this->execute($sql);
  }

  public function moveStatus($audit_id, $status) {
    /// change the status of audit_id audit to status
    $audit_id = (int) $audit_id;
    $sql = "update audit set status='{$status}' where id = {$audit_id}";
    $this->execute($sql);
    return;
  }

  public function getIncompleteAuditsByLabid($labid) {
    // get only incomplate audits with this labid
    $labid = (int) $labid;
    $sql = "select * from audit where status = 'INCOMPLETE' and lab_id = {$labid}";
    $rows = $this->queryRows($sql);
    return $rows;
  }

}