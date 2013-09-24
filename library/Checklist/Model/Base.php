<?php

/**
 * This fetches the db handle so we can run straight SQL
 * queries with it/
 */
//require_once 'modules/Checklist/logger.php';
class Checklist_Model_Base extends Zend_Db_Table_Abstract
{
  public $log;
  public $datefns;
  public $general;

  public function init() 
  {
    $this->log = new Checklist_Logger();
    $this->datefns = new Checklist_Modules_Datefns();
    $this->general = new Checklist_modules_General();
  }

  public function getDb()
  {
    $path = APPLICATION_PATH . '/configs/application.ini';

    $config = new Zend_Config_Ini($path, 'staging');
    //$this->log->logit("app path: {$path}");
    //$this->log->logit("X: {$config->resources->db->params->dbname}");
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
    $this->log->logit("SQL: {$sql}");
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
    //$this->log->logit('DATA: '. print_r($data, true));
    $this->update($data, "id = {$id}");
  }

  public function deleteLab($id) {
    /**
     * delete row at $id
     */
    $this->delete('id = ' . (int)$id);
  }

  public function _mkList($data) {
    //$this->log->logit("MKL: {$data} " . print_r($data, true));
    $out = '';
    // if (count($data) == 0) {
    //  return
    if (is_string($data)) {
      // $this->log->logit('STR');
      $out =  "= '{$data}' ";
      //$this->log->logit("MKLv: {$out}");
      return $out;
    } else {
      // $this->log->logit('ARR');
      switch (count($data)) {
      	case 0 :
      	  //$this->log->logit("0: {$data} --". print_r($data, true));
      	  break;
      	case 1 :
      	  //$this->log->logit("A: = '{$data[0]}' ");
      	  //if ($data[0] == '-')
      	  //  return "= '{$data[0]}' ";
      	  if (is_string($data[0])) {
      	    $out .= "= '{$data[0]}'";
      	  } else {
      	    $out .= "= {$data[0]}";
      	  }
      	  //$this->log->logit("MKL: {$out}");
      	  return $out;
      	  break;
      	default :
      	  foreach($data as $d) {
      	    if ($out != '')
      	      $out .= ',';
      	    if (is_string($d)) {
      	      $out .= "'{$d}'";
      	    } else {
      	      $out .= "{$d}";
      	    }
      	  }
      	  //$this->log->logit("MKL2: {$out}");
      	  return "in ({$out})";
      }
      }
    }
}

