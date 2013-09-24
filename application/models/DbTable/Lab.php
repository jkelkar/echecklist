<?php

/**
 * This implements the model for lab data
 *
 */
//require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Lab extends Checklist_Model_Base
{
  protected $_name = 'lab';

  public function init(){
    parent::init();
  }
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

  public function getLabByLabnum($labnum)
  {
    /**
     * Get a lab with this id
     */
    $row = $this->fetchRow("labnum = '$labnum'");

    if (! $row) {
      $this->log->logit("bylabnum: {$labnum} NOT found");
      return null;
    } else
      $out = $row->toArray();
    $this->log->logit("bylabnum: {$labnum} -> ". print_r($out, true));
    return $out;
  }

  public function getLabs($data, $start=0, $ct=1000) {
    /*
     * Get $ct labs starting at position $start
     */
    $this->log->logit("Lab:getLabs: ".print_r($data, true)." {$start}, {$ct}");
    $sql = "select * from lab where 1=1 ";
    foreach ($data as $a => $b) {
      if ($b == '' || $a == 'submit_button') continue;
      switch($a) {
      case 'country':
        if ($b != '-') {
          $sql = $sql . " and {$a} = '{$b}' ";
        }
        break;
      default:
        $sql = $sql . " and {$a} like '%{$b}%' ";
      }
    }
    //$sql = $sql . " limit {$start}, {$ct}";
    $this->log->logit("getLabs: {$sql}");
    $rows = $this->queryRows( $sql );
    foreach($rows as $row) {
      $this->log->logit("{$row['labname']} -- {$row['country']}");
    }
    if (count($rows) == 0) {
      $rows = array();
    }
    return $rows;

  }

  public function getAllLabs( $start, $ct) {
    $this->log->logit("Labs: getalllabs");
    $sql = "select * from lab limit {$start}, {$ct}";
    $this->log->logit("getAllLabs: {$sql}");
    $rows = $this->queryRows( $sql );
    foreach($rows as $row) {
      $this->log->logit("{$row['labname']} -- {$row['country']}");
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

  public function getDistinctCountries() {
    $sql = "select distinct country from lab";
    $rows = $this->queryRows($sql);
    if (!$rows) {
      throw new Exception("No labs found");
    }
    return $rows;
  }

}

