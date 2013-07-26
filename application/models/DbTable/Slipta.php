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
    $sql = <<<"SQL"
 select r.varname, r.row_type,  r.score, r.page_num,
      r.prefix, r.heading, r.text, r.info,
      lh.row_id lhrowid, lh.default lhdefault, lh.{$lang} lhlang,
      lt.row_id ltrowid, lt.default ltdefault, lt.{$lang} ltlang
      from tmpl_row r left join lang lh on (r.heading = lh.row_id)
        left join lang lt on (r.text = lt.row_id)
   where r.tmpl_head_id = {$id}
    and r.page_num = {$page_num}
    order by r.part, r.level1, r.level2, r.level3, r.level4, r.level5, element
SQL;
    // logit("SQL: {$sql}");
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      throw new Exception("No rows available for this template.");
    }
    /*foreach($rows as $row) {
      logit("{$row['text']}");
      }*/
    return $rows;
  }

  

}