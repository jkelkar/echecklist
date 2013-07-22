<?php

/**
 * For insert to return the created id
 * We have to include the id field in the data being inserted
 * $id = null
 * somone said this has been fixed - best to know in advance
 */
class Application_Model_DbTable_Albums extends Zend_Db_Table_Abstract
{
  protected $_name = 'albums';

  public function getAlbum($id)
  {
    $id = (int)$id;
    $row = $this->fetchRow('id = ' . $id);
    if (!$row) {
      throw new Exception("Could not find row $id");
    }
    return $row->toArray();
  }

  public function getAlbums() {
    // $rows = $this->fetchAll(null, 'artist');
    $rows = $this->fetchAll($this->select()
			    ->order('artist'));
    
    //$this->getConnection();
    return $rows;
  }

  public function addAlbum($artist, $title)
  {
    $data = array(
		  'artist' => $artist,
		  'title' => $title,
		  );
    $this->insert($data);
    $newid = $this->lastInsertId();
    return $newid;
  }
  public function updateAlbum($id, $artist, $title)
  {
    $data = array(
		  'artist' => $artist,
		  'title' => $title,
		  );
    $this->update($data, 'id = '. (int)$id);
  }
  public function deleteAlbum($id)
  {
    $this->delete('id =' . (int)$id);
  }
}