<?php

/**
 * This implements the model for template data
 * 
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Langword extends Application_Model_DbTable_Checklist
{
  protected $_name = 'data';

  public function get_words($tag)
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
}

