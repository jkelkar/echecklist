<?php

/**
 * This implements the model for template data
 *
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Langword extends Application_Model_DbTable_Checklist
{
  protected $_name = 'lang_word';

  public function getWordsOrig($tag)
  {
    $debug = 1;
    $db = $this->getDb();
    // Read the following sql with $id == tmpl_head_id
    $sql = "select * from lang_word where tag = '{$tag}'" ;
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      throw new Exception("These is no data.");
    }
    $tword = array();
    foreach($rows as $row) {
      $val = '';
      $tword[$row['word']] = $row['trans_word'];
    }
    return $tword;
  }
  
  public function getWords($lname) {
    $sql = "select def, {$lname} from lang_word";
    logit("SQL: {$sql}");
    $rows = $this->queryRows($sql);
    //$rows = $this->fetchAll ( $this->select ( "default, {$lname}" ) );
    if (! $rows) {
      throw new Exception ( "Could not find language short data" );
    }
    $out = array ();
    foreach ( $rows as $row ) {
      if ($row ['def']) {
        $val = $row['def'];
        if ($row[$lname]) {
          $val = $row[$lname];
          $out["{$row['def']}"] = $val;
        }
      }
    }
    return $out;
  
  }
}

