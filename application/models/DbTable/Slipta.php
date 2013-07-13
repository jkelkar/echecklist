<?php

/**
 * This implements the model for accessing the data for the display
 * rows
 */
require_once 'modules/Checklist/fillout.php';

class Application_Model_DbTable_Slipta extends Application_Model_DbTable_Checklist
// Zend_Db_Table_Abstract
{
  protected $_name = 'slipta';

  public function getrows($id)
  {
    $debug = 1;
    $db = $this->getDb();
    $id = (int)$id;
    // Read the following sql with $id == tmpl_head_id
    $sql = "select * from tmpl_row tr where tr.tmpl_head_id = " . $id .
      " order by part, level1, level2, level3, level4, level5" ;
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      throw new Exception("Could not find rows in getTmplrows!");
    }
    /**
       $tout = array();
       $tout[] = '<table border=1>';
       $tout[] = '<td width=60mm></td><td width=60mm></td><td width=20mm></td>';
       foreach($rows as $row){
       // echo $row['id']. ' ' . $row['row_text'] . '<br />';
       $tout[] = '<tr>' . call_user_func("partial_{$}", $row, $value) . '</tr>';
       }
    **/
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