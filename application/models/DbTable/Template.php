<?php

/**
 * This implements the model for template data
 */

class Application_Model_DbTable_Template extends Checklist_Model_Base
{
  protected $_name = 'template';

  public function init()
  {
    parent::init();
  }

  public function getByTag($tag)
  {
    //get row based on tag name - get highest version
    $sql = "select * from template where tag = '{$tag}' order by version desc";
    $rows = $this->queryRows($sql);
    if (! $rows)
    {
      throw new Exception("Could not find template row with tag $tag");
    }
    return $rows[0];
  }
}