<?php
//-*- coding: utf-8 -*-
?>
<?php

/**
 * using the widgets and partials fill out the row
 */

/**
 * This handles logging
 */
require_once 'modules/Checklist/logger.php';

/**
 * returns a value if a key exists in the dictionary else
 * returns the $default value passed in
 */
function get_arrval($arr, $k, $default) {
  // logit("GA: " . gettype($arr) . " {$k}");
  $callers = debug_backtrace ();
  // logit("TRACE: {$callers[1]['function']}");
  /*
   * if (gettype($arr) == 'string') { logit("Str: {$arr}"); }
   */
  return key_exists ( $k, $arr ) ? $arr [$k] : $default;

}

function get_common_words_translated($value, $words) {
  $trans_list = array ();
  foreach ( $words as $word ) {
    $trans_list [$word] = get_arrval ( $value, $word, $word );
  }
  return $trans_list;

}

/**
 * these implement low level html code generators
 */
function SELECT($name, $optvals, $value) {
  // $log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
  if (count ( $optvals ) == 0) {
    throw new Exception ( 'Optvals has no elements', 0 );
  }
  $optout = array ();
  $val = get_arrval ( $value, $name, '' );
  // $log->LogInfo("{$name} - {$val}");
  /*
   * foreach ($value as $n => $v) { $log->LogInfo("Values {$n} - {$v}"); }
   */
  foreach ( $optvals as $n => $v ) {
    $sel = ($v == $val) ? "selected=selected " : '';
    // $log->LogInfo("Interiem - {$val} : {$sel}: {$n} => {$v}");
    $optout [] = "<option {$sel} value=\"{$v}\">{$n}</option>";
  }
  $options = implode ( "\n", $optout );
  $out = <<<"END"
<select name="{$name}" id="{$name}" class="select">
  {$options}
</select>
END;
  return $out;

}

function RADIO($name, $optvals, $value) {
  if (count ( $optvals ) == 0) {
    throw new Exception ( 'Optvals has no elements', 0 );
  }
  $optout = array ();
  $val = get_arrval ( $value, $name, '' );
  // logit("{$name} - {$val}");
  /*
   * foreach ($value as $n => $v) { logit("Values {$n} - {$v}"); }
   */
  foreach ( $optvals as $n => $v ) {
    $sel = ($v == $val) ? "checked=\"checked\" " : '';
    // logit("Interiem - {$val} : {$sel}: {$n} => {$v}");
    $optout [] = "<input style=\"margin: 0 4px 0 6px;\" type=\"radio\" name=\"{$name}\" " . "id=\"{$name}_{$n}\" value=\"{$v}\" {$sel} > {$n} ";
  }
  $options = implode ( "\n", $optout );
  $out = $options;
  return $out;

}

function RADIO_CALC($name, $optvals, $value, $score) {
  if (count ( $optvals ) == 0) {
    throw new Exception ( 'Optvals has no elements', 0 );
  }
  $optout = array ();
  $val = get_arrval ( $value, $name, '' );
  // logit("{$name} - {$val}");
  /*
   * foreach ($value as $n => $v) { logit("Values {$n} - {$v}"); }
   */
  foreach ( $optvals as $n => $v ) {
    $sel = ($v == $val) ? "checked=\"checked\" " : '';
    // logit("Interiem - {$val} : {$sel}: {$n} => {$v}");
    $optout [] = "<input style=\"margin: 0 4px 0 6px;\" type=\"radio\" name=\"{$name}\" " . "id=\"{$name}_{$n}\" value=\"{$v}\" {$sel} > {$n} ";
  }
  $options = implode ( "\n", $optout );
  $out = $options . "\n<script> watch_ynp('{$name}', {$score});</script>";
  return $out;

}

function TEXTAREA($name, $value, $style = '', $class = '') {
  $val = get_arrval ( $value, $name, '' );
  $use_style = ($style == '') ? "style=\"height:50px;\"" : "style=\"{$style}\"";
  $out = <<<"END"
    <textarea {$use_style} onchange="noteChange();" name="{$name}" id="{$name}" class="tarea {$class}">{$val}</textarea>
END;
  return $out;

}

function LABEL($name, $label_text = '', $label_style = "") {
  $out = "<label for=\"{$name}\" style=\"{$label_style}\">{$label_text}</label>";
  return $out;

}

function INPUT($name, $value, $type = "string", $length = 0, $style = "", $class = '') {
  $size = $dtype = '';
  switch ($type) {
    case 'integer' :
    case 'date' :
    case 'datetime' :
    case 'string' :
      $dtype = $type;
      $itype = 'text';
      if ($length != 0) {
        $l = strval ( $length );
        $size = "size=\"{$l}\" ";
      }
      break;
    
    case 'password' :
      // this implies a string
      $dtype = $type;
      $itype = $dtype;
      $l = strval ( $length );
      $size = "size=\"{$l}\" ";
      break;
    case 'submit' :
      $dtype = $type;
      $itype = $dtype;
      break;
    default :
      $dtype = 'unexpected';
  }
  $val = ($type != 'submit') ? get_arrval ( $value, $name, '' ) : $value;
  $out = <<<"END"
<input name="{$name}" id="{$name}"
type="{$itype}" class="{$dtype} {$class}" style="{$style}" value="{$val}" {$size} >
END;
  
  return $out;

}

