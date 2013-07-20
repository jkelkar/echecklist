<?php

/**
 * this implements the user table
 */

require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_User extends Application_Model_DbTable_Checklist
{
  protected $_name = 'user';

  public function getUser($id)
  {
    $id = (int)$id;
    $row = $this->fetchRow('id = ' . $id);
    if (!$row) {
      throw new Exception("Could not find row $id");
    }
    return $row->toArray();
  }

  public function getUsers() {
    $rows = $this->fetchAll($this->select()
			    ->order('name'));
    return $rows;
  }

  public function newUser($data) {
    /**
     * data is an array with name value pairs
     */
    $this->insert(data);
    $newid = $this->getAdapter()->lastInsertId();
    return $newid;
  }

  public function updateUser($data, $id) {
    /**
     * Update user at $id with this data
     * $data is an array with name value pairs
     */
    $this->update($data, "id = " . (int)$id);
  }
   
  public function delete($ind) {
    /**
     * delete user at id
     */
    $this->delete('id = ' . (int)$id);
  }
}

