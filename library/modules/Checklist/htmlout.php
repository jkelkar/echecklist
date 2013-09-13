<?php
// -*- coding: utf-8 -*-


/**
 * using the htmls to fill out the row
 */

/**
 * This handles logging
 */
require_once 'modules/Checklist/general.php';
require_once 'modules/Checklist/logger.php';
require_once '../application/models/DbTable/Lab.php';
require_once '../application/models/DbTable/Audit.php';

/**
 * these implement low level html code generators
 */





/**
 * These implement widgets each of which is responsible for an instance
 * of an input area on the screen
 */
/*
function widget_select_yn($varname, $value, $t) {
  $optvals = getYN($t);
  return OPTIONS($varname, $optvals, $value);
}



function widget_select_ynp($varname, $value, $t) {
  $optvals = getYNP($t);
  return OPTIONS($varname, $optvals, $value);
}

function widget_select_ynp_ro($varname, $value, $t) {
  / *
   * This is a display for calculated choices
   * /
  $ro_char = "{$varname}_ynp";
  $v_ro_char = get_arrval($value, $ro_char, 'N');
  $out = <<<"END"
<input class="ro" name="{$ro_char}" id="{$ro_char}"
       type="text" readonly="readonly" value="{$v_ro_char}" size=3>
END;
  return $out;
}

function widget_select_ynp_calc($varname, $value, $t, $score) {
  $optvals = getYNP($t);
  return OPTIONS_CALC($varname, $optvals, $value, $score);
}

function widget_select_ynp_add($varname, $value, $t) {
  $optvals = getYNP($t);
  $sendid = substr($varname, 0, 3);
  return OPTIONS_ADD($varname, $optvals, $value, "onclick=\"count_ynp_add('{$sendid}');\"");
}

function widget_select_yna_calc($varname, $value, $t, $score) {
  $optvals = getYNA($t);
  return OPTIONS_CALC($varname, $optvals, $value, $score);
}
function widget_select_yna_add($varname, $value, $t) {
  $optvals = getYNA($t);
  $sendid = substr($varname, 0, 3);
  return OPTIONS_ADD($varname, $optvals, $value, "onclick=\"count_ynaa_add('{$sendid}');\"");
}

function widget_select_wp($varname, $value, $t) {
  $optvals = array ( // "{$t['Select']} ..." => '-',
      "{$t['Personal']}" => 'PERSONAL',
      "{$t['Work']}" => 'WORK'
  );
  return OPTIONS($varname, $optvals, $value);
}


function widget_select_yni($varname, $value, $t) {
  $optvals = getYNI($t);
  return OPTIONS($varname, $optvals, $value);
}


function widget_select_usertype($varname, $value, $t, $noscript = true) {
  $optvals = getUserTypes($t);
  return OPTIONS($varname, $optvals, $value, $noscript);
}


function widget_select_pw($varname, $value, $t) {
  $optvals = getPW($t);
  return OPTIONS($varname, $optvals, $value);
}


function widget_select_yna($varname, $value, $t) {
  $optvals = getYNA($t);
  //logit ( "YNA: " . print_r ( $optvals, true ) );
  return OPTIONS($varname, $optvals, $value);
}

function widget_select_stars($varname, $value, $t) {
  $optvals = getStars($t);
  return OPTIONS($varname, $optvals, $value);
}

function widget_select_lablevel($varname, $value, $t, $scr = '', $multiple = false) {
  $optvals = getLevels($t);
  return OPTIONS($varname, $optvals, $value, $scr, $multiple);
}


function widget_select_labaffil($varname, $value, $t, $scr = '', $multiple = false) {
  $optvals = getAffiliations($t);
  return OPTIONS($varname, $optvals, $value, $scr, $multiple);
}

function widget_dt($name, $value, $length = 14) {
  return INPUT($name, $value, 'date', $length);
}

function widget_text100($name, $value) {
  return INPUT($name, $value, 'string', 100);
}

function widget_text255($name, $value) {
  return INPUT($name, $value, 'string', 255);
}

function widget_integer($name, $value, $length = 0) {
  return INPUT($name, $value, 'integer', $length);
}

function widget_integersmall($name, $value, $length = 0) {
  return INPUT($name, $value, 'integersmall', $length);
}
**/

function html_main_heading($row) {
  $heading = $row['heading'];
  $out = <<<"END"
<td colspan=6 class="nb">
  <center><div class="maintitle">
    {$heading}
  </div></center>
</td>
END;
  return $out;
}

function html_main2($row) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<td colspan=6 class="nb">
  <center><div class="maintitle2">
    {$heading}
  </div></center>
</td>
END;
  return $out;
}

function html_normal($row) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<div style="width:100%;">
  <div class="normal">{$text}</div>
</div>
END;
  return $out;
}

function html_full($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $text = str_replace('"', '\"', $text);
  eval("\$text = \"$text\"; ");
  $out = <<<"END"
<td colspan="6">
{$text}
</td>
END;
  return $out;
}

function html_full_nb($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $text = str_replace('"', '\"', $text);
  eval("\$text = \"$text\"; ");
  $out = <<<"END"
<td colspan="6" class="nb">
{$text}
</td>
END;
return $out;
}

function html_banner_rev($row) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $iftext = ($text != '')? 'normal': '';
  $out = <<<"END"
<td colspan=6 class="nb">
<table class="fullwide"><tr>
  <td class="banner_rev">
  {$prefix} {$heading}
  </td></tr>
  <tr>
  <td class="{$iftext} nb">{$text}</td>
      </tr>
  </table>
END;

  return $out;
}

function html_banner_rev_border($row) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];

  $out = <<<"END"
<td colspan=6>
  <div class="big_banner_rev">
  {$prefix} {$heading}
  </div>
  <div class="normal_border">{$text}</div>
</td>
END;
  return $out;
}

/**
 * These are the representations of a row on the screen
 */
function html_stars($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $stars = widget_select_stars("{$name}_stars", $value, $t);
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:left;">
  {$stars}
</div>
    </div>
END;
  return $out;
}

function html_string_field($row, $value, $t) {
  $name = $row['varname'];
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $stringf = INPUT($name, $value, 'string', 55, '', '');
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:left;">
  {$stringf}
</div>
</div>
END;
  return $out;
}

function html_string_ro($row, $value, $t) {
  $name = $row['varname'];
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  //$stringf = INPUT($name, $value, 'string', 55, '', '');
  $val = get_arrval($value, $name, '');
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:left;">
  {$val}
</div>
</div>
END;
  return $out;
}

function html_prof_info($row, $value, $t) {
  $name = $row['varname'];
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $num = get_arrval($value, $name, '');
  $yni = get_arrval($value, "${name}_yni", '');
  //$intf = INPUT($name, $value, 'integer', 3, 'margin-right:10px;', '');
  //$mc_yni = widget_select_yni("{$name}_yni", $value, $t);
  $prof = getPROF($yni);
  $out = <<<"END"
<td colspan="2" class="dhead">{$text}</td>
<td colspan="2" style="text-align:center;">{$num}</td>
<td colspan="2" style="padding:2px; vertical-align:top; text-align:center;">
  <span class="{$prof['Y']}">Yes</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="{$prof['N']}">No</span>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="{$prof['I']}">Insufficient Data</span>
</td>
END;
  return $out;
}

function html_prof_info_yn_html($row, $value, $t) {
  $name = $row['varname'];
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $info = $row['info'];
  //$mc_yn = widget_select_yn("{$name}_yn", $value, $t);
 $ded = getPROF(get_arrval($value, "{$name}_dedicated_yn", ''));
 $tra = getPROF(get_arrval($value, "{$name}_trained_yn", ''));
  $out = <<<"END"
<td colspan="4" class="laic">{$text}
  <br />
  <span style="text-align:center;font-style:normal;">
    <span class="{$ded['Y']}">Yes</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="{$ded['N']}">No</span>
  </span></td>
<td colspan="2" class="laic">{$info}
  <br />
  <span style="text-align:center;font-style:normal;">
    <span class="{$tra['Y']}">Yes</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="{$tra['N']}">No</span>
  </span></td>
END;
  return $out;
}

