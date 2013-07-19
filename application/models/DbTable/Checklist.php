<?php

/**
 * This fetches the db handle so we can run straight SQL 
 * queries with it/
 */
require_once 'modules/Checklist/logger.php';
class Application_Model_DbTable_Checklist extends Zend_Db_Table_Abstract
{
  public function getDb()
  {
    $path = APPLICATION_PATH . '/configs/application.ini';

    $config = new Zend_Config_Ini($path, 'staging');
    //logit("app path: {$path}");
    //logit("X: {$config->resources->db->params->dbname}");
    $db = new Zend_Db_Adapter_Pdo_Mysql
      (
       array(
             'host'             => $config->resources->db->params->host, //'localhost',
             'username'         => $config->resources->db->params->username, //'root',
             'password'         => $config->resources->db->params->password, //'3ntr0py',
             'dbname'           => $config->resources->db->params->dbname //'mydb',
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