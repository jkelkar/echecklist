<?php

/**
 * This implements the model for template data
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Audit extends Application_Model_DbTable_Checklist {
  protected $_name = 'audit';
  private $debug = 0;
  private $format = 'Y-m-d H:i:s';

  public function UpdateTS_SLMTA($id, $sstatus) {
    $id = (int) $id;
    $dt = new DateTime();
    logit('TS: '. $dt->getTimestamp());
    $fdt = $dt->format($this->format);
    $data = array(
                  'updated_at' => $fdt,
                  'slmta_status' => $sstatus
                  );
    logit("AUDIT_DT: {$id} " . print_r($data, true));
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
    /*if (!$rows) {
      throw new Exception("Could not find the audit.");
    }*/
    return $rows[0];
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

  /*public function _mkList($data) {
    logit("MKL: {$data} " . print_r($data, true));
    $out = '';
    // if (count($data) == 0) {
    //  return
    if (is_string($data)) {
      // logit('STR');
      $out =  "= '{$data}' ";
      logit("MKLv: {$out}");
      return $out;
    } else {
      // logit('ARR');
      switch (count($data)) {
        case 0 :
          //logit("0: {$data} --". print_r($data, true));
          break;
        case 1 :
          //logit("A: = '{$data[0]}' ");
          //if ($data[0] == '-')
          //  return "= '{$data[0]}' ";
          if (is_string($data[0])) {
            $out .= "= '{$data[0]}'";
          } else {
            $out .= "= {$data[0]}";
          }
          logit("MKL: {$out}");
          return $out;
          break;
        default :
          foreach($data as $d) {
            if ($out != '')
              $out .= ',';
            if (is_string($d)) {
              $out .= "'{$d}'";
            } else {
              $out .= "{$d}";
            }
          }
          logit("MKL2: {$out}");
          return "in ({$out})";
      }
    }
  }*/

  public function selectAudits($data) {
    global $userid;
    if (! $userid) $userid = 99999;
    // logit('IN top: ' . print_r($data, true));

    foreach($data as $n => $v) {
      logit("BEG $n -> $v " . print_r($v, true));
      if (is_string($v) && $v == '-') {
        logit('unset');
        unset($data[$n]);
        continue;
      }
      if (is_array($v) && count($v) == 1 && $v[0] == '-') {
        logit('unset');
        unset($data[$n]);
        continue;
      }
      logit("END {$n} ". print_r($data, true));
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
        //logit("IN: {$a} = {$b} " . print_r($b, true));
          logit("LIST: {$a} -> {$b}", $this->_mkList($b));
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
              $sql .= " and a.end_date >= '{$stdate}' ";
            }
            break;
          case 'enddate' :
            if ($b != '') {
              $sql .= " and a.end_date <= '{$enddate}' ";
            }
            break;
          default :
        }
      }
    }
    // logit("SQL: {$sql}");
    $sql .= " order by a.end_date desc, a.audit_type";
    logit("CSQL: {$sql}");
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

  /*public function insertData($data) {
    / **
     * Create a new audit
     * data is an array with name value pairs
     * /
    $this->insert($data);
    $newid = $this->getAdapter()->lastInsertId();
    return $newid;
  }*/

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