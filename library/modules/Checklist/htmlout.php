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

function widget_select_yn($varname, $value, $t) {
  $optvals = getYN($t);
  return OPTIONS($varname, $optvals, $value);
}



function widget_select_ynp($varname, $value, $t) {
  $optvals = getYNP($t);
  return OPTIONS($varname, $optvals, $value);
}

function widget_select_ynp_ro($varname, $value, $t) {
  /*
   * This is a display for calculated choices
   */
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

function html_full($row) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<div style="width:100%;">
  <div class="full">{$text}</div>
</div>
END;
  return $out;
}

function html_banner_rev($row) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<td colspan=6 class="nb">
<table class="fullwide"><tr>
  <td class="banner_rev">
  {$prefix} {$heading}
  </td></tr>
  <tr>
  <td class="normal nb">{$text}</td>
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
  $intf = INPUT($name, $value, 'integer', 3, 'margin-right:10px;', '');
  $mc_yni = widget_select_yni("{$name}_yni", $value, $t);
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:left;">
  {$intf} {$mc_yni}
</div>
</div>
END;
  return $out;
}

function html_prof_info_yn($row, $value, $t) {
  $name = $row['varname'];
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $mc_yn = widget_select_yn("{$name}_yn", $value, $t);
  $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:left;">
  {$mc_yn}
</div>
</div>
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

function html_tel_type($row, $value, $t) {
  $name = $row['varname'];
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $pwf = widget_select_pw($name, $value, $t);
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
  $out = <<<"END"
      <td class="ctb dg nsp"></td>
      <td class="cb dg nsp">Y</td>
      <td class="cb dg nsp">P</td>
      <td class="cb dg nsp">N</td>
      <td class="cb dg nsp">Comments</td>
      <td class="cb dg nsp">Score</td>
    </tr>
<tr>
<td colspan=6 style="padding: 2px 4px;color:white;background-color:black;">
  <div style="text-transform:uppercase;padding-bottom:10px;font-size:1.0625em;">
    <span>{$prefix}</span>
    <span>{$heading}</span>
  </div>
</td>
END;

  return $out;
}

function html_sec_head_lab($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $info = $row['info'];
  $name = $row['varname'];
  $max_score = $row['score'];
  $widget_nyp = widget_select_ynp_calc($name, $value, $t, $max_score);
  $head = ($heading) ? "{$heading}<br />" : "";
  $tarea = TEXTAREA("{$name}_comment", $value, "width:100%;height:50px;margin-top:5px;");
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
          </div>
        </div>
      </td>
  </tr></table>
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

function html_sec_head_small($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<table style="width:100%;">
<tr>
<td style="font-size:14px;font-weight: bold;padding: 2px 4px;">
<div style="">
<div style="vertical-align:top;">{$prefix} {$heading}</div>
</div>
</td>
</tr></table>
END;

  return $out;
}

function html_info_i($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $out = <<<"END"
<table style="width:100%;"><tr>
<td style="font-size:14px;font-style:italic;padding: 2px 4px;">
<div style="vertical-align:top;">{$text}</div>
</td>
</tr></table>
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
  $ch = getYNPA(selval);
  $comment = fixText(get_arrval($value, "{$name}_comment", ''));
  $widget_nyp = widget_select_ynp_add("{$name}_ynp", $value, $t);
  $head = ($heading) ? "{$heading}<br />" : "";
  $nscore = "{$name}_score";
  $scoreval = get_arrval($value, $nscore, 0);
  $out = <<<"END"
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
<td colspan="6" class="nb">
  <table style="width:100%;">
  <tr>
    <td class="tbw">
    <div class="bigtitlei">{$heading}</div>
    <div style="min-height:150px;vertical-align:top;margin-left:5px;margin-top:4px;">{$tval}</div>
    </td>
  </tr>
</table></td>
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
  <table style="width:100%;">
  <tr class="dg">
    <td width="7%" class="centertopbold ">{$prefix}</td>
    <td class="topbold">
      {$heading}
    </td>
    <td style="width:14%;" class="centertop">{$t['Date of panel receipt']}</td>
    <td style="width:18%;" class="centertop">{$t['Were results reported within 15 days?']}</td>
    <td style="width:10%;" class="centertopbold">{$t['Results & % Correct']}</td>
  </tr>
  </table>
END;

  return $out;
}

function html_panel_heading($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
  <table style="width:100%;">
  <tr class="lg">
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

function html_panel_heading2($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $sfield = widget_integer("{$name}_name", $value, 32);
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
<table style="width:100%;">
  <tr>
    <td style="width:7%;" class="title c">{$prefix}</td>
    <td class="panel">{$heading}</td>
    <td style="width:14%;" class="c">{$dt}</td>
    <td style="width:9%;" class="cb">{$y}</td>
    <td style="width:9%;"class="cb">{$n}</td>
    <td style="width:10%;" class="c">{$smallint}</td>
  </tr>
</table>
END;
  return $out;
}

function html_info($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $tarea = TEXTAREA($name, $value, $style = "width:100%;height:250px;");
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

function html_action_plan_heading($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $out = <<<"END"
<table style="width:100%;"><tr>
    <td width="45%" class="centertopbold">
      {$heading}
    </td>
    <td width="20%" class="centertopbold">Responsible Persons</td>
    <td width="10%" class="centertopbold">Timeline</td>
    <td class="centertopbold">Signature</td>
</tr></table>
END;
  return $out;
}

function html_action_plan_data($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $name = $row['varname'];
  $input_style = "width:100%;height:50px;";
  $item = TEXTAREA("{$name}_item", $value, $input_style);
  $person = TEXTAREA("{$name}_person", $value, $input_style);
  $time = TEXTAREA("{$name}_time", $value, $input_style);
  $sign = TEXTAREA("{$name}_sign", $value, $input_style);
  $out = <<<"END"
  <table style="width:810px;">
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
<div style="width:100%;">
  <div class="bat_banner_rev">
  {$heading}
  </div>
</div>
END;
  return $out;
}

function html_bat_element($row, $value, $t) {
  $prefix = $row['prefix'];
  $heading = $row['heading'];
  $text = $row['text'];
  $info = $row['info'];
  $dinfo = '';
  if ($info != '') {
    $dinfo = "<div style=\"border:1px solid #999;background-color:#eee;font-style:italic;\">{$info}</div>";
  }
  $name = $row['varname'];
  $mc_yna = widget_select_yna_add("{$name}_ynaa", $value, $t);
  $tarea = TEXTAREA("{$name}_comment", $value, "width:100%;height:50px;margin-top:6px;");
  $tareanc = TEXTAREA("{$name}_note", $value, "width:100%;height:50px;margin-top:6px;", 'nc');
  $ncval = get_arrval($value, $name . '_nc', 'F');
  $checked = '';
  if ($ncval === 'T') {
    $checked = 'checked';
    $vis = '';
  } else {
    $ncval = 'F';
    $vis = "display:none;";
  }
  $out = <<<"END"
<div style="width:100%;">
  <div style="padding: 2px 4px;">
    <div style="display:inline-block;width:100%x;vertical-align:top;">
      <div style="width:390px;padding-right:10px;display:inline;float:left;">
        <div style="display:inline;font-weight:bold;width:30px;vertical-align:top;">{$prefix}</div>
        <div style="display:inline-block;width:340px;">
          <div style="vertical-align:top;display:inline;">{$text}
            <div style="width:100%;text-align:right;margin-top:5px;">
              <label><input type="checkbox" id="{$name}" name="{$name}_cb" value="T" {$checked} style="margin-right:8px;"
                      onclick="toggleNCBox(this);">Non-Compliant</label>
              <input type="hidden" id="{$name}_nc" name="{$name}_nc" value="{$ncval}"/>
            </div>
          </div>
        </div>
      </div>
      <div style="width:400px;display:inline;float:left;">
        <div style="">{$mc_yna} </div>
          {$tarea}
        <div id="div{$name}_nc" style="{$vis}" >
          Notes:<br /> {$tareanc}
        </div>
      </div>
    </div>
    {$dinfo}
  </div>
</div>
END;
  return $out;
}

function html_bat_comment($row, $value, $t) {
  $name = $row['varname'];
  $tarea = TEXTAREA($name, $value, "width:810px;height:50px;margin:6px 10px 4px 10px;");
  $out = <<<"END"
<div style="width:100%;">
  <div class="bat_comment">
  {$tarea}
  </div>
</div>
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
<div style="padding: 2px; 4px;width:810px;background:#ccccff;">
  <div style="font-size:14px;padding: 4px;font-weight:bold;background:#ccccff;width:570px;float:left;">{$heading}</div>
  <div style="display:inline;float:right;margin-left:5px;background:#ccccff;">
N/A<input class="ro" name="{$name}_na_ct" id="{$name}_na_ct" value="{$v_na_ct}"
           type="text"  size="2">
  </div>
  <div style="display:inline;float:right;margin-left:5px;background:#ccccff;">
No<input class="ro" name="{$name}_n_ct" id="{$name}_n_ct" value="{$v_n_ct}"
           type="text"  size="2">
  </div>
 <div style="display:inline;float:right;margin-left:5px;background:#ccccff;">
Yes<input class="ro" name="{$name}_y_ct" id="{$name}_y_ct" value="{$v_y_ct}"
           type="text"  size="2">
  </div>
<div style="clear:both;"></div>
</div>
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
<div style="padding: 2px; 4px;width:810px;background:#ccccff;">
  <div style="font-size:14px;padding: 4px;font-weight:bold;background:#ccccff;width:570px;float:left;">{$heading}</div>
  <div style="display:inline;float:right;margin-left:5px;background:#ccccff;">
No<input class="ro" name="{$name}_n_ct" id="{$name}_n_ct" value="{$v_n_ct}"
         type="text"  size="2"> </div>
  <div style="display:inline;float:right;margin-left:5px;background:#ccccff;">
Partial<input class="ro" name="{$name}_p_ct" id="{$name}_p_ct" value="{$v_p_ct}"
              type="text"  size="2"> </div>
 <div style="display:inline;float:right;margin-left:5px;background:#ccccff;">
Yes<input class="ro" name="{$name}_y_ct" id="{$name}_y_ct" value="{$v_y_ct}"
          type="text"  size="2"> </div>
<div style="clear:both;"></div>
</div>
END;

  return $out;
}

/* the bottom line */
function calculate_view($rows, $value, $langtag) { //$tword) {
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
      'sec_elem_info_normal',
      'sec_head',
      'sub_sec_head',
      'sub_sec_head_ynp',
      'sub_sec_head_yna',
      'sub_sec_head_ro',
      'sec_elem_info',
      'sec_elem_ynp',
      'sec_element',
      'sec_element_yna',
      'sec_element_ynp',
      'sec_element_info',
      'sub_sec_info',
      'sec_total',
      'img',
      'main_heading',
      'main2',
      'banner_rev',
      'banner_rev_border',
      'sec_head_top',
      'criteria_1_heading',
      'criteria_1_values',
      'com_and_rec',
      'criteria_2_heading',
      'panel_heading',
      'panel_result',

  );
  $tout = array ();
  $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
  $tout[] = '<table class="display">';
  /*  $tout[] = '<table border=0  class="display">'; */
  $tout[] = <<<"END"
<tr style="">
  <td style="width:36% !important;">1</td>
  <td style="width:5.4% !important;">2</td>
  <td style="width:5.4% !important;">3</td>
  <td style="width:5.4% !important;">4</td>
  <td style="width:41% !important;">5</td>
  <td>6</td>
</tr>
END;
  $ctr = 0;
  $slmta = false;
  foreach($rows as $row) {
    $ctr++;
    $type = $row['row_type'];
    $arow = array ();
    $arow['prefix'] = get_lang_text($row['prefix'], $row['lpdefault'], $row['lplang']);
    $arow['heading'] = get_lang_text($row['heading'], $row['lhdefault'], $row['lhlang']);
    $arow['text'] = get_lang_text($row['text'], $row['ltdefault'], $row['ltlang']);
    $arow['varname'] = $row['varname'];
    $arow['info'] = $row['info'];
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

