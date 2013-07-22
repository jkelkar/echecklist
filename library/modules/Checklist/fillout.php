<?php //-*- coding: utf-8 -*-
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
  logit("GA: " . gettype($arr) . "  {$k}");
  $callers=debug_backtrace();
  logit("TRACE: {$callers[1]['function']}");
  if (gettype($arr) == 'string') {
    logit("Str: {$arr}");
  }
  return key_exists($k, $arr) ? $arr[$k] : $default;
}

function get_common_words_translated($value, $words) {
  $trans_list = array();
  foreach($words as $word) {
    $trans_list[$word] = get_arrval($value, $word, $word);
  }
  return $trans_list;
}

/**
 * these implement low level html code generators
 */
function SELECT($name, $optvals, $value)
{
  //$log = new KLogger("/var/log/log.txt", KLogger::DEBUG);
  if (count($optvals) == 0) {
    throw new Exception('Optvals has no elements', 0);
  }
  $optout = array();
  $val = get_arrval($value, $name, '');
  //$log->LogInfo("{$name} - {$val}");
  /*foreach ($value as $n => $v) {
    $log->LogInfo("Values {$n} - {$v}");
    }*/
  foreach($optvals as $n => $v) {
    $sel = ($v == $val) ? "selected=selected " : '';
    //$log->LogInfo("Interiem - {$val} : {$sel}: {$n} => {$v}");
    $optout[] = "<option {$sel} value=\"{$v}\">{$n}</option>";
  }
  $options = implode("\n", $optout);
  $out = <<<"END"
<select name="{$name}" id="{$name}" class="select"> 
  {$options}
</select>
END;
  return $out;
} 

function TEXTAREA($name, $value, $style='', $class='')
{
  $val = get_arrval($value, $name, '');
  $use_style = ($style == '') ?  "style=\"width:298px;\"" : "style=\"{$style}\"";
  $out = <<<"END"
    <textarea {$use_style} name="{$name}" id="{$name}" class="tarea">
{$val}
</textarea>
END;
  return $out;
}

function LABEL($name, $label_text='', $label_style="")
{
  $out = "<label for=\"{$name}\" style=\"{$label_style}\">{$label_text}</label>";
  return $out;
}

function INPUT($name, $value, $type="string", $length=0, $style="", $class='')
{
  $size = $dtype = '';
  switch($type) {
  case 'integer':
  case 'date':
  case 'datetime':
  case 'string':
    $dtype = $type;
    $itype = 'text';
    if ($length != 0) {
      $l = strval($length);
      $size= "size=\"{$l}\" ";
    }
    break;
    
  case 'password':
    // this implies a string
    $dtype = $type;
    $itype = $dtype;
    $l = strval($length);
    $size = "size=\"{$l}\" ";
    break;
  case 'submit':
    $dtype = $type;
    $itype = $dtype;
    break;
  default:
    $dtype = 'unexpected';
  }
  $val = ($type != 'submit') ? get_arrval($value, $name, ''): $value;
  $out = <<<"END"
    <input name="{$name}" id="{$name}" 
  type="{$itype}" class="{$dtype} {$class}" value="{$val}" {$size} > 
END;

  return $out;
}

/**
 * These implement widgets each of which is responsible for an instance
 * of an input area on the screen
 */

function widget_select_yn($varname, $value, $t)
{
  $optvals  = array("{$t['Select']} ..." => '-', 
                    "{$t['Yes']}" => 'Y',
                    "{$t['No']}" => 'N');
  return SELECT($varname, $optvals, $value);
}

function widget_select_ynp($varname, $value, $t)
{
  $optvals  = array("{$t['Select']}} ..." => '-', 
                    "{$t['Yes']}" => 'Y',
                    "{$t['Partial']}" => 'P',
                    "{$t['No']}" => 'N');
  return SELECT($varname, $optvals, $value);
}

