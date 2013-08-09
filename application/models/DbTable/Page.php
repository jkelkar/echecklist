<?php

/**
 * This implements the model for Page tags
 *
 */
require_once 'modules/Checklist/logger.php';

class Application_Model_DbTable_Page extends Application_Model_DbTable_Checklist
{
  protected $_name = 'page';
  protected $_primary = 'id';

  public function getPages($template_id) {
    /**
     * Get all the pages for this temp_head_id
     */
    $this->setMetadataCacheInClass(false);
    $db = $this->getDb();
    $template_id = (int)$template_id;
    $sql = "select * from page where template_id = ". $template_id .
      " order by parent, page_id";
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    
    if (!$rows) {
      throw new Exception("Could not find any pages.");
    }
    return $rows;
  }

  public function getPage($template_id, $page_num) {
    $db = $this->getDb();
    $template_id = (int)$template_id;
    $page_num = (int)$page_num;
    logit("PAGE: {$template_id} {$page_num}");
    /**$row = $this->fetchAll
      (
       $this->select()
       ->where('template_id = ?', $template_id)
       ->where('page_num = ?', $page_num));
    **/
    
    $sql = "select * from page where template_id = ". $template_id .
      " and page_num = " . $page_num ;
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    
    if (!$rows) {
      throw new Exception("Could not find page.");
    }
    return $rows[0];
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
}

