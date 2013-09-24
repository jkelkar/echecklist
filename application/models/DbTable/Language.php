<?php

/**
 * This implements the model for Page tags
 *
 */

class Application_Model_DbTable_Language extends Checklist_Model_Base
{
  protected $_name = 'language';

  public function init()
  {
    parent::init();
  }

  public function get_language_tag($name)
  {
    $lang_name = (int) $name;
    $sql = "select * from language where name = '{$lang_name}'";
    $rows = $this->queryRows($sql); // db->query($sql);
    if (! $rows)
    {
      throw new Exception("This language is unavailable.");
    }
    $value = $rows[0]['tag'];
    return $value;
  }

  public function getLanguages()
  {
    /*
     * Return all languages and their tags
     */
    $sql = "select * from language";
    $rows = $this->queryRows($sql);
    if (! $rows)
    {
      throw new Exception("This language is unavailable.");
    }
    return $rows;
  }
}