function html_integer_field($row, $value, $t) {
  $name = $row['varname'];
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $intf = INPUT($name, $value, 'integer', 0, '', '');
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:left;">
  {$intf}
</div>
</div>
END;
  return $out;
}

function html_text_field($row, $value, $t) {
  $name = $row['varname'];
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $tarea = TEXTAREA("{$name}_comment", $value, "width:395px;height:50px;margin-top:5px;");
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

function html_text_ro($row, $value, $t) {
  $name = $row['varname'];
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  // $tarea = TEXTAREA("{$name}_comment", $value, "width:395px;height:50px;margin-top:5px;");
  $val = get_arrval($value, $name, '');
  $val = str_replace("\n", '<br />', $val);
  $out = <<<"END"
<div style="width:100%;">
<div style="display:inline-block;vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left">
  {$text}
</div>
<div style="display:inline-block;vertical-align:top;width:400px;float:left;">
  {$val}
</div>
</div>
END;
      return $out;
}

function html_date_field($row, $value, $t) {
  $name = $row['varname'];
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $datef = INPUT($name, $value, 'date', 14, '', '');
  // $script = '<script> $(function() {$( "' . "#{$name}" . '" ).datepicker();});</script>';
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:left;">
  {$datef}
</div>
</div>
END;
  return $out;
}


function html_sec_elem_info_normal($row, $value, $t) {
  /* $prefix = $row['prefix'];
     $heading = $row['heading']; */
  $text = $row['text'];
  /*$name = $row['varname'];
  $dt = widget_dt($name, $value);
  $script = '<script> $(function() {$( "' . "#{$name}" . '" ).datepicker();});</script>'; */
  $out = <<<"END"
<td colspan=6 class="lg" style="padding: 2px 4px;font-style:italic;font-size:0.9em">
{$text}
</td>
END;

  return $out;
}

function html_sec_head($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  logit("SECHEAD: {$prefix} {$heading}");
  $out = <<<"END"
<td colspan=6 style="padding: 2px 4px;color:white;background-color:black;">
  <div style="text-transform:uppercase;padding-bottom:10px;font-size:1.0625em;">
    <span>{$prefix}</span>
    <span>{$heading}</span>
  </div>
</td>
END;

  return $out;
}

function html_sec_head_lab_info($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<td colspan="6" class="nb">
  <table border="0"><tr class="dg">
    <td style="" class="ss">{$text}</td>
    <td style="width:8%;" class="cb"></td>
    <td style="width:8%;" class="cb"></td>
  </tr></table></td>
END;

  return $out;
}

function html_prof_info_yn_suff($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $extradata = $ending = '';
  if ($name == 'sufficient_other') {
      $extradata = get_arrval($value, "{$name}_data", '');
      $ending = '</tr><tr><td colspan="6" class="nb"><div class="pagebreak" style="height:15px;">&nbsp;</div></td>';
      }
  //logit("YN: {$name}_yn
  $yn = getPROF(get_arrval($value, "{$name}_yn", ''));

  $out = <<<"END"
<td colspan="6" class="nb">
  <table style="border:none;width:100%;"><tr class="dg">
    <td class="ss" style="padding:7px;font-weight:bold;">{$text} {$extradata}</td>
    <td style="width:8%;" class="c {$yn['Y']}">YES</td>
    <td style="width:8%;" class="c {$yn['N']}">NO</td>
  </tr></table></td>{$ending}
END;

  return $out;
}

function html_sec_head_top($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<td colspan="6">
<div style="border:1px solid #ccc;padding: 4px;font-size:0.9em">
<b>{$heading}</b> {$text}
</div></td>
 </tr><tr><td colspan="6" class="nb"><div class="pagebreak" style="height:15px;">&nbsp;</div></td>
END;

  return $out;
}
function html_sec_head_empty($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<td colspan="6" class="" style="padding:4px;">
{$prefix} {$heading}
</td>
END;

return $out;
}

function html_sec_head_small($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<td colspan="6" class="dg">
{$prefix} {$heading}
</td>
END;

  return $out;
}

function html_info_i($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<td colspan="6">
<table style="width:100%;"><tr>
<td style="font-size:14px;font-style:italic;padding: 2px 4px;">
<div style="vertical-align:top;">{$text}</div>
</td>
</tr></table></td>
END;

  return $out;
}

function html_info_bn($row, $value, $t) {
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
<table style="width:100%;"><tr>
<td style="font-size:14px;padding: 2px 4px;">
    <div style="vertical-align:top;"><b>{$heading}</b> {$text}</div>
</td>
</tr></table>
END;

  return $out;
}

function html_sub_sec_head($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $info = $row['info'];
  $name = $row['varname'];
  $ec = $row['element_count'];
  $max_score = $row['score'];
  $head = ($heading) ? "{$heading}<br />" : "";
  $nscore = "{$name}_score";
  $scoreval = get_arrval($value, $nscore, '');
  $selval = get_arrval($value, "{$name}", '');
  logit("{$name} -> {$selval}");
  $ch = getYNPA($selval);
  $comment = fixText(get_arrval($value, "{$name}_comment", ''));
  $br = ($heading =='') ? '' : '<br />';
  $out = <<<"END"
<td style="padding: 2px 4px;">
    <div style="display:inline;">
      <div style="font-weight:bold;vertical-align:top;float:left;">
      {$prefix}</div>
      <div style="display:block;float:left;width:85%;margin-left:5px;">
        <span class="ub">{$heading}</span>{$br}
        <b>{$text}</b>
      </div>
      <div style="clear:both;"></div>
      <div style="font-style:italic;font-weight:bold;font-size::0.75em;margin-top:5px;">
      {$info} </div>
    </div>
</td>
<td class="cb {$ch['YC']}">Y<br />{$ch['Y']}</td>
<td class="cb {$ch['PC']}">P<br />{$ch['P']}</td>
<td class="cb {$ch['NC']}">N<br />{$ch['N']}</td>
<td class="comment">{$comment}</td>
<td class="cb">{$scoreval} /<span class="tiny">{$max_score}</span></td>
END;

      return $out;
}

function html_sub_sec_head_ynp($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $info = $row['info'];
  $name = $row['varname'];
  $ec = $row['element_count'];

  $selval = get_arrval($value, "{$name}_ynp", '');
  $ch = getYNPA($selval);
  $comment = fixText(get_arrval($value, "{$name}_comment", ''));
  // $widget_nyp = widget_select_ynp_add("{$name}_ynp", $value, $t);
  $head = ($heading) ? "{$heading}<br />" : "";
  $nscore = "{$name}_score";
  $scoreval = get_arrval($value, $nscore, 0);END;
  $out = <<<"END"
<td style="vertical-align:top;padding: 3px 0 0 3px;">{$prefix}</td>
<td style="padding: 2px 4px;">
  <div style="padding-left: 10px;">{$text}</div>
  <div class="en">{$info}</div>
</td>
<td class="cb {$ch['YC']}">{$ch['Y']}</td>
<td class="cb {$ch['NC']}">{$ch['N']}</td>
<td class="cb {$ch['PC']}">{$ch['P']}</td>
<td class="comment">{$comment}</td>
END;

  $outx = <<<"END"
  <td style="padding: 2px 4px;">
  <div style="display:inline-block;vertical-align:top;">
    <div style="display:inline;">
      <div style="font-weight:bold;vertical-align:top;float:left;">
      {$prefix}</div>
      <div style="display:block;float:left;width:85%;margin-left:5px;">
        <div style="text-decoration:underline;font-weight:bold;display:inline-block;">
          {$heading}</div>
        <div style="vertical-align:top;display:inline-block;font-weight:bold;">
          {$text}
        </div>
      </div>
      <div style="clear:both;"></div>
      <div style="font-style:italic;font-weight:bold;font-size::0.75em;margin-top:5px;">
      {$info} </div>
    </div>
  </div>
</td>
<td class="cb {$ch['YC']}">Y<br />{$ch['Y']}</td>
<td class="cb {$ch['PC']}">P<br />{$ch['P']}</td>
<td class="cb {$ch['NC']}">N<br />{$ch['N']}</td>
<td class="comment">{$comment}</td>
<td class="cb">{$scoreval}/<span class="tiny">{$max_score}</span></td>
END;

  return $out;
}

function html_sub_sec_head_ro($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $info = $row['info'];
  $name = $row['varname'];
  $ec = $row['element_count'];
  $max_score = $row['score'];
  logit("MSCORE: {$max_score}");
  $nscore = "{$name}_score";
  $scoreval = get_arrval($value, $nscore, 0);
  $head = ($heading) ? "{$heading}<br />" : "";
  $yna = get_arrval($value, "{$name}_ynp", '');
  $ch = getYNPA($yna);
  $comment = fixText(get_arrval($value, "{$name}_comment", ''));
  $br = ($heading =='') ? '' : '<br />';
  $out = <<<"END"
  <td style="padding: 2px 4px;">
      <div style="display:inline;">
        <div style="font-weight:bold;vertical-align:top;float:left;">
          {$prefix}</div>
        <div style="display:block;float:left;width:85%;margin-left:5px;">
          <span class="ub">{$heading}</span>{$br}
            <b>{$text}</b>
        </div>
        <div style="clear:both;"></div>
        <div style="font-style:italic;font-weight:bold;font-size:0.75em;margin-top:5px;">
          {$info} </div>
      </div>
  </td>
  <td class="cb {$ch['YC']}">Y<br />{$ch['Y']}</td>
  <td class="cb {$ch['PC']}">P<br />{$ch['P']}</td>
  <td class="cb {$ch['NC']}">N<br />{$ch['N']}</td>
  <td class="comment">{$comment}</td>
  <td class="cb">{$scoreval} /<span class="tiny">{$max_score}</span>
  </td>
END;

  return $out;
}

function html_sec_element_yna($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  if ($heading) {
    $heading = $heading . '<br />';
  }
  $text = $row['text'];
  $info = $row['info'];
  $name = $row['varname'];
  $yna = get_arrval($value, "{$name}_yna", '');
  $ch = getYNPA($yna);
  $comment = fixText(get_arrval($value, "{$name}_comment", ''));

  $out = <<<"END"
<td style="padding: 2px 4px;">
        <div style="padding-left: 10px;">{$text}</div>
        <div class="en"
          <!--style="font-style: italic; font-weight: bold; font-size::0.75em; margin-top: 5px;"-->
          {$info}</div>
      </td>
      <td class="cb {$ch['YC']}">{$ch['Y']}</td>
      <td class="cb {$ch['NC']}">{$ch['N']}</td>
      <td class="cb {$ch['NAC']}">{$ch['NA']}</td>
      <td class="comment">{$comment}</td>
      <td></td>
END;

  return $out;
}

function html_sec_element_ynp($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  if ($heading) {
    $heading = $heading . '<br />';
  }
  $text = $row['text'];
  $info = $row['info'];
  $name = $row['varname'];
  $ynp = get_arrval($value, "{$name}_ynp", '');
  $ch = getYNPA($ynp);
  $comment = fixText(get_arrval($value, "{$name}_comment", ''));
  $out = <<<"END"
<td style="padding: 2px 4px;">
        <div style="padding-left: 10px;">{$text}</div>
        <div class="en">
          <!--style="font-style: italic; font-weight: bold; font-size::0.75em; margin-top: 5px;"-->
          {$info}</div>
      </td>
      <td class="cb {$ch['YC']}">{$ch['Y']}</td>
      <td class="cb {$ch['PC']}">{$ch['P']}</td>
      <td class="cb {$ch['NC']}">{$ch['N']}</td>
      <td class="comment">{$comment}</td>
      <td></td>
END;

        return $out;
}

function html_sec_element($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  if ($heading) {
    $heading = $heading . '<br />';
  }
  $text = $row['text'];
  $info = $row['info'];
  $name = $row['varname'];
  $yn = get_arrval($value, "{$name}_yn", '');
  $ch = getYNPA($yn);
  $comment = fixText(get_arrval($value, "{$name}_comment", ''));
  $out = <<<"END"
<td style="padding: 2px 4px;">
  <div style="padding-left: 10px;">{$text}</div>
  <div class="en">{$info}</div>
</td>
<td class="cb {$ch['YC']}">{$ch['Y']}</td>
<td class="cb {$ch['NC']}">{$ch['N']}</td>
<td class="dg"></td>
<td class="comment">{$comment}</td>
<td></td>
END;

  return $out;
}

function html_lablevel($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $mc_lab_level = widget_select_lablevel($name, $value, $t);
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding-right:10px;width:390px;text-align:right;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;width:400px;float:left;">
{$mc_lab_level}
</td>
</tr></table>
END;

  return $out;
}

function html_slipta_official($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $val = get_arrval($value, $name, 'f');
  $checked = ($val == 't') ? true : false;
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
<label for="{$name}">{$text}</label>
</td>
<td style="vertical-align:top;width:400px;float:left;">
<input type="checkbox" id="{$name}" name="{$name}" value="T" {$checked}
    style="margin-right:8px;">
</td>
</tr></table>
END;

return $out;
}

function html_slmta_type($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $mc_slmta_status = widget_select_slmtatypes($name, $value, $t);
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
{$text}
</td>
<td style="vertical-align:top;width:400px;float:left;">
{$mc_slmta_status}
</td>
</tr></table>
END;

  return $out;
}

function html_labaffil($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $mc_lab_affil = widget_select_labaffil($name, $value, $t);

  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding-right:10px; width:390px;text-align:right;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;width:400px;float:left;">
{$mc_lab_affil}
</td>
</tr></table>
END;

  return $out;
}

function html_date($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $dt = widget_dt($name, $value);
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

function html_text($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $tarea = TEXTAREA($name, $value);
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

function html_tab_head3($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<td colspan="2" style="vertical-align:top;padding: 2px 4px;text-align:center;">
<i>{$prefix}</i>
</td>
<td colspan="2" style="vertical-align:top;padding: 2px 4px;text-align:center;">
<i>{$heading}</i>
</td>
<td colspan="2" style="vertical-align:top;padding: 2px 4px;text-align:center;">
<i>{$text}</i>
</td>
END;

  return $out;
}

function html_pinfo($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $smallint = widget_integer("{$name}_num", $value, 4);
  $mc_yni = widget_select_yni("{$name}_yni", $value, $t);
  $out = <<<"END"
  <table style="width:100%;"><tr>
      <td style="vertical-align:top;padding: 2px 4px;width:500px;">
        <div style="float:left;">{$text}</div>
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

function html_pinfo2_i($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $smallint = widget_integer("{$name}_num", $value);
  $mc_yn = widget_select_yn("{$name}_yn", $value, $t);
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

function html_pinfo2($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $smallint = widget_integer("{$name}_num", $value);
  $mc_yn = widget_select_yn("{$name}_yn", $value, $t);
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

function html_criteria_1_heading($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
 <td colspan="6" class="nb">
  <table style="width:100%;">
  <tr class="dg">
    <td width="7%" rowspan="2" class="centertopbold ">{$prefix}</td>
    <td rowspan="2" class="title">
      {$heading}
    </td>
    <td width="21%" colspan=3 class="centertopbold">{$t['FREQUENCY']}</td>
  </tr>
  <tr class="dg">
    <td width="7%" class="centertopbold">{$t['Daily']}</td>
	  <td width="7%" class="centertopbold">{$t['Weekly']}</td>
	  <td class="centerbold">{$t['With Every Run']}</td>
  </tr>
  </table></td>
END;

  return $out;
}

function html_criteria_1_values($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $i11 = get_arrval($value, "{$name}_qnt_d", '');
  $i12 = get_arrval($value, "{$name}_qnt_w", '');
  $i13 = get_arrval($value, "{$name}_qnt_er", '');
  $i21 = get_arrval($value, "{$name}_sqt_d", '');
  $i22 = get_arrval($value, "{$name}_sqt_w", '');
  $i23 = get_arrval($value, "{$name}_sqt_er", '');
  $i31 = get_arrval($value, "{$name}_qlt_d", '');
  $i32 = get_arrval($value, "{$name}_qlt_w", '');
  $i33 = get_arrval($value, "{$name}_qlt_er", '');
  $out = <<<"END"
<td colspan="6" class="nb">
  <table style="width:100%;border:none;">
  <tr>
    <td width="7%" rowspan="4" class="centertopbold tbw">{$prefix}</td>
    <td colspan="4" class="title tbw">{$heading}</td>
  </tr>
  <tr>
    <td class="tests">{$t['Quantitative tests']}</td>
    <td width="7%" class="cb">{$i11}</td>
    <td width="7%" class="cb">{$i12}</td>
	<td width="7%" class="cb">{$i13}</td>
  </tr>
  <tr>
    <td class="tests">{$t['Semi-quantitative tests']}</td>
    <td class="cb">{$i21}</td>
    <td class="cb">{$i22}</td>
    <td class="cb">{$i23}</td>
  </tr>
  <tr>
    <td class="tests">{$t['Qualitative tests']}</td>
    <td class="cb">{$i31}</td>
    <td class="cb">{$i32}</td>
    <td class="cb">{$i33}</td>
  </tr>
</table></td>
END;

  return $out;
}

function html_com_and_rec($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $tval = get_arrval($value, $name, '');
  //$tarea = TEXTAREA($name, $value, $style = "width:100%;height:400px;");
  $out = <<<"END"
<td colspan="6">
  <div class="bigtitlei">{$heading}</div>
  <div style="min-height:150px;vertical-align:top;margin-left:5px;margin-top:4px;">{$tval}</div>
</td>
</tr><tr><td colspan="6" class="nb"><div class="pagebreak" style="height:15px;">&nbsp;</div></td>
END;

  return $out;
}

function html_criteria_2_heading($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<td colspan="6">
  <table style="width:100%;"><tr class="dg">
    <td width="7%" class="centertopbold ">{$prefix}</td>
    <td class="topbold">
      {$heading}
    </td>
    <td style="width:14%;" class="centertop">{$t['Date of panel receipt']}</td>
    <td style="width:18%;" class="centertop">{$t['Were results reported within 15 days?']}</td>
    <td style="width:10%;" class="centertopbold">{$t['Results & % Correct']}</td>
</tr></table></td>
END;

  return $out;
}

function html_panel_heading($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<td colspan="6">
  <table style="width:100%;">
  <tr class="lg">
    <td width="7%"></td>
    <td class="title">
      {$heading}
    </td>
    <td width="10%" class="percent">%</td>
</tr></table></td>
END;

  return $out;
}

function html_panel_heading2($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $sfield = widget_integer("{$name}_name", $value, 32);
  $out = <<<"END"
<td colspan="6">
  <table style="width:100%;">
  <tr>
    <td width="7%"></td>
    <td class="title">
      {$heading} {$sfield}
    </td>
    <td width="10%" class="percent">%</td>
  </tr>
  </table></td>
END;

  return $out;
}

function html_slipta_tel_type($row, $value, $t) {
  $names = array('end_date','dola','slmta_pas',
      'names_affil_t_comment','labname','labnum','labaddr',
      'labtel','labfax','labemail','labhead','labheadtel','labheadteltype','lablevel','labaffil',
      'labaffil_other','prof_deg_num','prof_deg_yni','prof_dip_num','prof_dip_yni','prof_cert_num',
      'prof_cert_yni','microscopist_num','microscopist_yni','dataclerk_num','dataclerk_yni',
      'phlebo_num','phlebo_yni','cleaner_num','cleaner_yni','cleaner_dedicated','cleaner_trained',
      'driver_num','driver_yni','driver_dedicated','driver_trained','other_num','other_yni',
      'sufficient_space','sufficient_equipment','sufficient_supplies','sufficient_personnel',
      'sufficient_infra');
  $v = array();
  foreach($names as $n) {
    $v[$n] = get_arrval($value, $n, '');
  }
 // $stars_rev = rev("getStars", $t);
 // $v['slmta_pas'] = $stars_rev[$v[$n]];
  $ll = getLL($v['lablevel']);
  $af = getAF($v['labaffil']);
  $st = getST($v['slmta_pas']);
  $tt = getTT($v['labheadteltype']);
  $v['labaddr'] = str_replace("\n", "<br />", $v['labaddr']);
  $out = <<<"END"
 <td colspan="6">
 <table class="display">
  <tr style="">
    <td style="width: 16%;"></td>
    <td style="width: 7%;"></td>
    <td style="width: 10%;"></td>
    <td style="width: 5%;"></td>
    <td style="width: 9%;"></td>
    <td style="width: 4%;"></td>
    <td style="width: 6%;"></td>
    <td style="width: 7%;"></td>
    <td style="width: 4%;"></td>
    <td style="width: 7%;"></td>
    <td style="width: 4%;"></td>
    <td style="width: 11%;"></td>
    <td style="width: 3%;"></td>
    <td style=""></td>
  </tr>
  <tr>
    <td colspan=14
      style="padding: 2px 4px; background-color: black; color: white;">
      <div
        style="text-transform: uppercase; padding-bottom: 15px; font-family: Helvetica, Arial, sans-serif; font-size: 18px;">
        part I: laboratory profile</div>
    </td>
  </tr>
  <tr>
    <td colspan="7" style="vertical-align: top;"><span class="dhead">Date
        of Audit</span> <span class="data">{$v['end_date']}</span></td>
    <td colspan="7" style="vertical-align: top;"><span class="dhead">Date
        of Last Audit</span> <span class="data">{$v['dola']}</span></td>
  </tr>
  <tr>
    <td colspan=2 class="dhead" style="vertical-align: top;">Prior Audit
      Status</td>
    <td colspan=2 class="star lg {$st['N']}">Not Audited</td>
    <td class="star lg {$st['0']}">0 Stars</td>
    <td colspan=2 class="star lg {$st['1']}">1 Star</td>
    <td colspan=2 class="star lg {$st['2']}">2 Stars</td>
    <td colspan=2 class="star lg {$st['3']}">3 Stars</td>
    <td class="star lg {$st['4']}">4 Stars</td>
    <td colspan=2 class="star lg {$st['5']}">5 Stars</td>
  </tr>
  <tr>
    <td colspan="14" class="la"><span
      class="dhead">Names and Affiliation(s) of Auditor(s)</span>
      <div class="data">
        {$v['names_affil_t_comment']}
      </div></td>
  </tr>
  <tr>
    <td colspan=10 style="vertical-align: top;"><span class="dhead">Laboratory
        Name</span> <span class="data"><br />{$v['labname']}</span></td>
    <td colspan=4 style="vertical-align: top;"><span class="dhead">Laboratory
        Number</span> <span class="data"><br />{$v['labnum']}</span></td>
  </tr>
  <tr>
    <td colspan=14 style="vertical-align: top;"><span class="dhead">Laboratory
        Address</span> <span class="data"><br />{$v['labaddr']}</span></td>
  </tr>
  <tr>
    <td colspan=3 style="vertical-align: top;"><span class="dhead">Laboratory
        Telephone</span> <span class="data"><br />{$v['labtel']}</span></td>
    <td colspan=6 style="vertical-align: top;"><span class="dhead">Fax</span>
      <span class="data"><br />{$v['labfax']}</span></td>
    <td colspan=5 style="vertical-align: top;"><span class="dhead">Email</span><span
      class="data"><br />{$v['labemail']}</span></td>
  </tr>
  <tr>
    <td colspan=6 rowspan=2 style="vertical-align: top;"><span
      class="dhead">Head of Laboratory</span> <span class="data"><br />{$v['labhead']}</span></td>
    <td colspan=7 rowspan=2 style="vertical-align: top;"><span
      class="dhead">Telephone</span> <span class="data">(Head of
        Laboratory)</span> <span class="data"><br />{$v['labheadtel']}</span></td>
    <td style="text-align: center;" class="{$tt['P']}">Personal</td>
  </tr>
  <tr>
    <td style="text-align: center;" class="{$tt['W']}">Work</td>
  </tr>
  <tr class="lg" style="height: 35px">
    <td colspan=6 style="vertical-align: top;"><span class="dhead">Laboratory
        Level</span> <span class="data">(check only one)</span></td>
    <td colspan=8 style="vertical-align: top;"><span class="dhead">Type
        of Laboratory/Laboratory Affiliation</span> <span class="data">(check
        only one)</span></td>
  </tr>
  <tr>
    <td colspan=1 class="la">
      <div class="cbx">{$ll['N']}</div> <span class="la">National</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$ll['R']}</div> <span class="la">Reference</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$ll['P']}</div> <span class="la">Regional/Provincial</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$af['P']}</div> <span class="la">Public</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['H']}</div> <span class="la">Hospital<br />
        <br /></span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['V']}</div> <span class="la">Private</span>
    </td>
  </tr>
  <tr>
    <td colspan=1 class="la">
      <div class="cbx">{$ll['D']}</div> <span class="la">District</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$ll['Z']}</div> <span class="la">Zonal</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$ll['F']}</div> <span class="la">Field</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$af['R']}</div> <span class="la">Research</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['N']}</div> <span class="la">Non-hospital
        Outpatient Clinic</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['O']}</div> <span class="la">Other --
        Please specify:</span>
      <div style="text-decoration: underline; vertical-align: bottom;">{$v['labaffil_other']}</div>
    </td>
  </tr>
  <tr class="lg" style="height:35px">
    <td colspan=14 class="dhead">
      <span>Laboratory Staffing Summary</span>
    </td>
  </tr>
