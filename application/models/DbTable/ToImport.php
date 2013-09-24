<?php

/**
 * This implements the model for template data
 */

class Application_Model_DbTable_ToImport extends Checklist_Model_Base
{
  protected $_name = 'toimport';

  public function init()
  {
    parent::init();
  }

  public function getByOwner($owner_id)
  {
    // get a row by owner_id
    $sql = "select * from toimport where owner_id = {$owner_id}";
    $ct = $this->queryRowcount($sql);
    if ($ct == 0)
    {
      return null;
    }
    $rows = $this->queryRows($sql);
    return $rows[0];
  }
}