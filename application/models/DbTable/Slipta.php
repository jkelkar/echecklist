<?php

/**
 * This implements the model for accessing template 
 * rows
 */

class Application_Model_DbTable_Slipta extends Application_Model_DbTable_Checklist
{
  protected $_name = 'slipta';

  public function getrows($id, $page_num)
  {
    $debug = 1;
    $db = $this->getDb();
    $id = (int)$id;
    // Read the following sql with $id == tmpl_head_id
    $sql = "select * from tmpl_row tr where tr.tmpl_head_id = " . $id . 
      " and page_num = " . $page_num . 
      " order by part, level1, level2, level3, level4, level5" ;
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      throw new Exception("No rows available for this template.");
    }
    return $rows;
  }

  /*
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
  */
}