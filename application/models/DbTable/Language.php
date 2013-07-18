<?php

/**
 * This implements the model for Page tags
 * 
 */
class Application_Model_DbTable_Language extends Application_Model_DbTable_Checklist
{
  protected $_name = 'language';

  public function get_language_tag($name)
  {
    //$log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
    //$debug = 1;
    $db = $this->getDb();
    $lang_name = (int)$name;
    $sql = "select * from language where name = ". $lang_name ;
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      throw new Exception("These is no tag for this page.");
    }
    $value = $rows[0]['tag'];
    return $value;
  }
}

