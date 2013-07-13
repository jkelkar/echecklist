<?php

class Application_Model_Albums
{
  protected $_dbTable;

  public function setDbTable($dbTable)
  {
    if (is_string($dbTable)) {
      $dbTable = new $dbTable();
    }
    if (!$dbTable instanceof Zend_Db_Table_Abstract) {
      throw new Exception('Invalid table data gatewat provided');
    }
    $this->_dbTable = $dbTable;
    return $this;
  }

  public function getDbTable()
  {
    if (null ==  $this->_dbTable) {
      $this->setDbTable('Application_Model_DbTable_Albums');
    }
    return $this->_dbTable;
  }
  
  public function save(Application_model_Ablums $album)
  {
    $data = array(
		  'artits' => $album->getArtist(),
		  'title' => $album->getTitle()
		  );
    if (null == ($id = $album->getId())) {
      unset($data['id']);
      $this->getDbTable()->insert($data);
    } else {
      $this->getdbTable()->update($data, array('id= ?' => $id));
    }
  }

 

}