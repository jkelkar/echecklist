<?php //-*- coding: utf-8 -*-
?>
<?php

/**
 * using the widgets and partials fill out the row
 */

/**
 * This handles logging
 */
require_once 'modules/KLogger.php';

$userid = null;
$langtag = null;
$user = null;

function logit($msg) {
  $log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
  $log->LogInfo($msg);
}