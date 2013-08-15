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

  public function getDialogRows($dialog_id) {
    /**
     * Get all rows for this dialog
     */
    $this->setMetadataCacheInClass(false);
    $dialog_id = (int)$dialog_id;
    $sql = "select * from dialog_row where dialog_id = ". $dialog_id ;;
    $rows = $this->queryRows($sql);
    
    if (!$rows) {
      throw new Exception("Could not find any dialog rows.");
    }
    return $rows;
  }

  public function getDialog($dialog_id) {
    // Get the dialog header
    $db = $this->getDb();
    $dialog_id = (int)$dialog_id;
    //logit("Dialog: {$dialog_id}");
    $sql = "select * from dialog where dialog_id = ". $dialog_id ;
    //logit("getDialog: ". $sql);
    $rows = $this->queryRows($sql);
    
    if (!$rows) {
      throw new Exception("Could not find dialog.");
    }
    return $rows[0];
  }

  public function getFullDialog($dialog_name)
  {
    /**
     * get the dialog and its rows
     */
    $sql = "select dialog_id from dialog where name='{$dialog_name}'";
    $rows = $this->queryRows($sql);
    if (!$rows) {
      throw new Exception("Could not find dialog.");
    }
    $dialog_id = $rows[0]['dialog_id'];
    $row = $this->getDialog($dialog_id);
    $rows = $this->getDialogRows($dialog_id);
    return array('dialog' => $row,
                 'dialog_rows' => $rows);
  }
}

