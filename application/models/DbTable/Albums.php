<?php
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
    /*
     * $rows = $this->fetchAll($this->select()
     *                      ->order('artist'));
     * print_r($rows);
     */
    //$this->getConnection();
    $db = new Zend_Db_Adapter_Pdo_Mysql(
                                    array(
                                          'host'             => 'localhost',
                                          'username'         => 'root',
                                          'password'         => '3ntr0py',
                                          'dbname'           => 'mydb',
                                          //  'adapterNamespace' => 'MyProject_Db_Adapter'
                                          ));
    $stmt = $db->query("select * from albums order by artist desc");
    $rows = $stmt->fetchAll();
    return $rows;
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