function INPUT_AC($name, $value, $type = "string", $length = 0, $style = "", $class = '') {
  $size = $dtype = '';
  switch ($type) {
    case 'integer' :
    case 'date' :
    case 'datetime' :
    case 'string' :
      $dtype = $type;
      $itype = 'text';
      if ($length != 0) {
        $l = strval ( $length );
        $size = "size=\"{$l}\" ";
      }
      break;
    
    case 'password' :
      // this implies a string
      $dtype = $type;
      $itype = $dtype;
      $l = strval ( $length );
      $size = "size=\"{$l}\" ";
      break;
    case 'submit' :
      $dtype = $type;
      $itype = $dtype;
      break;
    default :
      $dtype = 'unexpected';
  }
  $val = ($type != 'submit') ? get_arrval ( $value, $name, '' ) : $value;
  $out = <<<"END"
<input name="{$name}" id="{$name}"
type="{$itype}" class="{$dtype} {$class}" value="" autocomplete="off" {$size} >
END;
  
  return $out;

}

function BUTTON($name, $rtnval, $text, $type, $style = "", $class = "") {
  return "<button name=\"{$name}\" type=\"$type\" value=\"{$rtnval}\">{$text}</button>";

}

function TR($strx, $class = '') {
  return "<tr class=\"{$class}\" >" . implode ( "\n", $strx ) . "</tr>";

}

function TD($str, $class = '') {
  return "<td class=\"{$class}\" >{$str}</td>";

}

function TH($str, $class = '') {
  return "<th class=\"{$class}\" >{$str}</th>";

}

function IMG($src, $class = '') {
  return "<img src=\"{$src}\" class=\"{$class}\" /> ";

}

function SELECT_LIVE($arr, $name, $value, $class = '') {
  $opts = array ();
  $opts ['ALL'] = 'ALL';
  foreach ( $arr as $a ) {
    $opts [$a] = $a;
  }
  return OPTIONS ( $name, $opts, $value );

}

function OPTIONS($varname, $optvals, $value) {
  /**
   * Depending on the number of optvals we choose select or Radio buttons
   *
   * 3 or less gets Radio
   */
  $ct = count ( $optvals );
  if (count ( $optvals ) <= 3) {
    return RADIO ( $varname, $optvals, $value );
  } else {
    return SELECT ( $varname, $optvals, $value );
  }

}

function OPTIONS_CALC($varname, $optvals, $value, $score) {
  /**
   * Depending on the number of optvals we choose select or Radio buttons
   *
   * 3 or less gets Radio
   */
  $ct = count ( $optvals );
  if (count ( $optvals ) <= 3) {
    return RADIO_CALC ( $varname, $optvals, $value, $score );
  } else {
    return SELECT ( $varname, $optvals, $value );
  }

}

/**
 * These implement widgets each of which is responsible for an instance
 * of an input area on the screen
 */
function getYN($t) {
  return array ( // "{$t['Select']} ..." => '-',
      "{$t['Yes']}" => 'YES',
      "{$t['No']}" => 'NO'
  );

}

function widget_select_yn($varname, $value, $t) {
  $optvals = getYN ( $t );
  /*
   * array(//"{$t['Select']} ..." => '-', "{$t['Yes']}" => 'YES', "{$t['No']}" => 'NO');
   */
  return OPTIONS ( $varname, $optvals, $value );

}

/*
 * function widget_radio_yn($varname, $value, $t) { $optvals = array(//"{$t['Select']} ..." => '-', "{$t['Yes']}" => 'Y', "{$t['No']}" => 'N'); return OPTIONS($varname, $optvals, $value); }
 */
function getYNP($t) {
  return array ( // "{$t['Select']} ..." => '-',
      "{$t['Yes']}" => 'YES',
      "{$t['Partial']}" => 'PARTIAL',
      "{$t['No']}" => 'NO'
  );

}

function widget_select_ynp($varname, $value, $t) {
  $optvals = getYNP ( $t );
  /*
   * array(//"{$t['Select']} ..." => '-', "{$t['Yes']}" => 'YES', "{$t['Partial']}" => 'PARTIAL', "{$t['No']}" => 'NO');
   */
  return OPTIONS ( $varname, $optvals, $value );

}

function widget_select_ynp_ro($varname, $value, $t) {
  /*
   * This is a display for calculated choices
   */
  $ro_char = "{$varname}_ynp";
  $v_ro_char = get_arrval ( $value, $ro_char, 'NO' );
  $ro_num = "{$varname}_num";
  $v_ro_num = get_arrval ( $value, $ro_num, 0 );
  $out = <<<"END"
<input class="ro" name="{$ro_char}" id={$ro_char}"
       type="text" readonly="readonly" value="{$v_ro_char}" length=3>
<input class="ro" name="{$ro_num}" id={$ro_num}"
       type="text" readonly="readonly" value="{$v_ro_num}" length=3>
END;
  return $out;

}

function widget_select_ynp_calc($varname, $value, $t, $score) {
  $optvals = getYNP ( $t );
  /*
   * array(//"{$t['Select']} ..." => '-', "{$t['Yes']}" => 'YES', "{$t['Partial']}" => 'PARTIAL', "{$t['No']}" => 'NO');
   */
  return OPTIONS_CALC ( $varname, $optvals, $value, $score );

}

function widget_select_wp($varname, $value, $t) {
  $optvals = array ( // "{$t['Select']} ..." => '-',
      "{$t['Personal']}" => 'PERSONAL',
      "{$t['Work']}" => 'WORK'
  );
  return OPTIONS ( $varname, $optvals, $value );

}

function getYNI($t) {
  return array ( // "{$t['Select']} ..." => '-',
      "{$t['Yes']}" => 'YES',
      "{$t['No']}" => 'NO',
      "{$t['Insufficient Data']}" => 'I'
  );

}

function widget_select_yni($varname, $value, $t) {
  $optvals = getYNI ( $t );
  /*
   * array(//"{$t['Select']} ..." => '-', "{$t['Yes']}" => 'YES', "{$t['No']}" => 'NO', "{$t['Insufficient Data']}" => 'I');
   */
  return OPTIONS ( $varname, $optvals, $value );

}

