<?php
/**
 * This implements the model for accessing template
 * rows
 */

class Application_Model_DbTable_TemplateRows extends Checklist_Model_Base {
  protected $_name = 'template_rows';

  public function init(){
    parent::init();
  }

  public function getRows($id, $page_num, $lang) {
    $id = ( int ) $id;
    $page_num = (int) $page_num;
    $sql = <<<"SQL"
      select r.varname, r.row_type, r.score, p.page_num, p.page_id,
      r.prefix, r.heading, r.text, r.info, r.element_count,
      lp.row_id lprowid, lp.def lpdefault, lp.{$lang} lplang,
      lh.row_id lhrowid, lh.def lhdefault, lh.{$lang} lhlang,
      lt.row_id ltrowid, lt.def ltdefault, lt.{$lang} ltlang,
      li.row_id lirowid, li.def lidefault, li.{$lang} lilang
      from page p, template_row r left join lang lp on (r.prefix = lp.row_id)
        left join lang lh on (r.heading = lh.row_id)
        left join lang lt on (r.text = lt.row_id)
        left join lang li on (r.info = li.row_id)
   where r.template_id = {$id}
    and p.page_id = r.page_id
    and p.page_num = {$page_num}
    order by r.part, r.level1, r.level2, r.level3, r.level4
SQL;
    // $this->log->logit("SQL: {$sql}");
    $rows = $this->queryRows($sql); // $stmt->fetchAll ();
    if (! $rows) {
      throw new Exception ( "No rows available for this template." );
    }
    /*
     * foreach($rows as $row) { logit("{$row['text']}"); }
     */
    return $rows;

  }

  public function getAllRows($id, $lang) {
    $id = ( int ) $id;
    $sql = <<<"SQL"
select r.varname, r.row_type, r.score, p.page_num, p.page_id,
       r.prefix, r.heading, r.text, r.info, r.element_count, r.required,
       lp.row_id lprowid, lp.def lpdefault, lp.{$lang} lplang,
       lh.row_id lhrowid, lh.def lhdefault, lh.{$lang} lhlang,
       lt.row_id ltrowid, lt.def ltdefault, lt.{$lang} ltlang,
       li.row_id lirowid, li.def lidefault, li.{$lang} lilang
  from page p, template_row r left join lang lp on (r.prefix = lp.row_id)
       left join lang lh on (r.heading = lh.row_id)
       left join lang lt on (r.text = lt.row_id)
       left join lang li on (r.info = li.row_id)
 where r.template_id = {$id}
   and p.page_id = r.page_id
 order by r.part, r.level1, r.level2, r.level3, r.level4
SQL;
    // $this->log->logit("SQL: {$sql}");
    $rows = $this->queryRows($sql); // $stmt->fetchAll ();
    if (! $rows) {
      throw new Exception ( "No rows available for this template." );
    }
    /*
     * foreach($rows as $row) { logit("{$row['text']}"); }
     */
    return $rows;

  }

  public function getAllRowsNotext($id, $lang) {
    // get the rows without the text
    $id = ( int ) $id;
    $sql = <<<"SQL"
select r.varname, r.row_type, r.element_count, r.required
  from page p, template_row r
 where r.template_id = {$id}
   and p.page_id = r.page_id
 order by r.part, r.level1, r.level2, r.level3, r.level4
SQL;
    // $this->log->logit("SQL: {$sql}");
    $rows = $this->queryRows($sql); // $stmt->fetchAll ();
    if (! $rows) {
      throw new Exception ( "No rows available for this template." );
    }
    /*
     * foreach($rows as $row) { logit("{$row['text']}"); }
    */
    return $rows;

  }
  public function getByVarname($tid, $regex) {
    /*
     * Return all matching template rows which have
     *   varname rlike the regex
     */
    $sql = <<<"SQL"
select * from template_row
 where template_id = {$tid}
   and varname rlike '{$regex}'
SQL;

   $rows =  $this->queryRows($sql);
   return $rows;
  }

  public function findPageId($template_id, $varname) {
    /// given a template id and varname get its page_id
    $template_id = (int) $template_id;
    $sql = <<<"END"
SELECT page_id
FROM template_row
WHERE template_id = {$template_id}
AND varname = '{$varname}'
END;
    $rows = $this->queryRows($sql);
    $this->log->logit("TR R: ". print_r($rows, true));
    if (! $rows) {
      return null;
    } else {
      return $rows[0]['page_id'];
    }
  }
}