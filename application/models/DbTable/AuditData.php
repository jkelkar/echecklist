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

  public function getData($did, $page_id) {
    // $db = $this->getDb ();
    $did = ( int ) $did;
    $page_id = (int) $page_id;
    $sql = "select * from audit_data where audit_id = {$did} and ".
      " page_id = {$page_id}";
    //$stmt = $db->query ( $sql );
    //$rows = $stmt->fetchAll ();
    $rows = $this->queryRows($sql);
    /*if (! $rows) {
      throw new Exception ( "There is no data" );
      }*/
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
    //logit("getAuditItem: {$audit_id} {$field_name} -- {$sql}");
    $rows = $this->queryRows($sql);
    if (! $rows) {
      throw new Exception ("There is no data" );
    }
    return $rows[0];
  }

  public function updateData($data, $did, $page_id) {
    /**
     * Update user at $id with this data
     * $data is an array with name value pairs
     */
    $did = ( int ) $did;
    $page_id = (int) $page_id;
    foreach ( $data as $n => $v ) {
      //logit ( "BEFORE: {$n} ==> {$v}" );
      $this->updateAuditData ( $did, $n, $v, $page_id );
    }
    $this->updateFinalScore($did, 0);
  
  }

  public function updateAuditData($did, $name, $value, $page_id) {
    $suff = end ( preg_split ( "/_/", $name ) );
    //logit ( "END: {$name} --> {$suff}" );
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
      // these are considered to be integers
    case 'num' :
    case 'ct':
    case 'd' :
    case 'w' :
    case 'er' :
    case 'int' :
    case 'score' :
    case 'total':
      $ival = ( int ) $value;
      $ftype = 'integer';
      break;
      // --------- dates
    case 'dt' :
    case 'date' :
      $ftype = 'date';
      $dt = date_parse_from_format ( $format, $value );
      $date = new DateTime ();
      $date->setDate ( $dt ['year'], $dt ['month'], $dt ['day'] );
      $dval = $date;
      break;
      // --------- TEXT - multiple lines
    case 'comment' :
    case 'note' :
    case 't' :
      $tval = $value;
      $ftype = 'text';
      break;
      // --------- Boolean values T/F
    case 'nc' :
      $bval = ($value === 'T')? 'T' : 'F';
      $ftype = 'bool';
      break;
      // --------- String values - default is also string type
    case 'item' :
    case 'person' :
    case 'sig' :
    case 'time' :
    case 'yn' :
    case 'yni' :
    case 'ynp' :
    case 'yna' :
    case 'n':
    case 'y':
        $sval = $value;
        $ftype = 'string';
        break;
      // ---------
      default :
        $ftype = 'string';
        $sval = $value;
    }
    //logit ( "AD: {$did}, '{$name}', {$ival}, '{$tval}', '{$sval}', 
    //'{$dval->format($ISOformat)}', '{$bval}', {$ftype}', {$page_id}" );
    $sql = <<<"END"
INSERT INTO audit_data (audit_id, field_name, int_val, text_val,
string_val, date_val, bool_val, field_type, page_id) 
values 
({$did}, '{$name}', {$ival}, '{$tval}', '{$sval}', '{$dval->format($ISOformat)}', 
'{$bval}', '{$ftype}', {$page_id})
ON DUPLICATE KEY UPDATE
audit_id={$did}, field_name='{$name}', int_val={$ival}, text_val='{$tval}',
string_val='{$sval}', date_val='{$dval->format($ISOformat)}', bool_val='{$bval}',
field_type='{$ftype}', page_id={$page_id} 
END;

    $ct = $this->queryRowcount ( $sql );
    return $ct;
  
  }

  public function updateFinalScore($did, $page_id) {
    /*
     * Each time the data is saved, we need to compute the 
     * current final score - at the end it will be up to date!
     */
    // get all scores in the audit
    $sql = "select * from audit_data where audit_id = {$did} ".
      " and field_name like 's___total' ";
    $rows = $this->queryRows($sql);
    if (count($rows) > 0) {
      $final_score = 0;
      
      foreach($rows as $row) {
        if ($row['field_name'] != 'final_score') {
          // Since integer values are stored in audit_data.int_val
          $final_score += $row['int_val'];
        }
      }
      // calculate if it is a minimum of 55% or 143 points
      $this->updateAuditData($did, 'final_score', $final_score, $page_id);
      $final_y = '';
      $final_n = '';
      if ($final_score > 142) {
        $final_y = 'Y';
      } else {
        $final_n = 'N';
      }
      $this->updateAuditData($did, 'final_y', $final_y, $page_id);
      $this->updateAuditData($did, 'final_n', $final_n, $page_id);
    }
    // update the totals for BAT & TB
    $sql = "select * from audit_data where audit_id = {$did} ".
      " and field_name like '%_ct' ";
    $rows = $this->queryRows($sql);
    if (count($rows) > 0) {
      $final_y_ct = 0;
      $final_n_ct = 0;
      $final_na_ct = 0;
      
      foreach($rows as $row) {
        $rn = $row['field_name'];
        if (substr($rn, 0, 5) != 'final') {
          // Since integer values are stored in audit_data.int_val
          $name = substr($rn, 4);
          switch($name) {
          case 'y_ct': $final_y_ct += $row['int_val'];break;
          case 'n_ct': $final_n_ct += $row['int_val'];break;
          case 'na_ct':$final_na_ct += $row['int_val'];break;
          default:
          }
        }
      }
      // calculate if it is a minimum of 55% or 143 points
      $this->updateAuditData($did, 'final_y_ct', $final_y_ct, $page_id);
      $this->updateAuditData($did, 'final_n_ct', $final_n_ct, $page_id);
      $this->updateAuditData($did, 'final_na_ct', $final_na_ct, $page_id);
    }
    
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