function getPW($t) {
  return array ( // "{$t['Select']} ..." => '-',
      "{$t['Personal']}" => 'P',
      "{$t['Work']}" => 'W'
  );

}

function widget_select_pw($varname, $value, $t) {
  $optvals = getPW ( $t );
  /*
   * array ( // "{$t['Select']} ..." => '-', "{$t['Personal']}" => 'P', "{$t['Work']}" => 'W' );
   */
  return OPTIONS ( $varname, $optvals, $value );

}

function getYNA($t) {
  return array ( // "{$t['Select']} ..." => '-',
      "{$t['Yes']}" => 'YES',
      "{$t['No']}" => 'NO',
      "{$t['N/A']}" => 'N/A'
  );

}

function widget_select_yna($varname, $value, $t) {
  $optvals = getYNA ( $t );
  /*
   * array ( // "{$t['Select']} ..." => '-', "{$t['Yes']}" => 'Y', "{$t['No']}" => 'N', "{$t['N/A']}" => 'A' );
   */
  logit("YNA: ". print_r($optvals, true));
  return OPTIONS ( $varname, $optvals, $value );

}

function getStars($t) {
  return array ( // "{$t['Select']}" => '-',
      "{$t['Not Audited']}" => 'N',
      "0 {$t['Stars']}" => '0',
      "1 {$t['Star']}" => '1',
      "2 {$t['Stars']}" => '2',
      "3 {$t['Stars']}" => '3',
      "4 {$t['Stars']}" => '4',
      "5 {$t['Stars']}" => '5'
  );

}

function getStarsRev($t) {

}

function widget_select_stars($varname, $value, $t) {
  $optvals = getStars ( $t );
  /*
   * array(//"{$t['Select']}" => '-',
   * "{$t['Not Audited']}" => 'N',
   * "0 {$t['Stars']}" => '0',
   * "1 {$t['Star']}" => '1',
   * "2 {$t['Stars']}" => '2',
   * "3 {$t['Stars']}" => '3',
   * "4 {$t['Stars']}" => '4',
   * "5 {$t['Stars']}" => '5');
   */
  return OPTIONS ( $varname, $optvals, $value );

}

function getLevels($t) {
  return array ( // " {$t['Select']} ..." => '-',
      "{$t['National']}" => 'NATIONAL',
      "{$t['Reference']}" => 'REFERENCE',
      "{$t['Regional']}" => 'REGIONAL',
      "{$t['District']}" => 'DISTRICT',
      "{$t['Zonal']}" => 'ZONAL',
      "{$t['Field']}" => 'FIELD'
  );

}

function widget_select_lablevel($varname, $value, $t) {
  $optvals = getLevels ( $t );
  /*
   * array(//"{$t['Select']} ..." => '-',
   * "{$t['National']}" => 'NATIONAL',
   * "{$t['Reference']}" => 'REFERENCE',
   * "{$t['Regional']}" => 'REGIONAL',
   * "{$t['District']}" => 'DISTRICT',
   * "{$t['Zonal']}" => 'ZONAL',
   * "{$t['Field']}" => 'FIELD' );
   */
  return OPTIONS ( $varname, $optvals, $value);

}

function getAffiliations($t) {
  return array ( // "{$t['Select']} ..." => '-',
      "{$t['Public']}" => 'PUBLIC',
      "{$t['Hospital']}" => 'HOSPITAL',
      "{$t['Private']}" => 'PRIVATE',
      "{$t['Research']}" => 'RESEARCH',
      "{$t['Non-hospital outpatient clinic']}" => 'NONHOSPITAL',
      "{$t['Other - please specify']}" => 'OTHER'
  );

}

function widget_select_labaffil($varname, $value, $t) {
  $optvals = getAffiliations ( $t );
  /*
   * array(//"{$t['Select']} ..." => '-', "{$t['Public']}" => 'PUBLIC', "{$t['Hospital']}" => 'HOSPITAL', "{$t['Private']}" => 'PRIVATE', "{$t['Research']}" => 'RESEARCH', "{$t['Non-hospital outpatient clinic']}" => 'NONHOSPITAL', "{$t['Other - please specify']}" => 'OTHER' );
   */
  return OPTIONS ( $varname, $optvals, $value );

}

function getSLMTAStatus($t) {
  return array ( // "{$t['Select']} ..." => '-',
      "{$t['Official ASLM Audit']}" => 'ASLM',
      "{$t['SLMTA Audit']}" => 'SLMTA',
      "{$t['Base Line Assessment']}" => 'BASELINE',
      "{$t['Non SLMTA Audit']}" => 'NONSLMTA'
  );

}

function widget_select_slmtastatus($varname, $value, $t) {
  $optvals = getSLMTAStatus ( $t );
  /*
   * array(//"{$t['Select']} ..." => '-', "{$t['Official ASLM Audit']}" => 'ASLM', "{$t['SLMTA Audit']}" => 'SLMTA', "{$t['Base Line Assessment']}" => 'BASELINE', "{$t['Non SLMTA Audit']}" => 'NONSLMTA' );
   */
  return OPTIONS ( $varname, $optvals, $value );

}

function widget_dt($name, $value, $length = 14) {
  return INPUT ( $name, $value, 'date', $length );

}

function widget_text100($name, $value) {
  return INPUT ( $name, $value, 'string', 100 );

}

function widget_text255($name, $value) {
  return INPUT ( $name, $value, 'string', 255 );

}

function widget_integer($name, $value, $length = 0) {
  return INPUT ( $name, $value, 'integer', $length );

}

/**
 * These are the representations of a row on the screen
 */