</table>
</td>
END;
  return $out;
}

function html_bat_tel_type($row, $value, $t) {
  logit('biosafety_level');
  $names = array('end_date','dola',//'slmta_pas',
      'names_affil_t','labname','labnum','labaddr',
      'labtel','labfax','labemail','labhead','labheadtel','labheadteltype','lablevel','labaffil',
      'labaffil_other',
      //'prof_deg_num','prof_deg_yni','prof_dip_num','prof_dip_yni','prof_cert_num',
      //'prof_cert_yni','microscopist_num','microscopist_yni','dataclerk_num','dataclerk_yni',
      //'phlebo_num','phlebo_yni','cleaner_num','cleaner_yni','cleaner_dedicated','cleaner_trained',
      //'driver_num','driver_yni','driver_dedicated','driver_trained','other_num','other_yni',
      //'sufficient_space','sufficient_equipment','sufficient_supplies','sufficient_personnel',
      //'sufficient_infra',
      'biosafety_level', 'toxins_comment');
  $v = array();
  foreach($names as $n) {
    $v[$n] = get_arrval($value, $n, '');
  }
  // $stars_rev = rev("getStars", $t);
  // $v['slmta_pas'] = $stars_rev[$v[$n]];
  $ll = getLL($v['lablevel']);
  $af = getAF($v['labaffil']);
  //$st = getST($v['slmta_pas']);
  $tt = getTT($v['labheadteltype']);
  $v['labaddr'] = fixText($v['labaddr']);
  $out = <<<"END"
 <td colspan="6">
 <table class="display">
  <tr style="">
    <td style="width: 16%;"></td>
    <td style="width: 7%;"></td>
    <td style="width: 10%;"></td>
    <td style="width: 5%;"></td>
    <td style="width: 9%;"></td>
    <td style="width: 4%;"></td>
    <td style="width: 6%;"></td>
    <td style="width: 7%;"></td>
    <td style="width: 4%;"></td>
    <td style="width: 7%;"></td>
    <td style="width: 4%;"></td>
    <td style="width: 11%;"></td>
    <td style="width: 3%;"></td>
    <td style=""></td>
  </tr>
  <tr>
    <td colspan=14
      style="padding: 2px 4px; background-color: black; color: white;">
      <div
        style="text-transform: uppercase; padding-bottom: 15px; font-family: Helvetica, Arial, sans-serif; font-size: 18px;">
        part I: laboratory profile</div>
    </td>
  </tr>
  <tr>
    <td colspan="7" style="vertical-align: top;"><span class="dhead">Date
        of Audit</span> <span class="data">{$v['end_date']}</span></td>
    <td colspan="7" style="vertical-align: top;"><span class="dhead">Date
        of Last Audit</span> <span class="data">{$v['dola']}</span></td>
  </tr>
  <!--tr>
    <td colspan=2 class="dhead" style="vertical-align: top;">Prior Audit
      Status</td>
    <td colspan=2 class="star lg {$st['N']}">Not Audited</td>
    <td class="star lg {$st['0']}">0 Stars</td>
    <td colspan=2 class="star lg {$st['1']}">1 Star</td>
    <td colspan=2 class="star lg {$st['2']}">2 Stars</td>
    <td colspan=2 class="star lg {$st['3']}">3 Stars</td>
    <td class="star lg {$st['4']}">4 Stars</td>
    <td colspan=2 class="star lg {$st['5']}">5 Stars</td>
  </tr-->
  <tr>
    <td colspan="14" class="la"><span
      class="dhead">Names and Affiliation(s) of Auditor(s)</span>
      <div class="data">
        {$v['names_affil_t']}
      </div></td>
  </tr>
  <tr>
    <td colspan=10 style="vertical-align: top;"><span class="dhead">Laboratory
        Name</span> <span class="data"><br />{$v['labname']}</span></td>
    <td colspan=4 style="vertical-align: top;"><span class="dhead">Laboratory
        Number</span> <span class="data"><br />{$v['labnum']}</span></td>
  </tr>
  <tr>
    <td colspan=14 style="vertical-align: top;"><span class="dhead">Laboratory
        Address</span> <span class="data"><br />{$v['labaddr']}</span></td>
  </tr>
  <tr>
    <td colspan=3 style="vertical-align: top;"><span class="dhead">Laboratory
        Telephone</span> <span class="data"><br />{$v['labtel']}</span></td>
    <td colspan=6 style="vertical-align: top;"><span class="dhead">Fax</span>
      <span class="data"><br />{$v['labfax']}</span></td>
    <td colspan=5 style="vertical-align: top;"><span class="dhead">Email</span><span
      class="data"><br />{$v['labemail']}</span></td>
  </tr>
  <tr>
    <td colspan=6 rowspan=2 style="vertical-align: top;"><span
      class="dhead">Head of Laboratory</span> <span class="data"><br />{$v['labhead']}</span></td>
    <td colspan=7 rowspan=2 style="vertical-align: top;"><span
      class="dhead">Telephone</span> <span class="data">(Head of
        Laboratory)</span> <span class="data"><br />{$v['labheadtel']}</span></td>
    <td style="text-align: center;" class="{$tt['P']}">Personal</td>
  </tr>
  <tr>
    <td style="text-align: center;" class="{$tt['W']}">Work</td>
  </tr>
  <tr class="lg" style="height: 35px">
    <td colspan=6 style="vertical-align: top;"><span class="dhead">Laboratory
        Level</span> <span class="data">(check only one)</span></td>
    <td colspan=8 style="vertical-align: top;"><span class="dhead">Type
        of Laboratory/Laboratory Affiliation</span> <span class="data">(check
        only one)</span></td>
  </tr>
  <tr>
    <td colspan=1 class="la">
      <div class="cbx">{$ll['N']}</div> <span class="la">National</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$ll['R']}</div> <span class="la">Reference</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$ll['P']}</div> <span class="la">Regional/Provincial</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$af['P']}</div> <span class="la">Public</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['H']}</div> <span class="la">Hospital<br />
        <br /></span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['V']}</div> <span class="la">Private</span>
    </td>
  </tr>
  <tr>
    <td colspan=1 class="la">
      <div class="cbx">{$ll['D']}</div> <span class="la">District</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$ll['Z']}</div> <span class="la">Zonal</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$ll['F']}</div> <span class="la">Field</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$af['R']}</div> <span class="la">Research</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['N']}</div> <span class="la">Non-hospital
        Outpatient Clinic</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['O']}</div> <span class="la">Other --
        Please specify:</span>
      <div style="text-decoration: underline; vertical-align: bottom;">{$v['labaffil_other']}</div>
    </td>
  </tr>
  <tr>
    <td colspan="14"><div style="padding:4px;float:left;font-weight:bold;">Biosafety Level: </div>
      <div style="margin-left:10px;padding:4px;float:left;">{$v['biosafety_level']}</div>
      <div style="clear:both;"></div>
  </td></tr>
  <tr>
    <td colspan="14" style="height:150px;padding:4px;vertical-align:top;">
      <b>List Agents/Toxins Used in Laboratory:</b><br />{$v['toxins_comment']}
  </td> </tr>
 </table>
