<?php

/**
 * This makes the connection so we do not have to do it in each of the tests
 */

abstract class Tests_Model_Checklist_Abstract extends Zend_Test_PHPUnit_DatabaseTestCase
{

  private $_connectionMock;
  /**
   * Returns the database connection.
   * 
   * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
   */
  protected function getConnection()
  {
    $database = 'mydb_test';
    $path = APPLICATION_PATH . '/configs/application.ini';
    
    $config = new Zend_Config_Ini($path, 'testing');
    $dbconf = $config->resources->db;
    $dbparams = $dbconf->params;
    $database = $dbparams->dbname;  //'mydb_test';
    if ($this->_connectionMock == null) {
      $connection = Zend_Db::factory
        ($dbconf->adapter,
         array(
               'host' => $dbparams->host, //'localhost',
               'username' => $dbparams->username, //'root',
               'password' => $dbparams->password, //'3ntr0py',
               'dbname' => $database //'mydb_test'
               ));
      //logit("app path: {$path}");
      //logit("X: {$config->resources->db->params->dbname}");
      // set results to be UTF8
      $db = $connection;
      $sql = "set names 'utf8'";
      $db->query($sql);
      $sql = "set character_set_client=utf8";
      $db->query($sql);
      $sql = "set character_set_connection=utf8";
      $db->query($sql);
      $sql = "set character_set_server=utf8";
      $db->query($sql);
      $this->_connectionMock = $this->createZendDbConnection
        (
         $connection, 'zfunittests'
         );
      Zend_Db_Table_Abstract::setDefaultAdapter($connection);
    }
    return $this->_connectionMock;
  }
  
}