function partial_stars($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row['varname'];
  $stars = widget_select_stars ( "{$name}_stars", $value, $t );
  $out = <<<"END"
<!--table style="width:100%;"><tr-->
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:left;">
  {$stars}
</div>
    </div>
<!--/tr></table-->
END;
  return $out;

}

function partial_string_field($row, $value, $t) {
  $name = $row ['varname'];
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $stringf = INPUT ( $name, $value, 'string', 55, 'width:100%;', '' );
  $out = <<<"END"
<!--table style="width:100%;"><tr-->
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:left;">
  {$stringf}
</div>
<!--/tr></table-->
</div>
END;
  return $out;

}

function partial_prof_info($row, $value, $t) {
  $name = $row ['varname'];
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $intf = INPUT ( $name, $value, 'integer', 3, 'margin-right:10px;', '' );
  $mc_yni = widget_select_yni ( "{$name}_yni", $value, $t );
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:right;">
  {$intf} {$mc_yni}
</div>
</div>
END;
  return $out;

}

function partial_prof_info_yn($row, $value, $t) {
  $name = $row ['varname'];
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $mc_yn = widget_select_yn ( "{$name}_yn", $value, $t );
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:right;">
  {$mc_yn}
</div>
</div>
END;
  return $out;

}

function partial_integer_field($row, $value, $t) {
  $name = $row ['varname'];
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $intf = INPUT ( $name, $value, 'integer', 0, 'width:100%;', '' );
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:right;">
  {$intf}
</div>
</div>
END;
  return $out;

}

function partial_text_field($row, $value, $t) {
  $name = $row ['varname'];
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $tarea = TEXTAREA ( "{$name}_comment", $value, "width:395px;height:50px;margin-top:5px;" );
  $out = <<<"END"
<div style="width:100%;">
<div style="display:inline-block;vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left">
  {$text}
</div>
<div style="display:inline-block;vertical-align:top;width:400px;float:left;">
  {$tarea}
</div>
</div>
END;
  return $out;

}

function partial_date_field($row, $value, $t) {
  $name = $row ['varname'];
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $datef = INPUT ( $name, $value, 'date', 14, '', '' );
  //$script = '<script> $(function() {$( "' . "#{$name}" . '" ).datepicker();});</script>';
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;;width:400px;float:left;">
  {$datef}
</div>
</div>
END;
  return $out;

}

function partial_tel_type($row, $value, $t) {
  $name = $row ['varname'];
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $pwf = widget_select_pw ( $name, $value, $t );
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top; ;width:400px;float:left;">
  {$pwf}
</div>
</div>
END;
  return $out;

}

function partial_sec_head($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="font-size:18px;font-weight: bold;text-transform:uppercase;padding: 2px 4px;">
<div style="">
<div style="vertical-align:top;"> {$heading}</div>
</div>
</td>
</tr></table>
END;
  
  return $out;

}

function partial_sec_head_lab($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $info = $row ['info'];
  $name = $row ['varname'];
  $max_score = $row ['score'];
  $widget_nyp = widget_select_ynp_calc ( $name, $value, $t, $max_score );
  // widget_select_ynp($name, $value, $t);; // widget_nyp_ro($name, $value);
  $head = ($heading) ? "{$heading}<br />" : "";
  $tarea = TEXTAREA ( "{$name}_comment", $value, "width:100%;height:50px;margin-top:5px;" );
  $out = <<<"END"
  <table style="width:100%;"><tr>
      <td style="padding: 2px 4px;">
        <div style="display:inline-block;width:100%x;vertical-align:top;">
          <div style="width:788px;display:inline;">
            <div style="display:inline;font-weight:bold;width:25px;vertical-align:top;">{$prefix}</div>
            <div style="display:inline-block;width:755px;">
              <div style="text-decoration:underline;font-weight:bold;display:inline;">{$head}</div>
              <div style="vertical-align:top;display:inline;">{$text}
              </div>
            </div>
      </td>
  </tr></table>
END;
  
  return $out;

}

function partial_sec_head_top($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $out = <<<"END"
<div style="width:100%;border:1px solid #ccc;background-color:#f0f0f0;padding: 4px;font-size:14px;">
<b>{$heading}</b> {$text}
</div>
END;
  
  return $out;

}

function partial_sec_head_small($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="font-size:14px;font-weight: bold;padding: 2px 4px;">
<div style="">
<div style="vertical-align:top;">{$prefix} {$heading}</div>
</div>
</td>
</tr></table>
END;
  
  return $out;

}

function partial_info_i($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="font-size:14px;font-style:italic;padding: 2px 4px;">
<div style="vertical-align:top;">{$text}</div>
</td>
</tr></table>
END;
  
  return $out;

}

function partial_info_bn($row, $value, $t) {
  /**
   * This implements full width information header with
   * text in bold and normal font.
   * bold text is from field heading
   * normal test is from field text
   */
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="font-size:14px;padding: 2px 4px;">
    <div style="vertical-align:top;"><b>{$heading}</b> {$text}</div>
</td>
</tr></table>
END;
  
  return $out;

}