function widget_select_wp($varname, $value, $t)
{
  $optvals  = array("{$t['Select']} ..." => '-', 
                    "{$t['Personal']}" => 'P',
                    "{$t['Work']}" => 'W');
  return SELECT($varname, $optvals, $value);
}

function widget_select_yni($varname, $value, $t)
{
  $optvals  = array("{$t['Select']} ..." => '-', 
                    "{$t['Yes']}" => 'Y',
                    "{$t['No']}" => 'N',
                    "{$t['Insufficient Data']}" => 'I');
  return SELECT($varname, $optvals, $value);
}

function widget_select_stars($varname, $value, $t)
{
  $optvals  = array("{$t['Select']}" => '-', 
                    "{$t['Not Audited']}" => 'N',
                    "0 {$t['Stars']}" => '0',
                    "1 {$t['Star']}"  => '1',
                    "2 {$t['Stars']}" => '2',
                    "3 {$t['Stars']}" => '3',
                    "4 {$t['Stars']}" => '4',
                    "5 {$t['Stars']}" => '5');
  return SELECT($varname, $optvals, $value);
}

function widget_select_lablevel($varname, $value, $t)
{
  $optvals  = array("{$t['Select']} ..." => '-', 
                    "{$t['National']}"   => 'N',
                    "{$t['Reference']}"  => 'F',
                    "{$t['Regional']}"   => 'G',
                    "{$t['District']}"   => 'D',
                    "{$t['Zonal']}"      => 'Z',
                    "{$t['Field']}"      => 'F'
                    );
  return SELECT($varname, $optvals, $value, $t);
}

function widget_select_labaffil($varname, $value, $t)
{
  $optvals  = array("{$t['Select']} ..." => '-', 
                    "{$t['Public']}"   => 'U',
                    "{$t['Hospital']}" => 'H',
                    "{$t['Private']}"  => 'P',
                    "{$t['Research']}" => 'R',
                    "{$t['Non-hospital outpatient clinic']}" => 'Z',
                    "{$t['Other - please specify']}"         => 'O'
                    );
  return SELECT($varname, $optvals, $value);
}

function widget_dt($name, $value, $length=10) 
{
  return INPUT($name, $value, 'date', $length);
}

function widget_text100($name, $value)
{
  return INPUT($name, $value, 'string', 100);
}

function widget_text255($name, $value)
{
  return INPUT($name, $value, 'string', 255);
}

function widget_integer($name, $value, $length=0)
{
  return INPUT($name, $value, 'integer', $length);
}


/**
 * These are the representations of a row on the screen
 */
function partial_stars($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $stars= widget_select_stars('stars', $value, $t);
$out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
  {$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
  {$stars}
</td>
END;
  return $out;
}

function partial_sec_head($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<td colspan=3 style="font-size:18px;font-weight: bold;text-transform:uppercase;padding: 2px 4px;">
<div style="">
<div style="vertical-align:top;">{$prefix} {$heading}</div>
</div>
</td>
END;
  
  return $out;
}

function partial_sec_head_small($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<td colspan=3 style="font-size:14px;font-weight: bold;padding: 2px 4px;">
<div style="">
<div style="vertical-align:top;">{$prefix} {$heading}</div>
</div>
</td>
END;

  return $out;
}

function partial_info_i($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<td colspan=3 style="font-size:14px;font-style:italic;padding: 2px 4px;">
<div style="vertical-align:top;">{$text}</div>
</td>
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

  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<td colspan=3 style="font-size:14px;padding: 2px 4px;">
    <div style="vertical-align:top;"><b>{$heading}</b> {$text}</div>
</td>
END;

  return $out;
}

function partial_sub_sec_head($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $widget_nyp_ro = "FIXME"; // widget_nyp_ro($name, $value);
 $out =  <<<"END"
<td style="padding: 2px 4px;">
<div style="display:inline-block;width:350px;vertical-align:top;">
<div style="width:348px;">
<div style="display:inline;font-weight:bold;width:25px;vertical-align:top;">{$prefix}</div> 
<div style="display:inline-block;width:320px;">
<div style="text-decoration:underline;font-weight:bold;display:inline-block;">{$heading}</div>
<div style="vertical-align:top;">{$text}
</div>
</div>
</div>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
<div style="width:60px;">{$widget_nyp_ro} </div>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
<div style="font-weight:bold;">5</div>
</td>
END;

 return $out;
}

function partial_sec_element($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $mc_yn = widget_select_yn($name, $value, $t);
  $tarea = TEXTAREA("{$name}_comment", $value);
  $out = <<<"END"
<td style="vertical-align:top;padding: 2px 4px;">
<div style="display:inline-block;vertical-align:top;">
<div style="width:325px;">
<div>
<div style="text-decoration:underline;font-weight:bold;vertical-align:top;">{$heading}</div>
<div style="vertical-align:top;">{$text}</div>
</div>
</div>
</div>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
<div style="">{$mc_yn} </div>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$tarea}
</td>
END;

  return $out;
}

