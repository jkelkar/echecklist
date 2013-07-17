<?php
class Application_Model_DbTable_Albums extends Application_Model_DbTable_Checklist
// Zend_Db_Table_Abstract
{
  protected $_name = 'albums';

  public function getAlbums() {
    $db = $this->getDb();
    /*$db = new Zend_Db_Adapter_Pdo_Mysql
      (
       array(
             'host'             => 'localhost',
             'username'         => 'root',
             'password'         => '3ntr0py',
             'dbname'           => 'mydb',
             ));
    */
    $stmt = $db->query("select * from albums order by artist desc");
    $rows = $stmt->fetchAll();
    return $rows;
  }
    
  public function getAlbum($id) {
    $db = $this->getDb();
    $id = (int)$id;
    $stmt = $db->query("select * from albums where id = ". $id);
    $row = $stmt->fetch();
    return $row;
  }

  public function addAlbum($artist, $title)
  {
    $data = array(
		  'artist' => $artist,
		  'title' => $title,
		  );
    $this->insert($data);
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