</td>
END;
  return $out;
}

function html_tb_tel_type($row, $value, $t) {
  logit('biosafety_level');
  $names = array('end_date','dola','slmta_pas',
      'names_affil_t','labname','labnum','labaddr',
      'labtel','labfax','labemail','labhead','labheadtel','labheadteltype','lablevel','labaffil',
      'labaffil_other','prof_deg_num','prof_deg_yni','prof_dip_num','prof_dip_yni','prof_cert_num',
      'prof_cert_yni','microscopist_num','microscopist_yni','dataclerk_num','dataclerk_yni',
      'phlebo_num','phlebo_yni','cleaner_num','cleaner_yni','cleaner_dedicated','cleaner_trained',
      'driver_num','driver_yni','driver_dedicated','driver_trained','other_num','other_yni',
      'sufficient_space','sufficient_equipment','sufficient_supplies','sufficient_personnel',
      'sufficient_infra', 'biosafety_level', 'toxins');
  $v = array();
  foreach($names as $n) {
    $v[$n] = get_arrval($value, $n, '');
  }
  // $stars_rev = rev("getStars", $t);
  // $v['slmta_pas'] = $stars_rev[$v[$n]];
  $ll = getLL($v['lablevel']);
  $af = getAF($v['labaffil']);
  $st = getST($v['slmta_pas']);
  $tt = getTT($v['labheadteltype']);
  $v['labaddr'] = str_replace("\n", "<br />", $v['labaddr']);
  $out = <<<"END"
 <td colspan="6">
 <table class="display">
  <tr style="">
    <td style="width: 16%;"></td>
    <td style="width: 7%;"></td>
    <td style="width: 10%;"></td>
    <td style="width: 5%;"></td>
    <td style="width: 9%;"></td>
    <td style="width: 4%;"></td>
    <td style="width: 6%;"></td>
    <td style="width: 7%;"></td>
    <td style="width: 4%;"></td>
    <td style="width: 7%;"></td>
    <td style="width: 4%;"></td>
    <td style="width: 11%;"></td>
    <td style="width: 3%;"></td>
    <td style=""></td>
  </tr>
  <tr>
    <td colspan=14
      style="padding: 2px 4px; background-color: black; color: white;">
      <div
        style="text-transform: uppercase; padding-bottom: 15px; font-family: Helvetica, Arial, sans-serif; font-size: 18px;">
        part I: laboratory profile</div>
    </td>
  </tr>
  <tr>
    <td colspan="7" style="vertical-align: top;"><span class="dhead">Date
        of Audit</span> <span class="data">{$v['end_date']}</span></td>
    <td colspan="7" style="vertical-align: top;"><span class="dhead">Date
        of Last Audit</span> <span class="data">{$v['dola']}</span></td>
  </tr>
  <tr>
    <td colspan=2 class="dhead" style="vertical-align: top;">Prior Audit
      Status</td>
    <td colspan=2 class="star lg {$st['N']}">Not Audited</td>
    <td class="star lg {$st['0']}">0 Stars</td>
    <td colspan=2 class="star lg {$st['1']}">1 Star</td>
    <td colspan=2 class="star lg {$st['2']}">2 Stars</td>
    <td colspan=2 class="star lg {$st['3']}">3 Stars</td>
    <td class="star lg {$st['4']}">4 Stars</td>
    <td colspan=2 class="star lg {$st['5']}">5 Stars</td>
  </tr>
  <tr>
    <td colspan="14" class="la"><span
      class="dhead">Names and Affiliation(s) of Auditor(s)</span>
      <div class="data">
        {$v['names_affil_t']}
      </div></td>
  </tr>
  <tr>
    <td colspan=10 style="vertical-align: top;"><span class="dhead">Laboratory
        Name</span> <span class="data"><br />{$v['labname']}</span></td>
    <td colspan=4 style="vertical-align: top;"><span class="dhead">Laboratory
        Number</span> <span class="data"><br />{$v['labnum']}</span></td>
  </tr>
  <tr>
    <td colspan=14 style="vertical-align: top;"><span class="dhead">Laboratory
        Address</span> <span class="data"><br />{$v['labaddr']}</span></td>
  </tr>
  <tr>
    <td colspan=3 style="vertical-align: top;"><span class="dhead">Laboratory
        Telephone</span> <span class="data"><br />{$v['labtel']}</span></td>
    <td colspan=6 style="vertical-align: top;"><span class="dhead">Fax</span>
      <span class="data"><br />{$v['labfax']}</span></td>
    <td colspan=5 style="vertical-align: top;"><span class="dhead">Email</span><span
      class="data"><br />{$v['labemail']}</span></td>
  </tr>
  <tr>
    <td colspan=6 rowspan=2 style="vertical-align: top;"><span
      class="dhead">Head of Laboratory</span> <span class="data"><br />{$v['labhead']}</span></td>
    <td colspan=7 rowspan=2 style="vertical-align: top;"><span
      class="dhead">Telephone</span> <span class="data">(Head of
        Laboratory)</span> <span class="data"><br />{$v['labheadtel']}</span></td>
    <td style="text-align: center;" class="{$tt['P']}">Personal</td>
  </tr>
  <tr>
    <td style="text-align: center;" class="{$tt['W']}">Work</td>
  </tr>
  <tr class="lg" style="height: 35px">
    <td colspan=6 style="vertical-align: top;"><span class="dhead">Laboratory
        Level</span> <span class="data">(check only one)</span></td>
    <td colspan=8 style="vertical-align: top;"><span class="dhead">Type
        of Laboratory/Laboratory Affiliation</span> <span class="data">(check
        only one)</span></td>
  </tr>
  <tr>
    <td colspan=1 class="la">
      <div class="cbx">{$ll['N']}</div> <span class="la">National</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$ll['R']}</div> <span class="la">Reference</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$ll['P']}</div> <span class="la">Regional/Provincial</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$af['P']}</div> <span class="la">Public</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['H']}</div> <span class="la">Hospital<br />
        <br /></span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['V']}</div> <span class="la">Private</span>
    </td>
  </tr>
  <tr>
    <td colspan=1 class="la">
      <div class="cbx">{$ll['D']}</div> <span class="la">District</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$ll['Z']}</div> <span class="la">Zonal</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$ll['F']}</div> <span class="la">Field</span>
    </td>
    <td colspan=2 class="la">
      <div class="cbx">{$af['R']}</div> <span class="la">Research</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['N']}</div> <span class="la">Non-hospital
        Outpatient Clinic</span>
    </td>
    <td colspan=3 class="la">
      <div class="cbx">{$af['O']}</div> <span class="la">Other --
        Please specify:</span>
      <div style="text-decoration: underline; vertical-align: bottom;">{$v['labaffil_other']}</div>
    </td>
  </tr>
 </table>
