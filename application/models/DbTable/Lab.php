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

  public function getLabByPartialName($name)
  {
  	/**
  	 * Get a labname and id by beginning of a labname
  	 */
  	$db = $this->getDb();
  	$stmt = $db->query("select id, labname from lab where labname like '{$name}%' order by labname limit 0, 4");
  	$rows = $stmt->fetchAll();
  	$jenc = json_encode($rows);
  	return $jenc;
  	/*return $rows;
  	logit("Finddb: {$name}");
  	$rows = $this->fetchAll($this->select()
  			->where("labname like '{$name}%' ")
  			->limit(4, 0)
  	);
  	if (!$row) {
  		throw new Exception("Could not find row $id");
  	}
  	$jenc = json_encode($rows);
  	return $jenc;*/
  }
  
  public function getLabs() {
    $rows = $this->fetchAll($this->select()
			    ->order('labname'));
    return $rows;
  }

  public function getLabsByCountry($ccode) {
    /**
     * Return all labs in this country code
     */
    $rows = $this->fetchAll($this->select()
                            ->where("country_code = ?", $ccode)
                            ->order('labname'));
    return $rows;
  }

  public function newLab($data) {
    /**
     * Create a new lab
     * data is an array with name value pairs
     */
    $this->insert($data);
    $newid = $this->getAdapter()->lastInsertId();
    return $newid;
  }

  public function updateLab($data, $id) {
    /**
     * Update lab at $id with this data
     * $data is an array with name value pairs
     */
    $this->update($data, "id = " . (int)$id);
  }
   
  public function deleteLab($ind) {
    /**
     * delete user at id
     */
    $this->delete('id = ' . (int)$id);
  }
}

