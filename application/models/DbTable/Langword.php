<?php

/**
 * This implements the model for template data
 *
 */
//require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Langword extends Checklist_Model_Base
{
  // protected $_name = 'lang_word';
  public function init()
  {
    parent::init();
  }

  public function getWordsOrig($tag)
  {
    $debug = 0;
    $db = $this->getDb();
    // Read the following sql with $id == tmpl_head_id
    $sql = "select * from lang_word where tag = '{$tag}'";
    $stmt = $db->query($sql);
    $rows = $stmt->fetchAll();
    if (! $rows)
    {
      throw new Exception("These is no data.");
    }
    $tword = array();
    foreach($rows as $row)
    {
      $val = '';
      $tword[$row['word']] = $row['trans_word'];
    }
    return $tword;
  }

  public function getWords($langtag)
  {
    $sql = "select def, {$langtag} from lang_word";
    $this->log->logit("SQL: {$sql}");
    $rows = $this->queryRows($sql);
    if (! $rows)
    {
      throw new Exception("Could not find language short data");
    }
    $out = array();
    foreach($rows as $row)
    {
      if ($row['def'])
      {
        $val = $row['def'];
        if ($row["{$langtag}"])
        {
          $val = $row["{$langtag}"];
          $out["{$row['def']}"] = $val;
        }
      }
    }
    return $out;
  }
}