</td>
END;
        return $out;
}
function html_panel_result($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  ///$smallint = widget_integer("{$name}_num", $value, 4);
  $smallint = get_arrval($value, "{$name}_num", '');
  //$mc_yn = widget_select_yn("{$name}_yn", $value, $t);
  $mc_yn = get_arrval($value, "{$name}_yn", '');
  //$dt = widget_dt("{$name}_dt", $value, 10);
  $dt = get_arrval($value, "{$name}_dt", '');
  //$script = '<script> $(function() {$( "' . "#{$name}_dt" . '" ).datepicker();});</script>';
  $y = ($mc_yn == 'YES') ? 'Y': '';
  $n = ($mc_yn == 'NO') ? 'N': '';
  $out = <<<"END"
<td colspan="6">
<table style="width:100%;">
  <tr>
    <td style="width:7%;" class="title c">{$prefix}</td>
    <td class="panel">{$heading}</td>
    <td style="width:14%;" class="c">{$dt}</td>
    <td style="width:9%;" class="cb">{$y}</td>
    <td style="width:9%;"class="cb">{$n}</td>
    <td style="width:10%;" class="c">{$smallint}</td>
  </tr>
</table></td>
END;
  return $out;
}

function html_info($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  //$tarea = TEXTAREA($name, $value, $style = "width:100%;height:250px;");
  $tval = get_arrval($value, $name, '');
  $out = <<<"END"
<td colspan="6">
<div style="min-height:150px;vertical-align:top;margin-left:5px;margin-top:4px;">{$tval}</div>
</td>
END;
  return $out;
}

