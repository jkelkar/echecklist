<?php

/**
 *  This contains date related functions
 */
function convert_ISO($value) {
  $format = 'm/d/Y';
  $ISOformat = 'Y-m-d';
  $dt = date_parse_from_format($format, $value);
  $date = new DateTime();
  $date->setDate($dt['year'], $dt['month'], $dt['day']);
  // logit('dt: '. $value);
  return $date;
}