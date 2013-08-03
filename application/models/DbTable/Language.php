<?php

/**
 * This implements the model for Page tags
 *
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Language extends Application_Model_DbTable_Checklist
{
  protected $_name = 'language';

  public function get_language_tag($name) {
    $lang_name = ( int ) $name;
    $sql = "select * from language where name = '{$lang_name}'";
    $rows = $this->queryRows ( $sql ); // db->query($sql);
    if (! $rows) {
      throw new Exception ( "This language is unavailable." );
    }
    $value = $rows [0] ['tag'];
    return $value;
  }
  
  public function getLanguages() {
    /*
     * Return all languages and their tags
     */
    $sql = "select * from language";
    $rows = $this->queryRows ( $sql );
    if (! $rows) {
      throw new Exception ( "This language is unavailable." );
    }
    return $rows;
  }
}