function html_action_plan_heading($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $info = $row['info'];
  $name = $row['varname'];
  $out = <<<"END"
  <td colspan="6">
<table style="width:100%;"><tr class="dg">
    <td width="45%" class="centertopbold">{$prefix}</td>
    <td width="20%" class="centertopbold">{$heading}</td>
    <td width="12%" class="centertopbold">{$text}</td>
    <td class="centertopbold">{$info}</td>
</tr></table></td>
END;
  return $out;
}

function html_action_plan_data($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $input_style = "width:100%;height:50px;";
  $item = str_replace("\n", "<br />", get_arrval($value, "{$name}_item",''));
  $person = str_replace("\n", "<br />", get_arrval($value, "{$name}_person", ''));
  $time = str_replace("\n", "<br />", get_arrval($value, "{$name}_time",''));
  $sign = str_replace("\n", "<br />", get_arrval($value, "{$name}_sign", ''));
  $out = <<<"END"
<td colspan="6" class="nb">
  <table style="width:100%;">
  <tr style="">
    <td width="45%" style="padding:4px;vertical-align:top;height:25px;">{$item}</td>
    <td width="20%" style="padding:4px;vertical-align:top;">{$person}</td>
    <td width="12%" style="padding:4px;vertical-align:top;">{$time}</td>
    <td style="padding:4px;vertical-align:top;">{$sign}</td>
</tr></table></td>
END;

  return $out;
}