function partial_sub_sec_head($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $info = $row ['info'];
  $name = $row ['varname'];
  $max_score = $row ['score'];
  $widget_nyp = widget_select_ynp_calc ( $name, $value, $t, $max_score );
  // widget_select_ynp($name, $value, $t);; // widget_nyp_ro($name, $value);
  $head = ($heading) ? "{$heading}<br />" : "";
  $tarea = TEXTAREA ( "{$name}_comment", $value, "width:100%;height:50px;margin-top:5px;" );
  $tareanc = TEXTAREA ( "{$name}_note", $value, "width:100%;height:50px;margin-top:6px;" , 'nc');
  $ncval = get_arrval ( $value, $name.'_nc', 'F' );
  $checked = '';
  $scoreval = get_arrval ( $value, $name.'_score', 0 );
  if ($ncval == 'T') {
    $checked = 'checked';
    $vis = '';
  } else {
    $ncval = 'F';
    $vis = "display:none;";
  }
  $out = <<<"END"
  <table style="width:100%;"><tr>
      <td style="padding: 2px 4px;vertical-align:top;">
        <div style="display:inline-block;width:450px;vertical-align:top;">
          <div style="width:448px;display:inline;">
            <div style="display:inline;font-weight:bold;width:25px;vertical-align:top;">{$prefix}</div>
            <div style="display:inline-block;width:405px;">
              <div style="text-decoration:underline;font-weight:bold;display:inline;">{$head}</div>
              <div style="vertical-align:top;display:inline;">{$text}<br />
              <div style="width:100%;text-align:right;margin-top:5px;">
            <label><input type="checkbox" id="{$name}" name="{$name}_cb" value="T" {$checked} style="margin-right:8px;"
              onclick="toggleNCBox(this);">Non-Compliant</label>
                <input type="hidden" id="{$name}_nc" name="{$name}_nc" value="{$ncval}"/>
                </div></div>
            </div>
            <div
            style="font-style:italic;font-weight:bold;font-size:10px;margin-top:5px;">{$info}</div>
          </div>
      </td>
      <td style="vertical-align:top;padding: 2px 4px;width:350px;">
        <div style="margin-right:5px;display:inline;">{$widget_nyp}</div>
<div style="display:inline;">
<input class="ro" name="{$name}_score" id="{$name}_score" value="{$scoreval}"
       type="text"  size="2">
 / <b>{$max_score}</b></div>
        <div>{$tarea}</div>
     <div id="div{$name}_nc" style="{$vis}" >
        Notes:<br />
        {$tareanc}
        </div>
      </td>
  </tr></table>
END;
  
  return $out;

}

function partial_sub_sec_head_ro($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $info = $row ['info'];
  $name = $row ['varname'];
  $max_score = $row ['score'];
  $widget_nyp_ro = widget_select_ynp_ro ( $name, $value, $t ); // 'N/Y/P FIXME';
  $ynp_ro = "{$name}_ynp";
  $this_score = get_arrval ( $value, $ynp_ro, 0 ); // '# FIXME';
  $head = ($heading) ? "{$heading}<br />" : "";
  logit("SRO: ". print_r($row, true));
  $out = <<<"END"
  <table style="width:100%;"><tr>
      <td style="padding: 2px 4px;">
        <div style="display:inline-block;width:450px;vertical-align:top;">
          <div style="width:448px;display:inline;">
            <div style="display:inline;font-weight:bold;width:25px;vertical-align:top;">{$prefix}</div>
            <div style="display:inline-block;width:405px;">
              <div style="text-decoration:underline;font-weight:bold;display:inline;">{$head}</div>
              <div style="vertical-align:top;display:inline;">{$text}
              </div>
            </div>
            <div
            style="font-style:italic;font-weight:bold;font-size:10px;margin-top:5px;">{$info}</div>
          </div>
      </td>
      <td style="vertical-align:top;padding: 2px 4px;width:350px;">
      <div style="margin-right:5px;display:inline;">{$widget_nyp_ro}</div>
      <div style="display:inline;">
      <input class="ro" name="{$name}_score" id="{$name}_score" value=""
       type="text"  size="2"> / <b>{$max_score}</b></div>
      </td>
  </tr></table>
END;
  
  return $out;

}

function partial_sec_element_yn($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  if ($heading) {
    $heading = $heading . '<br />';
  }
  $text = $row ['text'];
  $info = $row ['info'];
  $name = $row ['varname'];
  $mc_yn = widget_select_yn ( "{$varname}_yn", $value, $t );
  $tarea = TEXTAREA ( "{$name}_comment", $value, "width:100%;height:50px;" );
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding: 2px 4px;">
<div style="display:inline-block;vertical-align:top;">
<div style="width:325px;">
<div>
    <div style="vertical-align:top;display:inline;">{$prefix}</div>
    <div style="text-decoration:underline;font-weight:bold;vertical-align:top;display:inline;">{$heading}</div>
<div style="vertical-align:top;display:inline;">{$text}</div>
</div>
</div>
<div style="font-style:italic;font-weight:bold;font-size:10px;margin-top:4px;">{$info}</div>
</div>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
<div style="">{$mc_yn} </div>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$tarea}
</td>
</tr></table>
END;
  
  return $out;

}

function partial_sec_element_yna($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  if ($heading) {
    $heading = $heading . '<br />';
  }
  $text = $row ['text'];
  $info = $row ['info'];
  $name = $row ['varname'];
  $mc_yna = widget_select_yna ( "{$name}_yna", $value, $t );
  $tarea = TEXTAREA ( "{$name}_comment", $value, "width:100%;height:50px;margin-top:6px;" );
  $tareanc = TEXTAREA ( "{$name}_note", $value, "width:100%;height:50px;margin-top:6px;" , 'nc');
  $ncval = get_arrval ( $value, $name.'_nc', 'F' );
  $checked = '';
  if ($ncval === 'T') {
    $checked = 'checked';
    $vis = '';
  } else {
    $ncval = 'F';
    $vis = "display:none;";
  }
  $out = <<<"END"
  <table style="width=100%;"><tr>
      <td style="vertical-align:top;padding: 2px 4px;width:450px;">
        <div style="display:inline-block;vertical-align:top;">
          <div style="width:425px;">
            <div style="width:100%">
              <div style="vertical-align:top;display:inline;">{$prefix}</div>
              <div style="text-decoration:underline;font-weight:bold;vertical-align:top;display:inline;">{$heading}</div>
              <div style="vertical-align:top;display:inline;">{$text}<br />
              <div style="width:100%;text-align:right;margin-top:5px;">
            <label><input type="checkbox" id="{$name}" name="{$name}_cb" value="T" {$checked} style="margin-right:8px;"
              onclick="toggleNCBox(this);">Non-Compliant</label>
                <input type="hidden" id="{$name}_nc" name="{$name}_nc" value="{$ncval}"/>
                </div></div>
            </div>
          </div>
          <div style="font-style:italic;font-weight:bold;font-size:10px;margin-top:4px;">{$info}</div>
        </div>
      </td>
      <td  style="vertical-align:top;padding: 2px 4px;width:350px;">
        <div style="">{$mc_yna} </div>
        {$tarea}
        <div id="div{$name}_nc" style="{$vis}" >
        Notes:<br />
        {$tareanc}
        </div>
      </td>
</tr></table>
END;

        return $out;

}

