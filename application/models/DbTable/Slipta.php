<?php

/**
 * This implements the model for accessing template 
 * rows
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Slipta extends Application_Model_DbTable_Checklist
{
  protected $_name = 'slipta';

  public function getrows($id, $page_num, $lang)
  {
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
    /**
     * this is here so Slipta template will be only pulled from
     * table tmpl_row
     */
    $sql = <<<"SQL"
 select r.varname, r.row_type, r.part, r.level1, r.level2,
        r.level3, r.level4, r.level5, r.element, r.score, r.page_num,
      r.prefix, r.heading, r.text
   from tmpl_row r
   where r.tmpl_head_id = {$id} 
    and r.page_num = {$page_num} 
 order by r.part, r.level1, r.level2, r.level3, r.level4, r.level5
SQL;
    logit("SQL: {$sql}");
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      throw new Exception("No rows available for this template.");
    }
    foreach($rows as $row) {
      logit("{$row['text']}");
    }
    return $rows;
  }

  public function addAudit($artist, $title)
  {
    $data = array(
		  'artist' => $artist,
		  'title' => $title,
		  );
    $this->insert($data);
  }
  public function updateAudit($id, $artist, $title)
  {
    $data = array(
		  'artist' => $artist,
		  'title' => $title,
		  );
    $this->update($data, 'id = '. (int)$id);
  }
  public function deleteAudit($id)
  {
    $this->delete('id =' . (int)$id);
  }

}