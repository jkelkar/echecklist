<?php //-*- coding: utf-8 -*-
?>
<?php

/**
 * using the widgets and partials fill out the row
 */

/**
 * This handles logging
 */
//require_once 'modules/KLogger.php';

$userid = null;
$langtag = null;
$user = null;

class Checklist_Logger {

  public function logit($msg) {
    $log = new Checklist_Modules_KLogger("/var/log/log.txt", Checklist_Modules_KLogger::DEBUG);
    $log->LogInfo($msg); 
  }
}
/*
function logit($msg) {
  $log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
  $log->LogInfo($msg);
}
*/