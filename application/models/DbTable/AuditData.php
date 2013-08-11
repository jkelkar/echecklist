<?php

/**
 * This implements the model for template data
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_AuditData extends Application_Model_DbTable_Checklist {
  protected $_name = 'audit_data';
  private $debug = 0;
  private $format = 'm/d/Y';
  private $ISOformat = 'Y-m-d';

  public function get_data($did) {
    // $db = $this->getDb ();
    $did = ( int ) $did;
    $sql = "select * from audit_data where audit_id = {$did}";
    //$stmt = $db->query ( $sql );
    //$rows = $stmt->fetchAll ();
    $rows = $this->queryRows($sql);
    if (! $rows) {
      throw new Exception ( "There is no data" );
    }
    $value = array ();
    foreach ( $rows as $row ) {
      $val = '';
      $field_name = $row ['field_name'];
      switch ($row ['field_type']) {
        case 'integer' :
          $val = $row ['int_val'];
          break;
        case 'text' :
          $val = $row ['text_val'];
          break;
        case 'date' :
          $dt = date_parse_from_format ( $this->ISOformat, $row ['date_val'] );
          $date = new DateTime ();
          $date->setDate ( $dt ['year'], $dt ['month'], $dt ['day'] );
          $val = $date->format ( $this->format );
          break;
        case 'bool':
          $val = $row['bool_val'];
          break;
        case 'string' :
        default :
          $val = $row ['string_val'];
      }
      $value [$field_name] = $val;
      // logit ( "{$field_name} ==> {$val}" );
    }
    return $value;
  
  }

  public function getAudit($did) {
    /*
     * Get the audit data exactly as stored in the system
     *   - no reductions
     */
    $did = ( int ) $did;
    $sql = "select * from audit_data where audit_id = {$did}";
    //$stmt = $db->query ( $sql );
    //$rows = $stmt->fetchAll ();
    $rows = $this->queryRows($sql);
    if (! $rows) {
      throw new Exception ( "There is no data" );
    }
    return $rows;
  }
  
  public function getAuditItem($audit_id, $field_name) {
    /*
     * Get the row from auditdata that has this field
     */
    $audit_id = (int) $audit_id;
    $sql = "select * from audit_data where audit_id = {$audit_id} " .
    " and field_name = '{$field_name}' ";
    logit("getAuditItem: {$audit_id} {$field_name} -- {$sql}");
    $rows = $this->queryRows($sql);
    if (! $rows) {
      throw new Exception ("There is no data" );
    }
    return $rows[0];
  }

  public function updateData($data, $did) {
    /**
     * Update user at $id with this data
     * $data is an array with name value pairs
     */
    $did = ( int ) $did;
    foreach ( $data as $n => $v ) {
      logit ( "BEFORE: {$n} ==> {$v}" );
      $this->updateAuditData ( $did, $n, $v );
    }
  
  }

  public function updateAuditData($did, $name, $value) {
    $suff = end ( preg_split ( "/_/", $name ) );
    logit ( "END: {$name} --> {$suff}" );
    $format = 'm/d/Y';
    $ISOformat = 'Y-m-d';
    $ival = 0;
    $tval = '';
    $sval = '';
    $bval = '';
    $ftype = 'string';
    $dval = new DateTime ();
    switch ($suff) {
      // stuff to be ignored
      case 'cb' :
      case 'nextpage':
        break;
      case 'num' :
      case 'd' :
      case 'w' :
      case 'er' :
      case 'int' :
      case 'score' :
        $ival = ( int ) $value;
        $ftype = 'integer';
        break;
      // ---------
      case 'dt' :
      case 'date' :
        $ftype = 'date';
        $dt = date_parse_from_format ( $format, $value );
        $date = new DateTime ();
        $date->setDate ( $dt ['year'], $dt ['month'], $dt ['day'] );
        $dval = $date;
        break;
      // ---------
      case 'comment' :
      case 'note' :
      case 't' :
        $tval = $value;
        $ftype = 'text';
        break;
      // ---------
      case 'nc' :
        $bval = ($value === 'T')? 'T' : 'F';
        $ftype = 'bool';
        break;
      // ---------
      case 'item' :
      case 'person' :
      case 'sig' :
      case 'time' :
      case 'yn' :
      case 'yni' :
      case 'ynp' :
      case 'yna' :
        $sval = $value;
        $ftype = 'string';
        break;
      // ---------
      default :
        $ftype = 'string';
        $sval = $value;
    }
    logit ( "UI: {$did}, '{$name}', {$ival}, '{$tval}', '{$sval}', '{$dval->format($ISOformat)}', '{$bval}', {$ftype}'" );
    $sql = <<<"END"
 INSERT INTO audit_data (audit_id, field_name, int_val, text_val,
 string_val, date_val, bool_val, field_type) values ({$did}, '{$name}', {$ival}, '{$tval}',
 '{$sval}', '{$bval}', '{$dval->format($ISOformat)}', '{$ftype}')
 ON DUPLICATE KEY UPDATE
 audit_id={$did}, field_name='{$name}', int_val={$ival}, text_val='{$tval}',
 string_val='{$sval}', date_val='{$dval->format($ISOformat)}', bool_val='{$bval}',
 field_type='{$ftype}'
END;
    $ct = $this->queryRowcount ( $sql );
    return $ct;
  
  }

  public function getTemplateId($did) {
    /*
     * Get template_id from audit table matching the $did (audit_id) provided
     */
    $did = ( int ) $did;
    $sql = "select template_id from audit where id = {$did}";
    $rows = $this->queryRows ( $sql );
    if (! $rows) {
      throw new Exception ( "Cannot find Audit data for id: {$audit_id}" );
    }
    return $rows [0] ['template_id'];
  
  }

  public function update_scores($did) {
    /*
     * Update values for all calculated sub_section scores if they have all the elements answered otherwise leave it at 0
     */
    $did = ( int ) $did;
    // Get tmpl_head_id for this $did
    $tid = $this->getTemplateId ( $did );
    $sql = <<<"END"
select * from template_row tr, audit au, audit_data ad
 where tr.template_id = au.template_id
   and au.id = ad.audit_id
END;
  
  }
  
}