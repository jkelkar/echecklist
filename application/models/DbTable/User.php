<?php

/**
 * this implements the user table
 */
class Application_Model_DbTable_User extends Checklist_Model_Base
{
  protected $_name = 'user';

  public function init()
  {
    parent::init();
  }

  public function getUser($id)
  {
    $id = (int) $id;
    $row = $this->fetchRow('id = ' . $id);
    if (! $row)
    {
      throw new Exception("Could not find row $id");
    }
    return $row->toArray();
  }

  public function getUsers()
  {
    $rows = $this->fetchAll($this->select()->order('name'));
    return $rows;
  }

  public function getUserByUsername($username)
  {
    /**
     * get a user from userid
     */
    $sql = "select * from user where userid = '{$username}'";
    //$row = $this->fetchRow("username = '{$username}'" );
    $rows = $this->queryRows($sql);
    /*if (!$rows) {
      throw new Exception("No matching users found");
    }*/
    return ($rows) ? $rows[0] : null;
    // return $rows[0];
  }

  public function getUsersByUsername($username)
  {
    /**
     * get a user from userid
     */
    $sql = "select * from user where name like '%{$username}%'";
    //$row = $this->fetchRow("username = '{$username}'" );
    $rows = $this->queryRows($sql);
    #if (!$rows) {
    #  throw new Exception("No matching users found");
    #}
    return $rows;
  }
}

