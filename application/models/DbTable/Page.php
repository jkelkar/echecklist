<?php

/**
 * This implements the model for Page tags
 * 
 */
class Application_Model_DbTable_Page extends Application_Model_DbTable_Checklist
{
  protected $_name = 'page';

  public function get_page_tag($tmpl_head_id, $page_num)
  {
    //$log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
    $debug = 1;
    $db = $this->getDb();
    $tmpl_head_id = (int)$tmpl_head_id;
    $page_num = (int)$page_num;
    $sql = "select * from page where tmpl_head_id = ". $tmpl_head_id .
      " and page_num = " . $page_num ;
    $stmt =  $db->query($sql);
    $rows = $stmt->fetchAll();
    if (!$rows) {
      throw new Exception("These is no tag for this page.");
    }
    $value = $rows[0]['tag'];
    return $value;
  }
}

