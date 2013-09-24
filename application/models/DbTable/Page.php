<?php

/**
 * This implements the model for Page tags
 *
 */

class Application_Model_DbTable_Page extends Checklist_Model_Base
{
  protected $_name = 'page';
  protected $_primary = 'id';

  public function init()
  {
    parent::init();
  }

  public function getPages($template_id) {
    /**
     * Get all the pages for this temp_head_id
     */
    $this->setMetadataCacheInClass(false);
    $template_id = (int)$template_id;
    $sql = "select * from page where template_id = ". $template_id .
      " order by parent, page_id";
    $rows =  $this->queryRows($sql);

    if (!$rows) {
      throw new Exception("Could not find any pages.");
    }
    return $rows;
  }

  public function getPage($template_id, $page_num) {
    $template_id = (int)$template_id;
    $page_num = (int)$page_num;
    //$this->log->logit("PAGE: {$template_id} {$page_num}");
    /**$row = $this->fetchAll
      (
       $this->select()
       ->where('template_id = ?', $template_id)
       ->where('page_num = ?', $page_num));
    **/

    $sql = "select * from page where template_id = ". $template_id .
      " and page_num = {$page_num}" ;
    //$this->log->logit("getPage: ". $sql);
    $rows =  $this->queryRows($sql);

    if (!$rows) {
      throw new Exception("Could not find page.");
    }
    return $rows[0];
  }

public function getStartPage($template_id) {
    $template_id = (int)$template_id;
    //$this->log->logit("PAGE: {$template_id} ");
    $sql = "select * from page where template_id = ". $template_id .
      " and start = 't'" ;
    $rows = $this->queryRows($sql);

    if (!$rows) {
      throw new Exception("Could not find page.");
    }
    //$this->log->logit('Start page: ' . print_r($rows[0], true));
    return $rows[0]['page_num'];
  }

  public function getNav($template_id, $page_num)
  {
    /**
     * get the calculated nav items for showing with dtree
     */
    $row = $this->getPage($template_id, $page_num);
    $rows = $this->getPages($template_id);
    return array('row' => $row,
                 'rows' => $rows);
  }

  public function getSectionPages($template_id) {
    // get page_ids for section entries in this template
    $template_id = (int) $template_id;
    $sql = <<<"END"
select page_num, tag from page
 where template_id = {$template_id}
   and tag like 'Section %'
 order by tag
END;
    $rows = $this->queryRows($sql);
    return $rows;
  }
}