function html_pagebreak() {
  return '<td colspan="6" class="nb"><div class="pagebreak" style="height:15px;">&nbsp;</div></td>';
}
function html_sec_total($row, $value, $t) {
  $heading = $row['heading'];
  $name = $row['varname'];
  $ec = $row['element_count'];
  $max_score = $row['score'];
  $this_score = get_arrval($value, $name, 0);
  $out = <<<"END"
<td colspan=5 style="padding: 2px 4px">
  <div style="padding-top:15px;font-size:1.0625em;">
    <b>{$heading}</b>
  </div>
</td>
<td class="cb">
    {$this_score} /<span class="tiny">{$max_score}</span></td>
</td>
</tr><tr><td colspan="6" class="nb"><div class="pagebreak" style="height:15px;">&nbsp;</div></td>
END;

  return $out;
}

function html_sec_element_info($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  // $name = $row['varname'];
  $out = <<<"END"
  <td rowspan="2" style="padding-left: 10px;">{$text}
  </td>
  <td colspan=3 class="tick">
    Tick for each item</td>
  <td rowspan="2" colspan="2">
  </td>
</tr>
<tr>
  <td class="cb" style="font-size:.85em;">Yes</td>
  <td class="cb" style="font-size:.85em;">No</td>
  <td class="cb" style="font-size:.85em;">N/A</td>
END;

  return $out;
}

function html_bat_element_info($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  // $name = $row['varname'];
  $out = <<<"END"
<td></td><td></td>
<td class="cb" style="font-size:.85em;">Yes</td>
<td class="cb" style="font-size:.85em;">No</td>
<td class="cb" style="font-size:.85em;">N/A</td>
<td></td>
END;

  return $out;
}

function html_sec_elem_info($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  // $name = $row['varname'];
  $out = <<<"END"
  <td colspan=6>
  {$text}
  </td>
END;

  return $out;
}

/* Erin commented out 8/28/13
function html_sec_elem_info_normal($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<div style="width:820px;border:1px solid #ccc;
     background-color:#f0f0f0;padding:4px;font-size:14px;">
  <b>{$heading}</b> {$text}
</div>
END;
  return $out;
  } */

function html_sub_sec_info($row, $value, $t) {
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<td colspan=6 class="lg" style="padding:4px;font-size:0.75em;">
  <i><b>{$heading}</b></i>
  {$text}
</td>
END;
  return $out;
}

function html_sec_sec_head($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  return '';
}

function html_part_head($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"

<td colspan="6" style="font-size:18px;text-transform:uppercase;padding: 2px 4px;">
<div style="">
<div style="vertical-align:top;"> {$heading}</div>
</div>
</td>

END;
  return $out;
}

function html_img($row) {
  /* paint the image to the screen */
  $heading = $row['heading'];
  $baseurl = $row['baseurl'];
  $out = <<<"END"
<td colspan=6 class="nb">
  <div style="text-align:center;">
    <img style="width:130mm;height:42mm;" src="{$baseurl}/images/{$heading}" />
  </div>
</td>
END;
  return $out;
}

function html_bat_sec_head($row) {
  // $prefix = $row ['prefix'];
  $heading = $row['heading'];
  // $text = $row ['text'];
  // $name = $row ['varname'];
  $out = <<<"END"
<td colspan=6 style="padding:2px 4px;color:white;background-color:#666;">
  <div class="bat_banner_rev">
  {$heading}
</div></td>
END;
  return $out;
}

function html_bat_element($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  if ($heading) {
    $heading = $heading . '<br />';
  }
  $text = $row['text'];
  $info = $row['info'];
  $name = $row['varname'];
  $yn = get_arrval($value, "{$name}_ynaa", '');
  $ynname = "{$name}_ynaa";
  //logit("YN: {$ynname} : {$value[$ynname]} = {$yn}");
  $ch = getYNPA($yn);
  $comment = fixText(get_arrval($value, "{$name}_comment", ''));
  if ($info == '') {
  $out = <<<"END"
<td style="vertical-align:top;padding:3px 0 0 3px;font-size:0.9em;">{$prefix}</td>
<td style="padding:2px 4px;font-size:0.9em;">
  <div style="padding-left:5px;">{$text}</div>
</td>
<td class="cb {$ch['YC']}">{$ch['Y']}</td>
<td class="cb {$ch['NC']}">{$ch['N']}</td>
<td class="cb {$ch['NAC']}">{$ch['NA']}</td>
<td class="comment" style="padding:2px 4px;font-size:0.9em;">{$comment}</td>
END;
  } else {
    $out = <<<"END"
<td rowspan=2 style="vertical-align:top;padding: 3px 0 0 3px;font-size:0.9em;">{$prefix}</td>
<td style="padding: 2px 4px;">
  <div style="padding-left: 5px;font-size:0.9em;">{$text}</div>
</td>
<td class="cb {$ch['YC']}">{$ch['Y']}</td>
<td class="cb {$ch['NC']}">{$ch['N']}</td>
<td class="cb {$ch['NAC']}">{$ch['NA']}</td>
<td class="comment" style="padding: 2px 4px;font-size:0.9em;">{$comment}</td></tr>
<tr>
<td colspan="5" style="padding: 3px 4px;font-size:0.78em;font-style: italic;" class="lg">{$info}</td>
END;
  }
  return $out;
}

