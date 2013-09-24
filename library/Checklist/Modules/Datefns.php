<?php

/**
 *  This contains date related functions
 */
class Checklist_Modules_Datefns {

  public $log;
  public function init() {
    $this->log = new Checklist_Logger();
  }
  public function convert_ISO($value) {
    $format = 'm/d/Y';
    $ISOformat = 'Y-m-d';
    $dt = date_parse_from_format($format, $value);
    $date = new DateTime();
    $date->setDate($dt['year'], $dt['month'], $dt['day']);
    // $this->log->logit('dt: '. $value);
    return $date;
  }
}