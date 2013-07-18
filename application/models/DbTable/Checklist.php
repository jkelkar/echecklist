<?php

/**
 * This fetches the db handle so we can run straight SQL 
 * queries with it/
 */
class Application_Model_DbTable_Checklist extends Zend_Db_Table_Abstract
{
  public function getDb()
  {
    $db = new Zend_Db_Adapter_Pdo_Mysql
      (
       array(
             'host'             => 'localhost',
             'username'         => 'root',
             'password'         => '3ntr0py',
             'dbname'           => 'mydb',
             ));
    // set results to be UTF8
    $sql = "set names 'utf8'";
    $db->query($sql);
    $sql = "set character_set_client=utf8";
    $db->query($sql);
    $sql = "set character_set_connection=utf8";
    $db->query($sql);
    $sql = "set character_set_server=utf8";
    $db->query($sql);
    return $db;
  }
}