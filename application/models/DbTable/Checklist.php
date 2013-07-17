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
    return $db;
  }
}