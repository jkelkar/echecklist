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

  public function getPages($tmpl_head_id) {
    /**
     * Get all the pages for this temp_head_id
     */
    $this->setMetadataCacheInClass(false);
    $db = $this->getDb();
    $tmpl_head_id = (int)$tmpl_head_id;
    $sql = "select * from page where tmpl_head_id = ". $tmpl_head_id .
      " order by parent, page_num";
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    
    if (!$rows) {
      throw new Exception("Could not find any pages.");
    }
    return $rows;
  }

  public function getPage($tmpl_head_id, $page_num)
  {
    $db = $this->getDb();
    $tmpl_head_id = (int)$tmpl_head_id;
    $page_num = (int)$page_num;
    /**$row = $this->fetchAll
      (
       $this->select()
       ->where('tmpl_head_id = ?', $tmpl_head_id)
       ->where('page_num = ?', $page_num));
    **/
    
    $sql = "select * from page where tmpl_head_id = ". $tmpl_head_id .
      " and page_num = " . $page_num ;
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    
    if (!$rows) {
      throw new Exception("Could not find page.");
    }
    return $rows[0];
  }

  public function getNav($tmpl_head_id, $page_num)
  {
    /**
     * get the calculated nav items for showing with dtree
     */
    $row = $this->getPage($tmpl_head_id, $page_num);
    $rows = $this->getPages($tmpl_head_id);
    return array('row' => $row,
                 'rows' => $rows);
  }
}

