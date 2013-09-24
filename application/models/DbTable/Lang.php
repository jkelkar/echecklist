<?php

/**
 * this implements the lang table - where all the translations are maintained
 */

class Application_Model_DbTable_Lang extends Checklist_Model_Base
{
  protected $_name = 'lang';

  public function init()
  {
    parent::init();
  }

  public function getLang($lname)
  {
    $rows = $this->fetchAll($this->select("row_id, def, {$lname}"));
    if (! $rows)
    {
      throw new Exception("Could not find language data");
    }
    $out = array();
    foreach($rows as $row)
    {
      if ($row['default'])
      {
        $val = $row['default'];
        if ($row[$lang])
        {
          $val = $row[$lang];
          $out["AA{$row['row_id']}"] = val;
        }
      }
    }
    return $out;
  }
}