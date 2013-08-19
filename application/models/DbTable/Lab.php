<?php

/**
 * This implements the model for lab data
 *
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Lab extends Application_Model_DbTable_Checklist
{
  protected $_name = 'lab';

  public function getLab($id)
  {
    /**
     * Get a lab with this id
     */
    $id = (int)$id;
    $row = $this->fetchRow('id = ' . $id);
    if (!$row) {
      throw new Exception("Could not find row $id");
    }
    return $row->toArray();
  }

  public function getLabs($data, $start, $ct) {
    /*
     * Get $ct labs starting at position $start
     */
    logit("Lab:getLabs: ".print_r($data, true)." {$start}, {$ct}");
    $sql = "select * from lab where 1=1 ";
    foreach ($data as $a => $b) {
      if ($b == '') continue;
      switch($a) {
      case 'country':
        $sql = $sql . " and {$a} = '{$b}' ";
        break;
      default:
        $sql = $sql . " and {$a} like '{$b}%' ";
      }
    }
    $sql = $sql . " limit {$start}, {$ct}";
    logit("getLabs: {$sql}");
    $rows = $this->queryRows( $sql );
    foreach($rows as $row) {
      logit("{$row['labname']} -- {$row['country']}");
    }
    if (count($rows) == 0) {
      $rows = array();
    }
    return $rows;
  
  }
  
  public function getAllLabs( $start, $ct) {
    logit("Labs: getalllabs");
    $sql = "select * from lab limit {$start}, {$ct}";
    logit("getAllLabs: {$sql}");
    $rows = $this->queryRows( $sql );
    foreach($rows as $row) {
      logit("{$row['labname']} -- {$row['country']}");
    }
    return $rows;
  }
  
  public function getLabByPartialName($name)
  {
  	/**
  	 * Get a labname and id by beginning of a labname
  	 */
    $sql = "select * from lab where labname like '{$name}%' " .
      "and country like '{$ctry}%' order by labname limit 0, 4";
    // $jenc = $this->queryRowsAsJSON($sql);
  	// return $jenc;
  	 $rows = $this->queryRows($sql);
  	 return $rows;
  }

  public function insertData($data) {
    /**
     * Create a new lab
     * data is an array with name value pairs
     */
    $this->insert($data);
    $newid = $this->getAdapter()->lastInsertId();
    return $newid;
  }

  public function updateData($data, $id) {
    /**
     * Update lab at $id with this data
     * $data is an array with name value pairs
     */
    logit('LABDATA: '. print_r($data, true));
    $this->update($data, "id = {$id}");
  }
   
  public function deleteLab($ind) {
    /**
     * delete user at id
     */
    //$this->delete('id = ' . (int)$id);
  }

  public function getDistinctCountries() {
    $sql = "select distinct country from lab";
    $rows = $this->queryRows($sql);
    if (!$rows) {
      throw new Exception("No labs found");
    }
    return $rows;
  } 
    
}

