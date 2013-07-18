<?php

/**
 * This implements the model for accessing template 
 * rows
 */

class Application_Model_DbTable_Slipta extends Application_Model_DbTable_Checklist
{
  protected $_name = 'slipta';

  public function getrows($id, $page_num, $lang)
  {
    //$debug = 1;
    $log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
    $db = $this->getDb();
    $id = (int)$id;
    // Read the following sql with $id == tmpl_head_id
    $sql = <<<"SQL"
 select r.varname, r.row_type, r.part, r.level1, r.level2,
        r.level3, r.level4, r.level5, r.element, r.score, r.page_num,
      l.prefix, l.heading, l.text, l.ss_hint, l.row_hint
   from tmpl_row r, lang_text l 
  where l.tag = '{$lang}' 
    and r.tmpl_head_id = {$id} 
    and r.page_num = {$page_num} and r.row_name = l.row_name
 order by r.part, r.level1, r.level2, r.level3, r.level4, r.level5
SQL;
    $log->LogInfo("SQL: {$sql}");
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      throw new Exception("No rows available for this template.");
    }
    foreach($rows as $row) {
      $log->LogInfo("{$row['text']}");
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