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
    $db = $this->getAdapter();

    /**new Zend_Db_Adapter_Pdo_Mysql
      (
       array(
             'host'             => $config->resources->db->params->host, //'localhost',
             'username'         => $config->resources->db->params->username, //'root',
             'password'         => $config->resources->db->params->password, //'3ntr0py',
             'dbname'           => $config->resources->db->params->dbname //'mydb',
             ));
    **/
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

  public function execute($sql) {
    /*
     * This runs the sql against the database and returns the result
    * as $rows
    */
    $db = $this->getDb();
    $stmt = $db->query($sql);
    // $rows = $stmt->fetchAll();
    // return $rows;
  }

  public function queryRows($sql) {
    /*
     * This runs the sql against the database and returns the result
     * as $rows
     */
    logit("SQL: {$sql}");
    $db = $this->getDb();
    $stmt = $db->query($sql);
    $rows = $stmt->fetchAll();
    return $rows;
  }

  public function queryRowcount($sql) {
    /*
     * This runs the sql against the database and returns the count
    */
    $db = $this->getDb();
    $stmt = $db->query($sql);
    $ct = $stmt->rowCount();
    return $ct;
  }

  public function queryRowsAsJSON($sql) {
    /*
     * This runs the sql against the database and returns the result
     * as JSON
     */
    $db = $this->getDb();
    $stmt = $db->query($sql);
    $rows = $stmt->fetchAll();
    $jenc = json_encode($rows);
    return $jenc;
  }

  public function get($id)
  {
    /**
     * Get a row with this id
     */
    $id = (int)$id;
    $row = $this->fetchRow('id = ' . $id);
    if (!$row) {
      throw new Exception("Could not find row $id");
    }
    return $row->toArray();
  }

  public function insertData($data) {
    /**
     * Create a new row
     * data is an array with name value pairs
     */
    if (array_key_exists('submit_button', $data))
      unset($data['submit_button']);
    $this->insert($data);
    $newid = $this->getAdapter()->lastInsertId();
    return $newid;
  }

  public function updateData($data, $id) {
    /**
     * Update row at $id with this data
     * $data is an array with name value pairs
     */
    logit('DATA: '. print_r($data, true));
    $this->update($data, "id = {$id}");
  }

  public function deleteLab($id) {
    /**
     * delete row at $id
     */
    $this->delete('id = ' . (int)$id);
  }
}

