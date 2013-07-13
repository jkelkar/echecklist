<?php

class SliptaController extends Zend_Controller_Action
{

  public function init()
  {
    /* Initialize action controller here */
  }

  public function indexAction()
  {

  }

  public function showAction()
  {
    $slipta = new Application_Model_DbTable_Slipta();
    $rows = $slipta->getrows(1);
    /**
     * foreach($rows as $row){
     * //echo $row['id'];
     *
     }*/

  $value = array('notme' => 'no');
  $tout = array();
  $tout[] = '<table border=1>';
  $tout[] = '<td width=60mm></td><td width=60mm></td><td width=20mm></td>';
  foreach($rows as $row){
    $row_type = $row['row_type'];
    $tout[] = '<tr>' . call_user_func("partial_{$row_type}", $row, $value) . '</tr>';
  }
  $tout[] = '</table>';
  $this->view->outlines = implode("\n", $tout);
    // 'this is a the outline';
  }

}