function partial_sec_element($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  if ($heading) {
    $heading = $heading . '<br />';
  }
  $text = $row ['text'];
  $info = $row ['info'];
  $name = $row ['varname'];
  $mc_yn = widget_select_yn ( "{$name}_yn", $value, $t );
  $tarea = TEXTAREA ( "{$name}_comment", $value, "width:100%;height:50px;margin-top:6px;" );
  $tareanc = TEXTAREA ( "{$name}_note", $value, "width:100%;height:50px;margin-top:6px;" , 'nc');
  $ncval = get_arrval ( $value, $name.'_nc', 'F' );
  $checked = '';
  if ($ncval === 'T') {
    $checked = 'checked';
    $vis = '';
  } else {
    $ncval = 'F';
    $vis = "display:none;";
  }
  $out = <<<"END"
  <table style="width=100%;"><tr>
      <td style="vertical-align:top;padding: 2px 4px;width:450px;">
        <div style="display:inline-block;vertical-align:top;">
          <div style="width:425px;">
            <div style="width:100%">
              <div style="vertical-align:top;display:inline;">{$prefix}</div>
              <div style="text-decoration:underline;font-weight:bold;vertical-align:top;display:inline;">{$heading}</div>
              <div style="vertical-align:top;display:inline;">{$text}<br />
              <div style="width:100%;text-align:right;margin-top:5px;">
            <label><input type="checkbox" id="{$name}" name="{$name}_cb" value="T" {$checked} style="margin-right:8px;"
              onclick="toggleNCBox(this);">Non-Compliant</label>
                <input type="hidden" id="{$name}_nc" name="{$name}_nc" value="{$ncval}"/>
                </div></div>
            </div>
          </div>
          <div style="font-style:italic;font-weight:bold;font-size:10px;margin-top:4px;">{$info}</div>
        </div>
      </td>
      <td  style="vertical-align:top;padding: 2px 4px;width:350px;">
        <div style="">{$mc_yn} </div>
        {$tarea}
        <div id="div{$name}_nc" style="{$vis}" >
        Notes:<br />
        {$tareanc}
        </div>
      </td>
</tr></table>
END;
  
  return $out;

}

function partial_lablevel($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $mc_lab_level = widget_select_lablevel ( $name, $value, $t );
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding: 2px 7px 2px 4px;width:400px;text-align:right;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;width:400px;">
{$mc_lab_level}
</td>
</tr></table>
END;
  
  return $out;

}

function partial_slmta_status($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $mc_slmta_status = widget_select_slmtastatus ( $name, $value, $t );
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding: 2px 7px 2px 4px;width:400px;text-align:right;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;width:400px;">
{$mc_slmta_status}
</td>
</tr></table>
END;
  
  return $out;

}

function partial_labaffil($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $mc_lab_affil = widget_select_labaffil ( $name, $value, $t );
  
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding: 2px 7px 2px 4px;width:400px;text-align:right;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;width:400px;">
{$mc_lab_affil}
</td>
</tr></table>
END;
  
  return $out;

}

function partial_date($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $dt = widget_dt ( $name, $value );
  $script = '<script> $(function() {$( "' . "#{$name}" . '" ).datepicker();});</script>';
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding: 2px 4px;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$dt} {$script}
</td>
</tr></table>
END;
  
  return $out;

}

function partial_text($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $tarea = TEXTAREA ( $name, $value );
  $out = <<<"END"
<table style="width:100%;"><tr>
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$tarea}
</td>
</tr></table>
END;
  
  return $out;

}

function partial_tab_head3($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding: 2px 4px;">
<i>{$prefix}</i>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
<i>{$heading}</i>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
<i>{$text}</i>
</td>
</tr></table>
END;
  
  return $out;

}

function partial_pinfo($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $smallint = widget_integer ( "{$name}_num", $value, 4 );
  $mc_yni = widget_select_yni ( "{$name}_yni", $value, $t );
  $out = <<<"END"
  <table style="width:100%;"><tr>
      <td style="vertical-align:top;padding: 2px 4px;width:500px;">
        <div style="float:left;">{$text}</div>
      <!--/td>
      <td style="vertical-align:top;padding: 2px 4px;width:100px;"-->
        <div style="float:right;">{$smallint}</div>
      </td>
      <td style="vertical-align:top;padding: 2px 4px;width:300px;">
        {$mc_yni}
      </td>
</tr></table>
END;
  
  return $out;

}

function partial_pinfo2_i($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $smallint = widget_integer ( "{$name}_num", $value );
  $mc_yn = widget_select_yn ( "{$name}_yn", $value, $t );
  $out = <<<"END"
  <table style="width:100%;">
    <tr>
      <td style="vertical-align:top;padding: 2px 4px;width:500px;">
        &nbsp;&nbsp;<i>{$text}</i>
      </td>
      <td style="vertical-align:top;padding: 2px 4px;width:300px;">
        {$mc_yn}
      </td>
</tr></table>
END;
  
  return $out;

}

