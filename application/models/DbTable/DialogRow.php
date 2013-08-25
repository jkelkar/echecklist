<?php

/**
 * This implements the model for Page tags
 *
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_DialogRow extends Application_Model_DbTable_Checklist
{
  protected $_name = 'dialog_row';
  protected $_primary = 'id';

  public function getDialogRows($dialog_name) {
    /**
     * Get all rows for this dialog
     */
    //$this->setMetadataCacheInClass(false);
    // First get dialog_id from dialog_name
    $sql = "select dialog_id from dialog_row where field_name='{$dialog_name}'";
    $rows = $this->queryRows($sql);
    if (!$rows) {
      throw new Exception("Could not find dialog.");
    }
    $dialog_id = $rows[0]['dialog_id'];
    $dialog_id = (int)$dialog_id;
    $sql = "select * from dialog_row where dialog_id = ". $dialog_id .
      " order by position";
    $rows = $this->queryRows($sql);

    if (!$rows) {
      throw new Exception("Could not find any dialog rows.");
    }
    return $rows;
  }

}