function partial_lablevel($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $mc_lab_level = widget_select_lablevel($name, $value, $t);
  $out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$mc_lab_level}
</td>
END;

  return $out;
}

function partial_labaffil($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $mc_lab_affil = widget_select_labaffil($name, $value, $t);

  $out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$mc_lab_affil}
</td>
END;

  return $out;
}

function partial_date($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $dt = widget_dt($name, $value);

  $out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$dt}
</td>
END;

  return $out;
}

function partial_text($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $tarea = TEXTAREA($name, $value);
  $out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$tarea}
</td>
END;
  
  return $out;
}
function partial_tab_head3($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<td style="vertical-align:top;padding: 2px 4px;">
<i>{$prefix}</i>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
<i>{$heading}</i>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
<i>{$text}</i>
</td>
END;

  return $out;
}

function partial_pinfo($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $smallint = widget_integer("{$name}_num", $value);
  $mc_yni = widget_select_yni("{$name}_yni", $value, $t);
  $out = <<<"END"
<td style="vertical-align:top;padding: 2px 4px;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$smallint}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$mc_yni}
</td>
END;

  return $out;
}

function partial_pinfo2_i($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $smallint = widget_integer("{$name}_num", $value);
  $mc_yn = widget_select_yn("{$name}_yn", $value, $t);
  $out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
    &nbsp;&nbsp;&nbsp;&nbsp;<i>{$text}</i>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$mc_yn}
</td>
END;

  return $out;
}

function partial_pinfo2($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $smallint = widget_integer("{$name}_num", $value);
  $mc_yn = widget_select_yn("{$name}_yn", $value, $t);
  $out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
  {$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$mc_yn}
</td>
END;

  return $out;
}

function partial_criteria_1_heading($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<td colspan=3>
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
</td>
END;

  return $out;
}

function partial_criteria_1_values($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $i11 = widget_integer("{$name}_qnt_d", $value, 4);
  $i12 = widget_integer("{$name}_qnt_w", $value, 4);
  $i13 = widget_integer("{$name}_qnt_er", $value, 4);
  $i21 = widget_integer("{$name}_sqt_d", $value, 4);
  $i22 = widget_integer("{$name}_sqt_w", $value, 4);
  $i23 = widget_integer("{$name}_sqt_er", $value, 4);
  $i31 = widget_integer("{$name}_qlt_d", $value, 4);
  $i32 = widget_integer("{$name}_qlt_w", $value, 4);
  $i33 = widget_integer("{$name}_qlt_er", $value, 4);
  $out = <<<"END"
<td colspan=3>
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
</td>
END;

  return $out;
}

function partial_com_and_rec($row, $value) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $tarea = TEXTAREA($name, $value, $style="width:100%;height:400px;");
  $out = <<<"END"
<td colspan=3>
  <table style="width:100%;">    
  <tr>
    <td>
    <div class="bigtitlei">{$heading}</div>
    {$tarea}
    </td>
  </tr>
  </table>
</td>
END;

  return $out;
}