function partial_pinfo2($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $smallint = widget_integer ( "{$name}_num", $value );
  $mc_yn = widget_select_yn ( "{$name}_yn", $value, $t );
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding: 2px 4px;">
  {$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$mc_yn}
</td></tr><table>
END;
  
  return $out;

}

function partial_criteria_1_heading($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $out = <<<"END"
  <table style="width:100%;">
  <tr>
    <td width="7%" rowspan="2" class="centertopbold">{$prefix}</td>
    <td rowspan="2" class="title">
      {$heading}
    </td>
    <td width="21%" colspan=3 class="centertopbold">{$t['FREQUENCY']}</td>
  </tr>
  <tr>
    <td width="7%" class="centertopbold">{$t['Daily']}</td>
	  <td width="7%" class="centertopbold">{$t['Weekly']}</td>
	  <td class="centerbold">{$t['With Every Run']}</td>
  </tr>
  </table>
END;
  
  return $out;

}

function partial_criteria_1_values($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $i11 = widget_integer ( "{$name}_qnt_d", $value, 4 );
  $i12 = widget_integer ( "{$name}_qnt_w", $value, 4 );
  $i13 = widget_integer ( "{$name}_qnt_er", $value, 4 );
  $i21 = widget_integer ( "{$name}_sqt_d", $value, 4 );
  $i22 = widget_integer ( "{$name}_sqt_w", $value, 4 );
  $i23 = widget_integer ( "{$name}_sqt_er", $value, 4 );
  $i31 = widget_integer ( "{$name}_qlt_d", $value, 4 );
  $i32 = widget_integer ( "{$name}_qlt_w", $value, 4 );
  $i33 = widget_integer ( "{$name}_qlt_er", $value, 4 );
  $out = <<<"END"
  <table style="width:100%;">
  <tr>
    <td width="7%" rowspan="4" class="centertopbold">{$prefix}</td>
    <td colspan="4" class="title">{$heading}</td>
    </tr>
    <tr>
      <td  class="tests">{$t['Quantitative tests']}</td>
      <td width="7%">{$i11}</td>
	    <td width="7%">{$i12}</td>
	    <td width="7%">{$i13}</td>
	  </tr>
    <tr>
      <td class="tests">{$t['Semi-quantitative tests']}</td>
      <td>{$i21}</td>
	    <td>{$i22}</td>
	    <td>{$i23}</td>
	  </tr>
    <tr>
      <td class="tests">{$t['Qualitative tests']}</td>
      <td>{$i31}</td>
	    <td>{$i32}</td>
	    <td>{$i33}</td>
	  </tr>
  </table>
END;
  
  return $out;

}

function partial_com_and_rec($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $tarea = TEXTAREA ( $name, $value, $style = "width:100%;height:400px;" );
  $out = <<<"END"
  <table style="width:100%;">
  <tr>
    <td>
    <div class="bigtitlei">{$heading}</div>
    {$tarea}
    </td>
  </tr>
  </table>
END;
  
  return $out;

}

function partial_criteria_2_heading($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $out = <<<"END"
  <table style="width:100%;">
  <tr>
    <td width="7%" rowspan="2" class="centertopbold">{$prefix}</td>
    <td rowspan="2" class="title">
      {$heading}
    </td>
        <td width="12%" class="centertop">{$t['Date of panel receipt']}</td>
    <td width="16%" class="centertop">{$t['Were results reported within 15 days?']}</td>
    <td width="10%" class="centertopbold">{$t['Results & % Correct']}</td>
  </tr>
  </table>
END;
  
  return $out;

}

function partial_panel_heading($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $out = <<<"END"
  <table style="width:100%;">
  <tr>
    <td width="7%"></td>
    <td class="title">
      {$heading}
    </td>
    <td width="10%" class="percent">%</td>
  </tr>
  </table>
END;
  
  return $out;

}

function partial_panel_heading2($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $sfield = widget_integer ( "{$name}_name", $value, 32 );
  $out = <<<"END"
  <table style="width:100%;">
  <tr>
    <td width="7%"></td>
    <td class="title">
      {$heading} {$sfield}
    </td>
    <td width="10%" class="percent">%</td>
  </tr>
  </table>
END;
  
  return $out;

}

function partial_panel_result($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $smallint = widget_integer ( "{$name}_num", $value, 4 );
  $mc_yn = widget_select_yn ( "{$name}_yn", $value, $t );
  $dt = widget_dt ( "{$name}_dt", $value, 10 );
  $script = '<script> $(function() {$( "' . "#{$name}_dt" . '" ).datepicker();});</script>';
  $out = <<<"END"
  <table style="width:100%;">
  <tr>
    <td width="7%" class="centerbold">
      {$prefix}
    </td>
    <td class="panel">
      {$heading}
    </td>
    <td width="12%">
      {$dt} {$script}
    </td>
    <td width="16%">
      {$mc_yn}
    </td>
    <td width="10%">
      {$smallint}
    </td>
  </tr>
  </table>
END;
  
  return $out;

}

function partial_info($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $tarea = TEXTAREA ( $name, $value, $style = "width:100%;height:250px;" );
  $out = <<<"END"
  <table style="width:100%;">
  <tr>
     <td>
       {$tarea}
     </td>
  </tr>
  </table>
END;
  
  return $out;

}

function partial_action_plan_heading($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $out = <<<"END"
  <table style="width:100%;">
  <tr>
    <td width="45%" class="centertopbold">
      {$heading}
    </td>
    <td width="20%" class="centertopbold">Responsible Persons</td>
    <td width="10%" class="centertopbold">Timeline</td>
    <td class="centertopbold">Signature</td>
  </tr>
  </table>
END;
  return $out;

}

