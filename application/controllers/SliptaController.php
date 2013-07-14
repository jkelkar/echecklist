<?php
require_once 'modules/KLogger.php';
require_once 'modules/Checklist/fillout.php';

$log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
class SliptaController extends Zend_Controller_Action
{

  public function init()
  {
    /* Initialize action controller here */
  }

  public function indexAction()
  {

  }

  public function editAction()
  {
    $slipta = new Application_Model_DbTable_Slipta();
    $data = new Application_Model_DbTable_Data();
    $rows = $slipta->getrows(1); // 1 is the tmpl_head_id
    $value = $data->get_data(1); // 1 is the data_head_id
    //$value = array('notme' => 'no');
    $tout = calculate_page($rows, $value);
    $this->view->outlines = implode("\n", $tout);
  }

}