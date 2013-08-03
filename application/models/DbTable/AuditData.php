<?php

/**
 * This implements the model for template data
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_AuditData extends Application_Model_DbTable_Checklist {
  protected $_name = 'audit_data';

  public function get_data($did) {
    $debug = 1;
    $db = $this->getDb ();
    $did = ( int ) $did;
    $sql = "select * from audit_data where audit_id = {$did}";
    $stmt = $db->query ( $sql );
    $rows = $stmt->fetchAll ();
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
          $val = $row ['date_val'];
          break;
        case 'string' :
        default :
          $val = $row ['string_val'];
      }
      $value [$field_name] = $val;
      logit ( "{$field_name} ==> {$val}" );
    }
    return $value;
  
  }

  /*public function updateData($data, $did) {
    / **
     * Update user at $id with this data
     * $data is an array with name value pairs
     * /
    $did = ( int ) $did;
    foreach ( $data as $n => $v ) {
      logit ( "BEFORE: {$n} ==> {$v}" );
      $this->updateAData ( $did, $n, $v );
    }
  
  } */

  public function updateAuditData($did, $name, $value) {
    $suff = end ( preg_split ( "/_/", $name ) );
    logit ( "END: {$name} --> {$suff}" );
    $ival = 0;
    $dval = '';
    $tval = '';
    $sval = '';
    $ftype = 'string';
    switch ($suff) {
      case 'num' :
      case 'd' :
      case 'w' :
      case 'er' :
      case 'score' :
        $ival = ( int ) $value;
        $ftype = 'integer';
        break;
      case 'dt' :
        $ftype = 'date';
        $dval = $value;
        break;
      case 'comment' :
        $tval = $value;
        $ftype = 'text';
        break;
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
      default :
        $ftype = 'other';
    }
    logit ( "UI: {$did}, '{$name}', {$ival}, '{$tval}', '{$sval}', '{$dval}', '{$ftype}'" );
    $sql = <<<"END"
 INSERT INTO audit_data (audit_id, field_name, int_val, text_val,
 string_val, date_val, field_type) values ({$did}, '{$name}', {$ival}, '{$tval}',
 '{$sval}', '{$dval}', '{$ftype}')
 ON DUPLICATE KEY UPDATE
 audit_id={$did}, field_name='{$name}', int_val={$ival}, text_val='{$tval}',
 string_val='{$sval}', date_val='{$dval}', field_type='{$ftype}'
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
    $rows = $this->queryRows($sql);
    return $rows[0]['template_id'];
  }

  public function update_scores($did) {
    /*
     * Update values for all calculated sub_section scores if they have all the elements answered otherwise leave it at 0
     */
    $did = ( int ) $did;
    // Get tmpl_head_id for this $did
    $tid = $this->getTemplateId($did);
    $sql = <<<"END"
select * from template_row tr, audit au, audit_data ad
 where tr.template_id = au.template_id
   and au.id = ad.audit_id
END;
  }
}