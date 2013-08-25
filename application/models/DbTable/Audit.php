<?php

/**
 * This implements the model for template data
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Audit extends Application_Model_DbTable_Checklist {
  protected $_name = 'audit';
  private $debug = 0;
  private $format = 'Y-m-d H:i:s';

  public function UpdateTS_SLMTA($id, $sstatis) {
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
    $id = (int) $id;
    $sql = "select a.id audit_id, a.end_date, a.cohort_id, a.status, ".
      " a.slmta_type, l.id lab_id, ".
      " l.labname, l.labnum, l.country, l.lablevel, l.labaffil, " .
      " tt.tag from audit a, template_type tt, lab l " .
      " where a.id = {$id} and a.template_id = tt.id " .
      " and l.id = a.lab_id";
    $rows = $this->queryRows($sql);
    if (!$rows) {
      throw new Exception("Could not find the audit.");
    }
    return $rows[0];
  }

  public function getIncompleteAudits($id) {
    /*
     * Get all incomplete audits for this user
     */
    $sql = <<<"END"
select a.id audit_id, a.end_date, a.cohort_id, a.status,
       a.slmta_type, l.id lab_id,
       l.labname, l.labnum, l.country, l.lablevel, l.labaffil,
       tt.tag from audit a, template_type tt, lab l, audit_owner ao
      where a.template_id = tt.id and a.id = ao.audit_id and ao.owner ={$id}
   and l.id = a.lab_id and a.status = 'INCOMPLETE'
END;

    $rows = $this->queryRows($sql);
    return $rows;
  }

  private function _mkList($data) {
    $out = '';
    // if (count($data) == 0) {
    //  return
    switch(count($data)) {
    case 0 :
      //logit("0: {$data} --". print_r($data, true));
      break;
    case 1:
      //logit("A: = '{$data[0]}' ");
      return "= '{$data[0]}' ";
      break;
    default:
      foreach($data as $d) {
        if ($out != '') $out .= ',';
        if (is_string($d))
          $out .= "'{$d}'" ;
      }
      //logit("A: = in ({$out}) ");
      return "in ({$out})";
    }
  }

  public function selectAudits($data) {

    $sql = "select a.id audit_id, a.end_date, a.cohort_id, a.status, ".
      " a.slmta_type, l.id labid, ".
      " l.labname, l.labnum, l.country, l.lablevel, l.labaffil, " .
      " tt.tag from audit a, template_type tt, lab l " .
      " where l.id = a.lab_id and tt.id = a.template_id";
    foreach($data as $a => $b) {
      if (!is_null($b) and $b != '') {
        //logit("{$a} = {$b} ". print_r($b, true));
        //logit("LIST: ", $this->_mkList($b));
        switch($a) {
        case 'country':
          $sql .= " and l.country ". $this->_mkList($b) ;
          break;
        case 'lablevel':
          $sql .= " and l.lablevel ". $this->_mkList($b) ;
          break;
        case 'labaffil':
          $sql .= " and l.labaffil ". $this->_mkList($b)  ;
          break;
        case 'slmta':
          $sql .= " and a.slmta_type ". $this->_mkList($b) ;
          break;
        case 'cohortid':
          $sql .= " and a.cohort_id ". $this->_mkList($b) ;
          break;
        case 'stdate':
          if ($b != '') {
            $sql .= " and a.end_date >= '{$stdate}' ";
          }
          break;
        case 'enddate':
          if ($b != '') {
            $sql .= " and a.end_date <= '{$enddate}' ";
          }
          break;
        case 'labnum':
          if ($b != '') {
            $sql .= " and l.labnum = '$b' ";
          }
        default:
        }
      }
    }
    //logit("SQL: {$sql}");
    $sql .= " order by a.end_date desc";
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

  public function insertData($data) {
    /**
     * Create a new audit
     * data is an array with name value pairs
     */
    $this->insert($data);
    $newid = $this->getAdapter()->lastInsertId();
    return $newid;
  }

}