function partial_criteria_2_heading($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<td colspan=3>
  <table style="width:100%;">    
  <tr>
    <td width="7%" rowspan="2" class="centertopbold">{$prefix}</td>
    <td rowspan="2" class="title">
      {$heading}
    </td>
        <td width="12%" class="centertop">{$t['Date of panel receipt']}</td>
    <td width="12%" class="centertop">{$t['Were results reported within 15 days?']}</td>
    <td width="10%" class="centertopbold">{$t['Results & % Correct']}</td>
  </tr>
  </table>
</td>
END;

  return $out;
}
function partial_panel_heading($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<td colspan=3>
  <table style="width:100%;">    
  <tr>
    <td width="7%"></td>
    <td class="title">
      {$heading}
    </td>
    <td width="10%" class="percent">%</td>
  </tr>
  </table>
</td>
END;

  return $out;
}

function partial_panel_result($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $smallint = widget_integer("{$name}_num", $value, 4);
  $mc_yn = widget_select_yn("{$name}_yn", $value, $t);
  $dt = widget_dt("{$name}_dt", $value, 10);
  $out = <<<"END"
<td colspan=3>
  <table style="width:100%;">    
  <tr>
    <td width="7%" class="centerbold">
      {$prefix}
    </td>
    <td class="panel">
      {$heading}
    </td>
    <td width="12%">
      {$dt}
    </td>
    <td width="12%">
      {$mc_yn}
    </td>
    <td width="10%">
      {$smallint}
    </td>
  </tr>
  </table>
</td>
END;

  return $out;
}
function partial_info($row, $value) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $tarea = TEXTAREA($name, $value, $style="width:100%;height:250px;");
  $out = <<<"END"
<td colspan=3>
  <table style="width:100%;">    
  <tr>
     <td>
       {$tarea}
     </td>
  </tr>
  </table>
</td>
END;

  return $out;
}
function partial_action_plan_heading($row, $value) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<td colspan=3>
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
</td>
END;

  return $out;
}
function partial_action_plan_data($row, $value) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $input_style = "width:100%;height:50px;";
  $item = TEXTAREA("{$name}_item", $value, $input_style);
  $person = TEXTAREA("{$name}_person", $value, $input_style);
  $time = TEXTAREA("{$name}_time", $value, $input_style);
  $sign = TEXTAREA("{$name}_item", $value, $input_style);
  $out = <<<"END"
<td colspan=3>
  <table style="width:100%;">    
  <tr>
    <td width="45%">{$item}</td>
    <td width="20%">{$person}</td>
    <td width="10%">{$time}</td>                                                              
    <td >{$sign}</td>
  </tr>
  </table>
</td>
END;

  return $out;
}
/**
 * We render the rows here
 */
function calculate_page($rows, $value, $tword) 
{
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
  $words = array('Yes', 'No', 'Partial', 'Select', 'Insufficient Data', 'Personal', 'Work',
                 'Insufficient data', 'Not Audited', 'Star', 'Stars', 'National', 'Reference',
                 'Regional', 'District', 'Zonal', 'Field', 'Public', 'Hospital', 'Private',
                 'Research', 'Non-hospital outpatient clinic', 'Other - please specify',
                 'FREQUENCY', 'Daily', 'Weekly', 'With Every Run',
                 'Quantitative tests', 'Semi-quantitative tests', 'Qualitative tests',
                 'Date of panel receipt', 'Were results reported within 15 days?', 
'Results & % Correct');
  $tlist = get_common_words_translated($tword, $words);
  $tout = array();
  $tout[] = '<table border=1 style="width:800px;">';
  $tout[] = '<td style="width:359px;"></td><td style="width:164px;"></td><td  style="width:309px;"></td>';
  foreach($rows as $row){
    $type = $row['row_type'];
    $tout[] = '<tr>' 
      . call_user_func("partial_{$type}", $row, $value, $tlist) 
      . '</tr>';
  }
  $tout[] = '</table>';
  return $tout;
}