function html_bat_comment($row, $value, $t) {
  $name = $row['varname'];
  //$tarea = TEXTAREA($name, $value, "width:810px;height:50px;margin:6px 10px 4px 10px;");
  $ta = fixText(get_arrval($value, $name, ''));
  $out = <<<"END"
<td colspan="6">
  <div class="bat_comment">
  {$ta}
  </div>
</td>
END;
  return $out;
}

function html_ynna_ct($row, $value, $t) {
  /*
   * Yes, No and N/A count for the section
   */
  // $prefix = $row ['prefix'];
  $name = $row['varname'];
  $heading = $row['heading'];
  $v_y_ct = get_arrval($value, "{$name}_y_ct", 0);
  $v_n_ct = get_arrval($value, "{$name}_n_ct", 0);
  $v_na_ct = get_arrval($value, "{$name}_na_ct", 0);
  //$text = $row ['text'];
  //$info = $row ['info'];
  $name = $row['varname'];
  $ec = $row['element_count'];
  $out = <<<"END"
<td colspan="6">
  <div style="float:left;font-size:0.9em;"><br />{$heading}</div>
  <div style="float:right;width:50px;text-align:center;font-size:0.9em;"><b>N/A<br />{$v_na_ct}</b></div>
  <div style="float:right;width:50px;text-align:center;font-size:0.9em;"><b>No<br />{$v_n_ct}</b></div>
  <div style="float:right;width:50px;text-align:center;font-size:0.9em;"><b>Yes<br />{$v_y_ct}</b></div>
</td>
</tr><tr><td colspan="6" class="nb"><div class="pagebreak" style="height:15px;">&nbsp;</div></td>
END;

  return $out;
}

function html_ynp_ct($row, $value, $t) {
  /*
   * Yes, No and Partial count for the section
   */
  // $prefix = $row ['prefix'];
  $name = $row['varname'];
  $heading = $row['heading'];
  $v_y_ct = get_arrval($value, "{$name}_y_ct", 0);
  $v_n_ct = get_arrval($value, "{$name}_n_ct", 0);
  $v_p_ct = get_arrval($value, "{$name}_p_ct", 0);
  //$text = $row ['text'];
  //$info = $row ['info'];
  $name = $row['varname'];
  $ec = $row['element_count'];
  $out = <<<"END"
<td colspan="6">
  <div style="float:left;font-size:0.9em;"><br />{$heading}</div>
  <div style="float:right;width:50px;text-align:center;font-size:0.9em;"><b>No<br />{$v_n_ct}</b></div>
  <div style="float:right;width:50px;text-align:center;font-size:0.9em;"><b>Partial<br />{$v_p_ct}</b></div>
  <div style="float:right;width:50px;text-align:center;font-size:0.9em;"><b>Yes<br />{$v_y_ct}</b></div>
</td>
</tr><tr><td colspan="6" class="nb"><div class="pagebreak" style="height:15px;">&nbsp;</div></td>
END;

  return $out;
}

/* the bottom line */
function calculate_view($rows, $value, $langtag, $audit_type) { //$tword) {
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
  logit('VALUE: '. print_r($value, true));
  $tlist = getTranslatables($langtag); //$tword );
  $allowed_list = array(
      'action_plan_heading',
      'action_plan_data',
      'banner_rev',
      'banner_rev_border',
      'bat_comment',
      'bat_element',
      'bat_element_info',
      'bat_sec_head',
      'bat_tel_type',
      'com_and_rec',
      'criteria_1_heading',
      'criteria_1_values',
      'criteria_2_heading',
      'full',
      'full_nb',
      'img',
      'info',
      'info_i',
      'main2',
      'main_heading',
      'pagebreak',
      'panel_heading',
      'panel_result',
      'part_head',
      'prof_info',
      'prof_info_yn_html',
      'prof_info_yn_suff',
      'sec_elem_info',
      'sec_elem_ynp',
      'sec_element',
      'sec_element_info',
      'sec_element_yna',
      'sec_element_ynp',
      'sec_head',
      'sec_head_lab_info',
      'sec_head_empty',
      'sec_head_small',
      'sec_head_top',
      'sec_total',
      'slipta_tel_type',
      'sub_sec_head',
      'sub_sec_head_ro',
      'sub_sec_head_yna',
      'sub_sec_head_ynp',
      'sub_sec_info',
      'tab_head3',
      'tb_tel_type',
      //'prof_info_yn', //
      //'sec_elem_info_normal',
      //'sec_head_lab',
     'sec_elem_info_normal',
     'ynna_ct',
     'ynp_ct'
  );
  $tout = array ();
  $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
  $tout[] = '<table class="display">';
  /*  $tout[] = '<table border=0  class="display">'; */
  //logit("audit_type: {$audit_type}");
  switch ($audit_type) {
    case 'SLIPTA' :
      $tout[] = <<<"END"
<tr style="/*display:none;*/">
  <td style="width:36% !important;"></td>
  <td style="width:5.4% !important;"></td>
  <td style="width:5.4% !important;"></td>
  <td style="width:5.4% !important;"></td>
  <td style="width:41% !important;"></td>
  <td></td>
</tr>
END;
      break;
    case 'BAT' :
    case 'TB' :
      $tout[] = <<<"END"
<tr style="/*display:none;*/">
  <td style="width:4.6% !important;"></td>
  <td style="width:47% !important;"></td>
  <td style="width:4.6% !important;"></td>
  <td style="width:5.6% !important;"></td>
  <td style="width:5.6% !important;"></td>
  <td></td>
</tr>
END;
      break;
    default :
  }
  $ctr = 0;
  $slmta = false;
  foreach($rows as $row) {
    logit("ROW: {$row['varname']} - {$row['row_type']}");
  if ($row['row_type'] == 'tel_type')
    logit("at tel_type");
    $ctr++;
    $type = $row['row_type'];
    $arow = array ();
    $arow['prefix'] = get_lang_text($row['prefix'], $row['lpdefault'], $row['lplang']);
    $arow['heading'] = get_lang_text($row['heading'], $row['lhdefault'], $row['lhlang']);
    $arow['text'] = get_lang_text($row['text'], $row['ltdefault'], $row['ltlang']);
    $arow['varname'] = $row['varname'];
    $arow['info'] = get_lang_text($row['info'], $row['lidefault'], $row['lilang']); //$row['info'];
    $arow['score'] = $row['score'];
    $arow['baseurl'] = $baseurl;
    //$arow['homeurl'] = "{$baseurl}/audit/main";
    $arow['element_count'] = $row['element_count'];
    $bpad = 'class="bpad"';

    if ($type == '') {
      logit("ROW: " . print_r($row, true));
    }
    if (in_array($type, $allowed_list)) {
      if (! $slmta && substr($row['varname'], 0, 5) == 'slmta') {
        $tout[] = "<div id=\"onlyslmta\">";
        $slmta = true;
      }

      $tout[] = "<tr>" . call_user_func("html_{$type}", $arow, $value, $tlist) . '</tr>';
      if ($slmta && substr($row['varname'], 0, 5) != 'slmta') {
        $tout[] = "</div>";
        $slmta = false;
      }
    }
  }
  $tout[] = '</table>';
  return $tout;
}

