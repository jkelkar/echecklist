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

  public function getUserByUsername($username) {
    /**
     * get a user from userid
     */
    $sql = "select * from user where userid = '{$username}'";
    //$row = $this->fetchRow("username = '{$username}'" );
    $rows = $this->queryRows($sql);
    if (!$rows) {
      throw new Exception("No matching users found");
    }
    return $rows[0];
  }

  public function getUsersByUsername($username) {
    /**
     * get a user from userid
     */
    $sql = "select * from user where username like '%{$username}%'";
    //$row = $this->fetchRow("username = '{$username}'" );
    $rows = $this->queryRows($sql);
    if (!$rows) {
      throw new Exception("No matching users found");
    }
    return $rows;
  }
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
     * Update user at $id with this data
     * $data is an array with name value pairs
     */
    $this->update($data, "id = " . (int)$id);
  }

  public function delete($id) {
    /**
     * delete user at id
     */
    $this->delete('id = ' . (int)$id);
  }
}

