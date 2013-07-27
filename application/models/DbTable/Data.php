<?php

/**
 * This implements the model for template data
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Data extends Application_Model_DbTable_Checklist {
  protected $_name = 'data';

  public function get_data($did) {
    // global $log;
    $debug = 1;
    $db = $this->getDb ();
    $did = ( int ) $did;
    // Read the following sql with $id == data_head_id
    $sql = "select * from data_item where data_head_id = " . $did;
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

  public function updateData($data, $did) {
    /**
     * Update user at $id with this data
     * $data is an array with name value pairs
     */
    $did = ( int ) $did;
    foreach ( $data as $n => $v ) {
      logit ( "BEFORE: {$n} ==> {$v}" );
      $this->updateAData ( $did, $n, $v );
    }
  
  }

  public function updateAData($did, $name, $value) {
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
 INSERT INTO data_item (data_head_id, field_name, int_val, text_val,
 string_val, date_val, field_type) values ({$did}, '{$name}', {$ival}, '{$tval}',
 '{$sval}', '{$dval}', '{$ftype}')
 ON DUPLICATE KEY UPDATE
 data_head_id={$did}, field_name='{$name}', int_val={$ival}, text_val='{$tval}',
 string_val='{$sval}', date_val='{$dval}', field_type='{$ftype}'
END;
    $ct = $this->queryRowcount ( $sql );
    /*
     * $db = $this->getDb (); $stmt = $db->query( $sql ); $ct = $stmt->rowCount ();
     */
    return $ct;
  
  }

  public function getTmplHeadId($did) {
    /*
     * Get tmpl_head_id from data_head table matching the $did (data_head_id) provided
     */
    $did = ( int ) $did;
    $sql = "select tmpl_head_id from data_head where id = {$did}";
    $rows = $this->queryRows($sql);
    return $rows[0]['tmpl_row_id'];
  }

  public function update_scores($did) {
    /*
     * Update values for all calculated sub_section scores if they have all the elements answered otherwise leave it at 0
     */
    $did = ( int ) $did;
    // Get tmpl_head_id for this $did
    $tid = $this->getTmplHeadId($did);
    $sql = <<<"END"
select * from tmpl_row tr, data_head dh, data_item di
 where tr.tmpl_head_id = dh.tmpl_head_id
   and dh.id = di.data_head_id
END;
  }
}