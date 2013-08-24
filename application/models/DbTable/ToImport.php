<?php

/**
 * This implements the model for template data
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_ToImport extends Application_Model_DbTable_Checklist {
  protected $_name = 'toimport';

  public function insertData($data) {
    /**
     * data is an array with name value pairs
     */
    $this->insert($data);
    $newid = $this->getAdapter()->lastInsertId();
    return $newid;
  }

  public function updateData($data, $id) {
    /**
     * Update row with at $id with this data
     * $data is an array with name value pairs
     */
    $this->update($data, "id = " . (int)$id);
  }

  public function delete($id) {
    /**
     * delete row at id
     */
    $this->delete('id = ' . (int)$id);
  }

    public function getByOwner($owner_id) {
      // get a row by owner_id
      $sql = "select * from toimport where owner_id = {$owner_id}";
      $ct = $this->queryRowcount($sql);
      if ($ct == 0) {
          return null;
      }
      $row = $this->fetchRow("owner_id = {$owner_id}");
      return $row;
    }
}