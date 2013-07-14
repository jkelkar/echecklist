<?php 

/**
 * using the widgets and partials fill out the row
 */

/**
 * returns a value if a key exists in the dictionary else
 * returns the $default value passed in
 */
function get_arrval($arr, $k, $default) {
  return key_exists($k, $arr) ? $arr[$k] : $default;
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

function TEXTAREA($name, $value)
{
  $val = get_arrval($value, $name, '');
  $out = <<<"END"
<textarea style="width:298px;" name="{$name}" id="{$name}" class="tarea">
{$val}
</textarea>
END;
  return $out;
}

function INPUT($name, $value, $type='string', $length=0)
{
  $size = $dtype = '';
  switch($type) {
  case 'integer':
  case 'date':
  case 'datetime':
    $dtype = $type;
    break;
    
  case 'string':
  default:
    // this implies a string
    $dtype = 'string';
    $l = strval($length);
    $size = "size=\"{$l}\" ";
  }
  $val = get_arrval($value, $name, '');
  $out = <<<"END"
<input name="{$name}" id="{$name}" 
  type="text" class="{$dtype}" value="{$val}" {$size} > 
END;

  return $out;
}

/**
 * These implement widgets each of which is responsible for an instance
 * of an input area on the screen
 */

function widget_select_yn($varname, $value)
{
  $optvals  = array('Select ...' => '-', 
                    'Yes' => 'Y',
                    'No' => 'N');
  return SELECT($varname, $optvals, $value);
}

function widget_select_ynp($varname, $value)
{
  $optvals  = array('Select ...' => '-', 
                    'Yes' => 'Y',
                    'Partial' => 'P',
                    'No' => 'N');
  return SELECT($varname, $optvals, $value);
}

function widget_select_wp($varname, $value)
{
  $optvals  = array('Select ...' => '-', 
                    'Personal' => 'P',
                    'Work' => 'W');
  return SELECT($varname, $optvals, $value);
}

function widget_select_yni($varname, $value)
{
  $optvals  = array('Select ...' => '-', 
                    'Yes' => 'P',
                    'No' => 'W',
                    'Insufficient Data' => 'I');
  return SELECT($varname, $optvals, $value);
}

function widget_select_stars($varname, $value)
{
  $optvals  = array('Select ...' => '-', 
                    'Not Audited' => 'N',
                    '0 Stars' => '0',
                    '1 Star'  => '1',
                    '2 Stars' => '2',
                    '3 Stars' => '3',
                    '4 Stars' => '4',
                    '5 Stars' => '5');
  return SELECT($varname, $optvals, $value);
}

function widget_select_lablevel($varname, $value)
{
  $optvals  = array('Select ...' => '-', 
                    'National'   => 'N',
                    'Reference'  => 'F',
                    'Regional'   => 'G',
                    'District'   => 'D',
                    'Zonal'      => 'Z',
                    'Field'      => 'F'
                    );
  return SELECT($varname, $optvals, $value);
}

function widget_select_labaffil($varname, $value)
{
  $optvals  = array('Select ...' => '-', 
                    'Public'   => 'U',
                    'Hospital' => 'H',
                    'Private'  => 'P',
                    'Research' => 'R',
                    'Non-hospital Outpatient Clinic' => 'Z',
                    'Other - please specify'         => 'O'
                    );
  return SELECT($varname, $optvals, $value);
}

function widget_dt($name, $value) 
{
  return INPUT($name, $value, 'date');
}

function widget_text100($name, $value)
{
  return INPUT($name, $value, 'string', 100);
}

function widget_text255($name, $value)
{
  return INPUT($name, $value, 'string', 255);
}

function widget_integer($name, $value)
{
  return INPUT($name, $value, 'integer');
}


/**
 * These are the representations of a row on the screen
 */
function partial_stars($row, $value) {
  $row_prefix = $row['row_prefix'];
  $row_heading = $row['row_heading'];
  $row_text = $row['row_text'];
  $stars= widget_select_stars('stars', $value);
$out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
  {$row_text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
  {$stars}
</td>
END;
  return $out;
}

function partial_sec_head($row, $value) {
  $row_prefix = $row['row_prefix'];
  $row_heading = $row['row_heading'];
  $row_text = $row['row_text'];
  $out = <<<"END"
<td colspan=3 style="font-size:18px;font-weight: bold;text-transform:uppercase;padding: 2px 4px;">
<div style="">
<div style="vertical-align:top;">{$row_prefix} {$row_heading}</div>
</div>
</td>
END;
  
  return $out;
}

function partial_sec_head_small($row, $value) {
  $row_prefix = $row['row_prefix'];
  $row_heading = $row['row_heading'];
  $row_text = $row['row_text'];
  $out = <<<"END"
<td colspan=3 style="font-size:14px;font-weight: bold;padding: 2px 4px;">
<div style="">
<div style="vertical-align:top;">{$row_prefix} {$row_heading}</div>
</div>
</td>
END;

  return $out;
}

function partial_sub_sec_head($row, $value) {
  $row_prefix = $row['row_prefix'];
  $row_heading = $row['row_heading'];
  $row_text = $row['row_text'];
  $widget_nyp_ro = "FIXME"; // widget_nyp_ro($name, $value);
 $out =  <<<"END"
<td style="padding: 2px 4px;">
<div style="display:inline-block;width:350px;vertical-align:top;">
<div style="width:348px;">
<div style="display:inline;font-weight:bold;width:25px;vertical-align:top;">{$row_prefix}</div> 
<div style="display:inline-block;width:320px;">
<div style="text-decoration:underline;font-weight:bold;display:inline-block;">{$row_heading}</div>
<div style="vertical-align:top;">{$row_text}</div>
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

function partial_sec_element($row, $value) {
  $row_prefix = $row['row_prefix'];
  $row_heading = $row['row_heading'];
  $row_text = $row['row_text'];
  $name = $row['varname'];
  $mc_yn = widget_select_yn($name, $value);
  $tarea = TEXTAREA("{$name}_comment", $value);
  $out = <<<"END"
<td style="vertical-align:top;padding: 2px 4px;">
<div style="display:inline-block;vertical-align:top;">
<div style="width:325px;">
<div>
<div style="text-decoration:underline;font-weight:bold;vertical-align:top;">{$row_heading}</div>
<div style="vertical-align:top;">{$row_text}</div>
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

function partial_lablevel($row, $value) {
  $row_prefix = $row['row_prefix'];
  $row_heading = $row['row_heading'];
  $row_text = $row['row_text'];
  $name = $row['varname'];
  $mc_lab_level = widget_select_lablevel($name, $value);
  $out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
{$row_text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$mc_lab_level}
</td>
END;

  return $out;
}

function partial_labaffil($row, $value) {
  $row_prefix = $row['row_prefix'];
  $row_heading = $row['row_heading'];
  $row_text = $row['row_text'];
  $name = $row['varname'];
  $mc_lab_affil = widget_select_labaffil($name, $value);

  $out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
{$row_text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$mc_lab_affil}
</td>
END;

  return $out;
}

function partial_date($row, $value) {
  $row_prefix = $row['row_prefix'];
  $row_heading = $row['row_heading'];
  $row_text = $row['row_text'];
  $name = $row['varname'];
  $dt = widget_dt($name, $value);

  $out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
{$row_text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$dt}
</td>
END;

  return $out;
}

function partial_text($row, $value) {
  $row_prefix = $row['row_prefix'];
  $row_heading = $row['row_heading'];
  $row_text = $row['row_text'];
  $name = $row['varname'];
  $tarea = TEXTAREA($name, $value);
  $out = <<<"END"
<td colspan=2 style="vertical-align:top;padding: 2px 4px;">
{$row_text}
</td>
<td style="vertical-align:top;padding: 2px 4px;">
{$tarea}
</td>
END;
  
  return $out;
}
function partial_tab_head3($row, $value) {
  $row_prefix = $row['row_prefix'];
  $row_heading = $row['row_heading'];
  $row_text = $row['row_text'];
  $name = $row['varname'];
  $out = <<<"END"
<td style="vertical-align:top;padding: 2px 4px;">
<i>{$row_prefix}</i>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
<i>{$row_heading}</i>
</td>
<td style="vertical-align:top;padding: 2px 4px;">
<i>{$row_text}</i>
</td>
END;

  return $out;
}

function partial_pinfo($row, $value) {
  $row_prefix = $row['row_prefix'];
  $row_heading = $row['row_heading'];
  $row_text = $row['row_text'];
  $name = $row['varname'];
  $smallint = widget_integer("{$name}_num", $value);
  $mc_yni = widget_select_yni("{$name}_yni", $value);
  $out = <<<"END"
<td style="vertical-align:top;padding: 2px 4px;">
{$row_text}
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

function calculate_page($rows, $value) 
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
  //$value = array('notme' => 'no');
  $tout = array();
  $tout[] = '<table border=1>';
  $tout[] = '<td width=60mm></td><td width=60mm></td><td width=20mm></td>';
  foreach($rows as $row){
    $row_type = $row['row_type'];
    $tout[] = '<tr>' 
      . call_user_func("partial_{$row_type}", $row, $value) 
      . '</tr>';
  }
  $tout[] = '</table>';
  return $tout;
}