function partial_action_plan_data($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $name = $row ['varname'];
  $input_style = "width:100%;height:50px;";
  $item = TEXTAREA ( "{$name}_item", $value, $input_style );
  $person = TEXTAREA ( "{$name}_person", $value, $input_style );
  $time = TEXTAREA ( "{$name}_time", $value, $input_style );
  $sign = TEXTAREA ( "{$name}_item", $value, $input_style );
  $out = <<<"END"
  <table style="width:100%;">
  <tr>
    <td width="45%">{$item}</td>
    <td width="20%">{$person}</td>
    <td width="10%">{$time}</td>
    <td >{$sign}</td>
  </tr>
  </table>
END;
  
  return $out;

}

function partial_sec_total($row, $value, $t) {
  return '';

}

function partial_sec_element_info($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  // $name = $row['varname'];
  $out = <<<"END"
  {$text}
END;
  
  return $out;

}

function partial_sec_elem_info($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $out = <<<"END"
<div style="width:100%;border:1px solid #ccc;background-color:#f0f0f0;padding:4px;font-size:14px;">
  <b>{$heading}</b> {$text}
</div>
END;
  return $out;

}

function partial_sub_sec_info($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $out = <<<"END"
<div style="width:100%;border:1px solid #ccc;background-color:#f0f0f0;padding: 4px;font-size:14px;">
  <i><b>{$heading}</b> {$text}</i>
</div>
END;
  return $out;

}

function partial_sec_sec_head($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  return '';

}

function partial_part_head($row, $value, $t) {
  $prefix = $row ['prefix'];
  $heading = $row ['heading'];
  $text = $row ['text'];
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="font-size:18px;text-transform:uppercase;padding: 2px 4px;">
<div style="">
<div style="vertical-align:top;"> {$heading}</div>
</div>
</td>
</tr></table>
END;
  return $out;

}

function get_lang_text($base, $default, $sp_lang) {
  /**
   * $base contains the original text
   * $default contain default text from lang
   * $sp_lang contains language specific text - but is not always available
   */
  logit ( "{$base} -- {$default} -- {$sp_lang}" );
  $out = '';
  $out = $base;
  if ($default) {
    $out = $default;
  }
  if ($sp_lang) {
    $out = $sp_lang;
  }
  return $out;

}

/**
 * We render the rows here
 */
function getTranslatables($tword) {
  $words = array (
      'Yes',
      'No',
      'N/A',
      'Partial',
      'Select',
      'Insufficient Data',
      'Personal',
      'Work',
      'Insufficient data',
      'Not Audited',
      'Star',
      'Stars',
      'National',
      'Reference',
      'Regional',
      'District',
      'Zonal',
      'Field',
      'Public',
      'Hospital',
      'Private',
      'Research',
      'Non-hospital outpatient clinic',
      'Other - please specify',
      'FREQUENCY',
      'Daily',
      'Weekly',
      'With Every Run',
      'Quantitative tests',
      'Semi-quantitative tests',
      'Qualitative tests',
      'Date of panel receipt',
      'Were results reported within 15 days?',
      'Results & % Correct',
      'Official ASLM Audit',
      'SLMTA Audit',
      'Base Line Assessment',
      'Non SLMTA Audit'
  );
  $tlist = get_common_words_translated ( $tword, $words );
  return $tlist;

}

function calculate_page($rows, $value, $tword) {
  /**
   * Result of the query to get all template rows sorted in order
   *
   * Soon we will add a field which will partition the whole template into
   * groups, only one of which is displayed at a time.
   *
   * It will be possible to randomly pull up any group once the basic
   * profile informaiton for this audit has been saved.
   */
  // This is a list of words used often - get it translated only once
  // $rows contains translated list of text
  /*
   * $words = array('Yes', 'No', 'Partial', 'Select', 'Insufficient Data',
   * 'Personal', 'Work', 'Insufficient data', 'Not Audited', 'Star', 'Stars',
   * 'National', 'Reference', 'Regional', 'District', 'Zonal', 'Field',
   * 'Public', 'Hospital', 'Private', 'Research', 'Non-hospital outpatient clinic',
   * 'Other - please specify', 'FREQUENCY', 'Daily', 'Weekly', 'With Every Run',
   * 'Quantitative tests', 'Semi-quantitative tests', 'Qualitative tests',
   * 'Date of panel receipt', 'Were results reported within 15 days?',
   * 'Results & % Correct', 'Official ASLM Audit', 'SLMTA Audit',
   * 'Base Line Assessment', 'Non SLMTA Audit');
   * $tlist = get_common_words_translated($tword, $words);
   */
  $tlist = getTranslatables ( $tword );
  $tout = array ();
  $tout [] = '<table border=0 style="width:800px;">';
  // $tout[] = '<tr><td style="width:359px;"></td><td style="width:164px;"></td><
  // td style="width:309px;"></td><tr>';
  foreach ( $rows as $row ) {
    $type = $row ['row_type'];
    $arow = array ();
    $arow ['prefix'] = get_lang_text ( $row ['prefix'], $row ['lpdefault'], $row ['lplang'] );
    // $row['prefix'];
    $arow ['heading'] = get_lang_text ( $row ['heading'], $row ['lhdefault'], $row ['lhlang'] );
    $arow ['text'] = get_lang_text ( $row ['text'], $row ['ltdefault'], $row ['ltlang'] );
    $arow ['varname'] = $row ['varname'];
    $arow ['info'] = $row ['info'];
    $arow ['score'] = $row ['score'];
    $tout [] = '<tr ><td class="bpad">' . call_user_func ( "partial_{$type}", $arow, $value, $tlist ) . '</td></tr>';
  }
  $tout [] = '</table>';
  return $tout;

}
