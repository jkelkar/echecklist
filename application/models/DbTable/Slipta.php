<?php
/**
 * This implements the model for accessing template
 * rows
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Slipta extends Application_Model_DbTable_Checklist {
  protected $_name = 'template_rows';

  public function getrows($id, $page_num, $lang) {
    $db = $this->getDb ();
    $id = ( int ) $id;
    $sql = <<<"SQL"
      select r.varname, r.row_type, r.score, p.page_num, p.page_id,
      r.prefix, r.heading, r.text, r.info,
      lp.row_id lprowid, lp.def lpdefault, lp.{$lang} lplang,
      lh.row_id lhrowid, lh.def lhdefault, lh.{$lang} lhlang,
      lt.row_id ltrowid, lt.def ltdefault, lt.{$lang} ltlang
      from page p, template_row r left join lang lp on (r.prefix = lp.row_id)
        left join lang lh on (r.heading = lh.row_id)
        left join lang lt on (r.text = lt.row_id)
   where r.template_id = {$id}
    and p.page_id = r.page_id
    and p.page_num = {$page_num}
    order by r.part, r.level1, r.level2, r.level3, r.level4
SQL;
    // logit("SQL: {$sql}");
    // $stmt = $db->query ( $sql );
    $rows = $this->queryRows($sql); // $stmt->fetchAll ();
    if (! $rows) {
      throw new Exception ( "No rows available for this template." );
    }
    /*
     * foreach($rows as $row) { logit("{$row['text']}"); }
     */
    return $rows;
  
  }
}