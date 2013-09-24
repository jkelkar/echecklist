<?php
// -*- coding: utf-8 -*-


/**
 * using the widgets and partials fill out the row
 */

/**
 * This handles logging
 */
//require_once 'modules/Checklist/logger.php';
/*require_once 'modules/Checklist/general.php';
require_once '../application/models/DbTable/Lab.php';
require_once '../application/models/DbTable/Audit.php';
*/
/**
 * these implement low level html code generators
 */
class Checklist_Modules_Fillout
{

  public $baseurl;
  public $general;

  public function __construct()
  {
    $this->log = new Checklist_Logger();
    $this->log->logit('in fillout init');
    $this->baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $this->general = new Checklist_Modules_General();
  }

  function SELECT($name, $optvals, $value, $scr = '', $multiple = false, $divcss = '') {
    if (count($optvals) == 0) {
      throw new Exception('Optvals has no elements', 0);
    }
    $optout = array();
    //$this->log->logit('SEL NAME: '. $name );
    //$this->log->logit('SEL ARR:  ' . print_r($name, true));
    $val = $this->general->get_arrval($value, $name, '');
    $this->log->logit("SELECT: {$name} - {$val} " . print_r($val, true));

    // foreach ($value as $n => $v) { logit("Values {$n} - {$v}"); }


    foreach($optvals as $n => $v) {
      if ($multiple && is_array($val))
        $sel = (in_array($v, $val)) ? "selected=selected " : '';
      else
        $sel = ($v == $val) ? "selected=selected " : '';
        //logit("Interiem - {$val} : {$sel}: {$n} => {$v}");
      $optout[] = "<option {$sel} value=\"{$v}\">{$n}</option>";
    }
    $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $icon = ($val != '-') ? '' : "<img id=\"{$name}_icon\" src=\"{$this->baseurl}/cancel-on.png\" />";
    if ($scr == '') {
      $icon = '';
    }
    $options = implode("\n", $optout);
    $mult = ($multiple) ? 'multiple' : '';
    $namemul = ($multiple) ? "{$name}[]" : $name;
    $out = <<<"END"
   <div style="display:inline;float:left;{$divcss}"> <select name="{$namemul}" id="{$name}" data-rel="chosen" {$mult} class="select">
  {$options}
</select></div><div style="display:inline;float:left;">{$icon}</div>
END;

    if ($scr != '') {
      $out .= "<script>{$scr} </script>";
    }
    return $out;
  }

  function RADIO($name, $optvals, $value, $scr = '') {
    if (count($optvals) == 0) {
      throw new Exception('Optvals has no elements', 0);
    }
    $optout = array();
    $val = $this->general->get_arrval($value, $name, '');
    // logit("{$name} - {$val}");
    /*
   * foreach ($value as $n => $v) { logit("Values {$n} - {$v}"); }
   */
    foreach($optvals as $n => $v) {
      $sel = ($v == $val) ? "checked=\"checked\" " : '';
      /*$scr = '';
    if ($noscript == false) {
      $scr = <<<END
onclick="click_sub_sec('{$name}');"
END;
}*/
      // logit("Interiem - {$val} : {$sel}: {$n} => {$v}");
      $optout[] = "<input style=\"margin: 0 4px 0 6px;\" type=\"radio\" name=\"{$name}\" " .
           "id=\"{$name}_{$n}\" value=\"{$v}\" {$scr}  {$sel} >" .
           "<label class=\"il\" for=\"{$name}_{$n}\"> {$n}</label> ";
    }
    #$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $optout[] = ($val != '') ? '' : "<img id=\"{$name}_icon\" src=\"{$this->baseurl}/cancel-on.png\" />";
    $options = implode("\n", $optout);
    //if ($scr != '') {
    $out = $options . "\n<script> watch_radio('{$name}');</script>";
    $options;
    //}
    return $out;
  }

  function RADIO_CALC($name, $optvals, $value, $score, $scr = '') {
    if (count($optvals) == 0) {
      throw new Exception('Optvals has no elements', 0);
    }
    $optout = array();
    $val = $this->general->get_arrval($value, $name, '');
    // logit("{$name} - {$val}");
    /*
   * foreach ($value as $n => $v) { logit("Values {$n} - {$v}"); }
   */
    foreach($optvals as $n => $v) {
      $sel = ($v == $val) ? "checked=\"checked\" " : '';
      $sendid = substr($name, 0, 3);
      /*$scr = '';
    if ($noscript ==false) {
      $scr = "onclick=\"set_total('{$sendid}');\"";
      }*/
      // logit("Interiem - {$val} : {$sel}: {$n} => {$v}");
      $optout[] = "<input style=\"margin: 0 4px 0 6px;\" type=\"radio\" name=\"{$name}\" " .
           "id=\"{$name}_{$n}\" value=\"{$v}\" {$sel} {$scr}>" .
           " <label class=\"il\" for=\"{$name}_{$n}\">{$n}</label> ";
    }
    $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $optout[] = ($val != '') ? '' : "<img id=\"{$name}_icon\" src=\"{$this->baseurl}/cancel-on.png\" />";
    $options = implode("\n", $optout);
    if ($scr != '') {
      $suff = end(preg_split("/_/", $name));
      $suffix = '';
      switch ($stuff) {
      }
      $out = $options . "\n<script> watch_ynp('{$name}', {$score});</script>";
    }
    return $out;
  }

  function RADIO_ADD($name, $optvals, $value, $scr = '') {
    /*
   * Adds up the count of y, n and na
   */
    if (count($optvals) == 0) {
      throw new Exception('Optvals has no elements', 0);
    }
    $optout = array();
    $val = $this->general->get_arrval($value, $name, '');
    // logit("{$name} - {$val}");
    /*
   * foreach ($value as $n => $v) { logit("Values {$n} - {$v}"); }
   */
    foreach($optvals as $n => $v) {
      $sel = ($v == $val) ? "checked=\"checked\" " : '';
      $sendid = substr($name, 0, 3);
      // logit("Interiem - {$val} : {$sel}: {$n} => {$v}");
      /*$scr = '';
    if ($noscript == false) {
      $scr = "onclick=\"count_ynaa_add('{$sendid}');\"";
      }*/
      $optout[] = "<input style=\"margin: 0 4px 0 6px;\" type=\"radio\" name=\"{$name}\" " .
           "id=\"{$name}_{$n}\" value=\"{$v}\" {$sel} {$scr}>" .
           " <label class=\"il\" for=\"{$name}_{$n}\">{$n}</label> ";
    }
    //$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $optout[] = ($val != '') ? '' : "<img id=\"{$name}_icon\" src=\"{$this->baseurl}/cancel-on.png\" />";
    $out = implode("\n", $optout);
    //$out = $options; // . "\n<script> count_yna_add('{$name}');</script>";
    return $out;
  }

  function TEXTAREA($name, $value, $style = '', $class = '') {
    $val = $this->general->get_arrval($value, $name, '');
    $use_style = ($style == '') ? "style=\"height:50px;\"" : "style=\"{$style}\"";
    $out = <<<"END"
    <textarea {$use_style} onchange="noteChange();" name="{$name}" id="{$name}" class=" tarea {$class}">{$val}</textarea>
END;
    //logit("TA: {$name} {$out}");
    return $out;
  }

  function LABEL($name, $label_text = '', $label_style = "", $label_class = "") {
    $out = "<label for=\"{$name}\" style=\"{$label_style}\" class=\"{$label_class}\">{$label_text}</label>";
    return $out;
  }

  function BUTTON($name, $rtnval, $text, $type, $style = "", $class = "") {
    return "<button name=\"{$name}\" type=\"$type\" value=\"{$rtnval}\">{$text}</button>";
  }

  function INPUT($name, $value, $type = "string", $length = 0, $style = "", $class = '', $tabindex = '') {
    $size = $dtype = '';
    switch ($type) {
      case 'date' :
        $dtype = 'datepicker'; //input-xlarge datepicker hasDatepicker';
        $itype = 'text';
        if ($length != 0) {
          $l = strval($length);
          $size = "size=\"{$l}\" ";
        }
        break;
      case 'integer' :
      case 'integersmall' :
      case 'datetime' :
      case 'string' :
        $dtype = $type;
        $itype = 'text';
        if ($length != 0) {
          $l = strval($length);
          $size = "size=\"{$l}\" ";
        }
        break;

      case 'password' :
        // this implies a string
        $dtype = $type;
        $itype = $dtype;
        $l = strval($length);
        $size = "size=\"{$l}\" ";
        break;
      case 'submit' :
        $dtype = $type;
        $itype = $dtype;
        break;
      default :
        $dtype = 'unexpected';
    }
    $val = ($type != 'submit') ? $this->general->get_arrval($value, $name, '') : $value;
    $ti = ($tabindex != '') ? "tabindex=\"$tabindex\"" : '';
    $out = <<<"END"
<input name="{$name}" id="{$name}" onchange="noteChange();"
type="{$itype}" {$ti} class="input-xlarge {$dtype} {$class}" style="{$style}" value="{$val}" {$size} >
END;

    return $out;
  }

  function INPUT_AC($name, $value, $type = "string", $length = 0, $style = "", $class = '') {
    $size = $dtype = '';
    switch ($type) {
      case 'date' :
        $dtype = 'input-xlarge datepicker hasDatepicker';
        $itype = 'text';
        if ($length != 0) {
          $l = strval($length);
          $size = "size=\"{$l}\" ";
        }
        break;
      case 'integer' :
      case 'datetime' :
      case 'string' :
        $dtype = $type;
        $itype = 'text';
        if ($length != 0) {
          $l = strval($length);
          $size = "size=\"{$l}\" ";
        }
        break;

      case 'password' :
        // this implies a string
        $dtype = $type;
        $itype = $dtype;
        $l = strval($length);
        $size = "size=\"{$l}\" ";
        break;
      case 'submit' :
        $dtype = $type;
        $itype = $dtype;
        break;
      default :
        $dtype = 'unexpected';
    }
    $val = ($type != 'submit') ? $this->general->get_arrval($value, $name, '') : $value;
    $out = <<<"END"
<input name="{$name}" id="{$name}" onchange="noteChange();"
type="{$itype}" class="{$dtype} {$class}" value="" autocomplete="off" {$size} >
END;

    return $out;
  }

  function SELECT_LIVE($arr, $name, $value, $class = '') {
    $opts = array();
    $opts['ALL'] = 'ALL';
    foreach($arr as $a) {
      $opts[$a] = $a;
    }
    return $this->OPTIONS($name, $opts, $value);
  }

  function OPTIONS($varname, $optvals, $value, $scr = '', $multiple = false) {
    /**
   * Depending on the number of optvals we choose select or Radio buttons
   *
   * 3 or less gets Radio
   */
    // $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $ct = count($optvals);
    if (count($optvals) <= 3) {
      return $this->RADIO($varname, $optvals, $value, "onclick=\"click_sub_sec('{$varname}');\"");
    } else {
      return $this->SELECT($varname, $optvals, $value,
                    "watch_select('{$varname}', '{$this->baseurl}');", $multiple);
    }
  }

  function OPTIONS_CALC($varname, $optvals, $value, $score, $scr = '') {
    /**
   * Depending on the number of optvals we choose select or Radio buttons
   *
   * 3 or less gets Radio
   */
    // $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $ct = count($optvals);
    $sendid = substr($varname, 0, 3);
    if (count($optvals) <= 3) {
      return $this->RADIO_CALC($varname, $optvals, $value, $score,
                        "onclick=\"set_total('{$sendid}');\"");
    } else {
      return $this->SELECT($varname, $optvals, $value,
                    "watch_select('{$varname}', '{$this->baseurl}');");
    }
  }

  function OPTIONS_ADD($varname, $optvals, $value, $scr = '') {
    /**
   * Depending on the number of optvals we choose select or Radio buttons
   *
   * 3 or less gets Radio
   */
    $ct = count($optvals);
    if (count($optvals) <= 3) {
      return $this->RADIO_ADD($varname, $optvals, $value, $scr);
    } else {
      return $this->SELECT($varname, $optvals, $value, $scr);
    }
  }

  /**
 * These implement widgets each of which is responsible for an instance
 * of an input area on the screen
 */
  function widget_select_yn($varname, $value, $t) {
    $optvals = $this->general->getYN($t);
    return $this->OPTIONS($varname, $optvals, $value);
  }

  function widget_select_ynp($varname, $value, $t) {
    $optvals = $this->general->getYNP($t);
    return $this->OPTIONS($varname, $optvals, $value);
  }

  function widget_select_ynp_ro($varname, $value, $t) {
    /*
   * This is a display for calculated choices
   */
    $ro_char = "{$varname}_ynp";
    $v_ro_char = $this->general->get_arrval($value, $ro_char, 'N');
    $out = <<<"END"
<input class="ro" style="width:80px;" name="{$ro_char}" id="{$ro_char}"
       type="text" readonly="readonly" value="{$v_ro_char}" size=3>
END;
    return $out;
  }

  function widget_select_ynp_calc($varname, $value, $t, $score) {
    $optvals = $this->general->getYNP($t);
    return $this->OPTIONS_CALC($varname, $optvals, $value, $score);
  }

  function widget_select_ynp_add($varname, $value, $t) {
    $optvals = $this->general->getYNP($t);
    $sendid = substr($varname, 0, 3);
    return $this->OPTIONS_ADD($varname, $optvals, $value,
                      "onclick=\"watch_radio('{$varname}');count_ynp_add('{$sendid}');\"");
  }

  function widget_select_yna_calc($varname, $value, $t, $score) {
    $optvals = $this->general->getYNA($t);
    return $this->OPTIONS_CALC($varname, $optvals, $value, $score);
  }

  function widget_select_yna_add($varname, $value, $t) {
    $optvals = $this->general->getYNA($t);
    $sendid = substr($varname, 0, 3);
    return $this->OPTIONS_ADD($varname, $optvals, $value,
                      "onclick=\"watch_radio('{$varname}');count_ynaa_add('{$sendid}');\"");
  }

  /* function widget_select_wp($varname, $value, $t) {
  $optvals = getWP($t);
  return OPTIONS($varname, $optvals, $value);
} */
  function widget_select_yni($varname, $value, $t) {
    $optvals = $this->general->getYNI($t);
    return $this->OPTIONS($varname, $optvals, $value);
  }

  function widget_select_usertype($varname, $value, $t, $noscript = true) {
    $optvals = $this->general->getUserTypes($t);
    return $this->OPTIONS($varname, $optvals, $value, $noscript);
  }

  function dialog_usertype($row, $value, $t) {
    $varname = $row['varname'];
    return $this->widget_select_usertype($varname, $value, $t, true);
  }

  function widget_select_pw($varname, $value, $t) {
    $optvals = $this->general->getPW($t);
    return $this->OPTIONS($varname, $optvals, $value);
  }

  function widget_select_yna($varname, $value, $t) {
    $optvals = $this->general->getYNA($t);
    //$this->log->logit ( "YNA: " . print_r ( $optvals, true ) );
    return $this->OPTIONS($varname, $optvals, $value);
  }

  function getStarsRev($t) {
  }

  function widget_select_stars($varname, $value, $t) {
    $optvals = $this->general->getStars($t);
    return $this->OPTIONS($varname, $optvals, $value);
  }

  function widget_select_lablevel($varname, $value, $t, $scr = '', $multiple = false) {
    $optvals = $this->general->getLevels($t);
    return $this->OPTIONS($varname, $optvals, $value, $scr, $multiple);
  }

  function widget_select_labtype($varname, $value, $t, $scr = '', $multiple = false) {
    $optvals = $this->general->getLTypes($t);
    return $this->OPTIONS($varname, $optvals, $value, $scr, $multiple);
  }

  function widget_select_labaffil($varname, $value, $t, $scr = '', $multiple = false) {
    $optvals = $this->general->getAffiliations($t);
    return $this->OPTIONS($varname, $optvals, $value, $scr, $multiple);
  }

  function dialog_report($row, $value, $t) {
    $varname = $row['varname'];
    $optvals = $this->general->getReportTypes($t);
    return $this->SELECT($varname, $optvals, $value, '', false, 'padding: 2px 0 8px 0;');
    // return widget_select_lablevel($varname, $value, $t);
  }

  function dialog_audittype_m($row, $value, $t) {
    $varname = $row['varname'];
    $optvals = $this->general->getAuditTypes($t);
    return $this->SELECT($varname, $optvals, $value, '', true, 'padding: 2px 0 8px 0;');
    // return widget_select_lablevel($varname, $value, $t, '', true);
  }

  function dialog_lablevel($row, $value, $t) {
    $varname = $row['varname'];
    $optvals = $this->general->getLevels($t);
    return $this->SELECT($varname, $optvals, $value, '', false, 'padding: 2px 0 8px 0;');
    // return widget_select_lablevel($varname, $value, $t);
  }

  function dialog_labtype($row, $value, $t) {
    $varname = $row['varname'];
    $optvals = $this->general->getLTypes($t);
    return $this->SELECT($varname, $optvals, $value, '', false, 'padding: 2px 0 8px 0;');
  }

  function dialog_lablevel_m($row, $value, $t) {
    $varname = $row['varname'];
    $optvals = $this->general->getLevels($t);
    return $this->SELECT($varname, $optvals, $value, '', true, 'padding: 2px 0 8px 0;');
    // return widget_select_lablevel($varname, $value, $t, '', true);
  }

  function dialog_audit_type($row, $value, $t) {
    $varname = $row['varname'];
    $lab = new Application_Model_DbTable_Audit();
    $tags = $lab->getAuditTypes();
    //$this->log->logit('TAGS: ' . print_r($tags, true));
    $c = array('Select ...'=> '-');
    foreach($tags as $x) {
      foreach($x as $a => $tag) {
        $c[$tag] = strtoupper($tag);
      }
    }
    //$this->log->logit('TAGS: ' . print_r($c, true));
    // $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    return $this->SELECT($varname, $c, $value, '', false, 'padding: 2px 0 8px 0;');
  }

  function dialog_country($row, $value, $t) {
    $varname = $row['varname'];
    $lab = new Application_Model_DbTable_Lab();
    $countries = $lab->getDistinctCountries();
    //$this->log->logit('COUNTRIES: ' . print_r($countries, true));
    $c = array('Select ...'=> '-');
    foreach($countries as $x) {
      foreach($x as $a => $country) {
        $c[$country] = strtoupper($country);
      }
    }
    //$this->log->logit('COUNTRIES: ' . print_r($c, true));
    //$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    return $this->SELECT($varname, $c, $value, '', false, 'padding: 2px 0 8px 0;');
  }

  function dialog_country_m($row, $value, $t) {
    $varname = $row['varname'];
    $lab = new Application_Model_DbTable_Lab();
    $countries = $lab->getDistinctCountries();
    //$this->log->logit('COUNTRIES: ' . print_r($countries, true));
    $c = array('Select ...'=> '-');
    foreach($countries as $x) {
      foreach($x as $a => $country) {
        $c[$country] = strtoupper($country);
      }
    }
    //$this->log->logit('COUNTRIES: ' . print_r($c, true));
    //$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    return $this->SELECT($varname, $c, $value, '', true, 'padding: 2px 0 8px 0;');
  }

  function dialog_cohortid_m($row, $value, $t) {
    $varname = $row['varname'];
    $audit = new Application_Model_DbTable_Audit();
    $cohorts = $audit->getDistinctCohorts();
    //$this->log->logit('COHORTS: ' . print_r($cohorts, true));
    $c = array('Select ...'=> '-');
    foreach($cohorts as $x) {
      foreach($x as $a => $cohort) {
        if ($cohort == '')
          continue;
        $c[$cohort] = strtoupper($cohort);
      }
    }
    return $this->SELECT($varname, $c, $value, '', true, 'padding: 2px 0 8px 0;');
  }

  function dialog_cohortid($row, $value, $t) {
    $varname = $row['varname'];
    $audit = new Application_Model_DbTable_Audit();
    $cohorts = $audit->getDistinctCohorts();
    $this->log->logit('COHORTID: ' . print_r($cohorts, true));
    $c = array('Select ...'=> '-');
    foreach($cohorts as $x) {
      foreach($x as $a => $cohort) {
        if ($cohort == '')
          continue;
        $c[$cohort] = strtoupper($cohort);
      }
    }
    return $this->SELECT($varname, $c, $value, '', false, 'padding: 2px 0 8px 0;');
  }

  function dialog_labaffil($row, $value, $t) {
    $varname = $row['varname'];
    $optvals = $this->general->getAffiliations($t);
    return $this->SELECT($varname, $optvals, $value, '', false, 'padding: 2px 0 8px 0;');
    //return widget_select_labaffil($varname, $value, $t);
  }

  function dialog_labaffil_m($row, $value, $t) {
    $varname = $row['varname'];
    $optvals = $this->general->getAffiliations($t);
    return $this->SELECT($varname, $optvals, $value, '', true, 'padding: 2px 0 8px 0;');
    //return widget_select_labaffil($varname, $value, $t, '', true);
  }

  function widget_select_slmtatypes($varname, $value, $t) {
    $optvals = $this->general->getSLMTATypes($t);
    return $this->OPTIONS($varname, $optvals, $value, '');
  }

  function dialog_slmtastatus_m($row, $value, $t) {
    $varname = $row['varname'];
    $optvals = $this->general->getSLMTATypes($t);
    //$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    return $this->SELECT($varname, $optvals, $value, '', true, 'padding: 2px 0 8px 0;');
  }

  function dialog_slmtastatus($row, $value, $t) {
    $varname = $row['varname'];
    $optvals = $this->general->getSLMTATypes($t);
    //$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    return $this->SELECT($varname, $optvals, $value, '', false, 'padding: 2px 0 8px 0;');
  }

  function dialog_auditstates_m($row, $value, $t) {
    $varname = $row['varname'];
    $optvals = $this->general->getAuditStates($t);
    //$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    return $this->SELECT($varname, $optvals, $value, '', true, 'padding: 2px 0 8px 0;');
  }

  function dialog_auditstates($row, $value, $t) {
    $varname = $row['varname'];
    $optvals = $this->general->getAuditStates($t);
    //$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    return $this->SELECT($varname, $optvals, $value, '', false, 'padding: 2px 0 8px 0;');
  }

  function widget_select_slmtatype($varname, $value, $t) {
    $optvals = $this->general->getSLMTAType($t);
    //$baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    return $this->SELECT($varname, $optvals, $value, "watch_select('{$varname}', '{$this->baseurl}');",
                  'padding: 2px 0 8px 0;');
  }

  function dialog_slmta_type($row, $value, $t) {
    $varname = $row['varname'];
    return $this->widget_select_slmtatype($varname, $value, $t, '', true,
                                  'padding: 2px 0 8px 0;');
  }

  function widget_dt($name, $value, $length = 14) {
    return $this->INPUT($name, $value, 'date', $length);
  }

  function widget_text100($name, $value) {
    return $this->INPUT($name, $value, 'string', 100);
  }

  function widget_text255($name, $value) {
    return $this->INPUT($name, $value, 'string', 255);
  }

  function widget_integer($name, $value, $length = 0) {
    return $this->INPUT($name, $value, 'integer', $length);
  }

  function widget_integersmall($name, $value, $length = 0) {
    return $this->INPUT($name, $value, 'integersmall', $length);
  }

  function partial_main_heading($row) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $out = <<<"END"
<div style="width:100%;">
  <center><div class="maintitle">
    {$heading}
  </div></center>
</div>
END;
    return $out;
  }

  function partial_main2($row) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $out = <<<"END"
<div style="width:100%;">
  <center><div class="maintitle2">
    {$heading}
  </div></center>
</div>
END;
    return $out;
  }

  function partial_normal($row) {
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

  function partial_full($row) {
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

  function partial_full_nb($row) {
    return ($this->partial_full($row));
  }

  function partial_banner_rev($row) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $out = <<<"END"
<div style="width:100%;">
  <div class="banner_rev">
  {$prefix} {$heading}
  </div>
  <div class="normal">{$text}</div>
</div>
END;
    return $out;
  }

  function partial_banner_rev_border($row) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $out = <<<"END"
<div style="width:100%;">
  <div class="banner_rev">
  {$prefix} {$heading}
  </div>
  <div class="normal_border">{$text}</div>
</div>
END;
    return $out;
  }

  /**
 * These are the representations of a row on the screen
 */
  function partial_stars($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $stars = $this->widget_select_stars("{$name}", $value, $t);
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

  function partial_string_field($row, $value, $t) {
    $name = $row['varname'];
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $stringf = $this->INPUT($name, $value, 'string', 55, '', '');
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

  function partial_string_ro($row, $value, $t) {
    $name = $row['varname'];
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    //$stringf = INPUT($name, $value, 'string', 55, '', '');
    $val = $this->general->get_arrval($value, $name, '-');
    //$this->log->logit("739: {$val} - {$name}");
    switch ($name) {
      case 'slmta_labtype' :
        $rev_lt = $this->general->rev('getLTypes', $t);
        $val = $rev_lt[$val];
        break;
      case 'lablevel' :
        $rev_lt = $this->general->rev('getLevels', $t);
        $val = $rev_lt[$val];
        break;
      case 'labaffil' :
        $rev_lt = $this->general->rev('getAffiliations', $t);
        $val = $rev_lt[$val];
        break;
      default :
    }
    $this->log->logit("RO: {$name} --- {$val}");
    $out = <<<"END"
<div style="width:100%;">
<div style="vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left;">
  {$text}
</div>
<div style="vertical-align:top;width:400px;float:left;color:#3366cc;">
  {$val}
</div>
</div>
END;
    return $out;
  }

  function dialog_string_field($row, $value, $t) {
    $name = $row['varname'];
    $flength = $row['field_length'];
    $out = $this->INPUT($name, $value, 'string', '', '', 'text d');
    return $out;
  }

  function dialog_password_field($row, $value, $t) {
    $name = $row['varname'];
    $flength = $row['field_length'];
    $out = $this->INPUT($name, $value, 'password', '', '', 'password d');
    return $out;
  }

  function dialog_submit_button($row, $value, $t) {
    $name = $row['varname'];
    $flength = $row['field_length'];
    $val = $row['field_label'];
    $names = preg_split('/,/', $val);
    $out = '';
    //$ti = - 2;
    foreach($names as $text) {
      if ($text != 'Cancel') {
        //$ti ++;
        //if ($ti < - 1)
        //  $ti = 1;
        $out .= $this->INPUT($name, $text, 'submit', '', '', 'submit');//, $ti);
      } else {
        $out .= $this->INPUT($name, $text, 'submit', '', '', 'submit');
      }
    }
    return $out;
  }

  function partial_prof_info($row, $value, $t) {
    $name = $row['varname'];
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $intf = $this->INPUT($name, $value, 'integer', 3, 'margin-right:10px;', '');
    $mc_yni = $this->widget_select_yni("{$name}_yni", $value, $t);
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

  function partial_prof_info_yn_suff($row, $value, $t) {
    return $this->partial_prof_info_yn($row, $value, $t);
  }

  function partial_prof_info_yn($row, $value, $t) {
    $name = $row['varname'];
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $mc_yn = $this->widget_select_yn("{$name}_yn", $value, $t);
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

  function partial_integer_field($row, $value, $t) {
    $name = $row['varname'];
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $intf = $this->INPUT($name, $value, 'integer', 0, '', '');
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

  function partial_text_field($row, $value, $t) {
    $name = $row['varname'];
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $tarea = $this->TEXTAREA("{$name}_comment", $value,
                      "width:395px;height:50px;margin-top:5px;");
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

  function partial_text_ro($row, $value, $t) {
    $name = $row['varname'];
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    // $tarea = TEXTAREA("{$name}_comment", $value, "width:395px;height:50px;margin-top:5px;");
    $val = $this->general->get_arrval($value, $name, '');
    $val = str_replace("\n", '<br />', $val);
    $out = <<<"END"
<div style="width:100%;">
<div style="display:inline-block;vertical-align:top;padding-right:10px;width:390px;text-align:right;float:left">
  {$text}
</div>
<div style="display:inline-block;vertical-align:top;width:400px;float:left;color:#3366cc;">
  {$val}
</div>
</div>
END;
    return $out;
  }

  function partial_date_field($row, $value, $t) {
    $name = $row['varname'];
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $datef = $this->INPUT($name, $value, 'date', 14, '', '');
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
  // the next three as just being aliased
  function partial_slipta_date_field($row, $value, $t) {
    return $this->partial_date_field($row, $value, $t);
  }

  function partial_bat_date_field($row, $value, $t) {
    return $this->partial_date_field($row, $value, $t);
  }

  function partial_tb_date_field($row, $value, $t) {
    return $this->partial_date_field($row, $value, $t);
  }
  function partial_tb_comment ($row, $value, $t){
    return '';
  }
  function dialog_date_field($row, $value, $t) {
    $name = $row['varname'];
    //$prefix = $row ['prefix'];
    //$heading = $row ['heading'];
    //$text = $row ['text'];
    $datef = $this->INPUT($name, $value, 'date', 0, '', '');
    // $script = '<script> $(function() {$( "' . "#{$name}" . '" ).datepicker();});</script>';
    $out = $datef;
    return $out;
  }

  function partial_tel_type($row, $value, $t) {
    $name = $row['varname'];
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $pwf = $this->widget_select_pw($name, $value, $t);
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

  function partial_slipta_tel_type($row, $value, $t) {
    return $this->partial_tel_type($row, $value, $t);
  }

  function partial_bat_tel_type($row, $value, $t) {
    return $this->partial_tel_type($row, $value, $t);
  }

  function partial_tb_tel_type($row, $value, $t) {
    return $this->partial_tel_type($row, $value, $t);
  }


  function partial_sec_head($row, $value, $t) {
    $name = $row['varname'];
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $secinc = "{$name}_secinc";
    $incval = $this->general->get_arrval($value, $secinc, 999);
    $out = <<<"END"
<table style="width:100%;"><tr>
<td style="font-size:18px;font-weight: bold;text-transform:uppercase;padding: 2px 4px;">
<div style=""><input type="hidden" id="{$secinc}" name="{$secinc}" value="{$incval}"/>
<div style="vertical-align:top;"> {$prefix} {$heading}</div>
</div>
</td>
</tr></table>
END;

    return $out;
  }

  function partial_sec_head_lab($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $head = ($heading) ? "{$heading}<br />" : "";
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

  function partial_sec_head_lab_info($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $out = <<<"END"
  <table style="width:100%;"><tr>
      <td style="padding: 2px 4px;">
        <div style="display:inline-block;width:100%x;vertical-align:top;">
          <div style="width:788px;display:inline;">
            <div style="display:inline;font-weight:bold;width:25px;vertical-align:top;"></div>
            <div style="display:inline-block;width:755px;">
              <div style="text-decoration:underline;font-weight:bold;display:inline;"></div>
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

  function partial_sec_head_top($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $out = <<<"END"
<div style="width:100%;border:1px solid #ccc;background-color:#f0f0f0;padding: 4px;font-size:14px;">
<b>{$heading}</b> {$text}
</div>
END;

    return $out;
  }

  function partial_sec_head_small($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
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
<table style="width:100%;"><tr>
<td style="font-size:14px;padding: 2px 4px;">
    <div style="vertical-align:top;"><b>{$heading}</b> {$text}</div>
</td>
</tr></table>
END;

    return $out;
  }

  function partial_sub_sec_head($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $info = $row['info'];
    $name = $row['varname'];
    $ec = $row['element_count'];
    $max_score = $row['score'];
    $widget_nyp = $this->widget_select_ynp_calc($name, $value, $t, $max_score);
    $head = ($heading) ? "{$heading}<br />" : "";
    $tarea = $this->TEXTAREA("{$name}_comment", $value, "width:100%;height:50px;margin-top:5px;");
    $tareanc = $this->TEXTAREA("{$name}_note", $value, "width:100%;height:50px;margin-top:6px;",
                        'nc');
    $incval = $this->general->get_arrval($value, "{$name}_inc", 1); // incomplete counts for this sub section
    $ncval = $this->general->get_arrval($value,
                                                                          $name . '_nc',
                                                                          'F');
    $checked = '';
    $nscore = "{$name}_score";
    //$this->log->logit("NSCORE: ". $nscore);
    $scoreval = $this->general->get_arrval($value, $nscore, 0);
    if ($ncval == 'T') {
      $checked = 'checked';
      $vis = '';
    } else {
      $ncval = 'F';
      $vis = "display:none;";
    }
    $out = <<<"END"
<table style="width:810px;">
  <tr>
    <td style="padding: 2px 4px;vertical-align:top;">
      <div style="display:inline-block;width:440px;vertical-align:top;">
        <div style="width:438px;display:inline;">
          <div style="display:inline;font-weight:bold;width:25px;vertical-align:top;">{$prefix}</div>
          <div style="display:inline-block;width:395px;">
            <div style="text-decoration:underline;font-weight:bold;display:inline;">{$head}</div>
            <div style="vertical-align:top;display:inline;">{$text}
            </div>
          </div>
          <div
             style="font-style:italic;font-weight:bold;font-size:10px;margin-top:5px;">{$info}</div>
        </div>
      </div>
    </td>
    <td style="vertical-align:top;padding: 2px 4px;width:350px;">
      <div style="margin-right:5px;display:inline;">{$widget_nyp}</div>
      <div style="display:inline;float:right;">
        <input class="ro" name="{$name}_score" id="{$name}_score" value="{$scoreval}" rel="{$ec}"
               type="text"  size="2"><input type="hidden" id="{$name}_inc" name="{$name}_inc" value="{$incval}"/>
        / <b>{$max_score}</b></div>
      <div>{$tarea}</div>
      <div style="width:100%;text-align:left;margin-left:13px;">
                <label><input type="checkbox" id="{$name}" name="{$name}_cb" value="T" {$checked} style="margin-right:8px;"
                              onclick="toggleNCBox(this);">Non-Compliant</label>
                <input type="hidden" id="{$name}_nc" name="{$name}_nc" value="{$ncval}"/>
            </div>
      <div id="div{$name}_nc" style="{$vis}" >
        Non Compliance Notes:<br />
        {$tareanc}
      </div>
    </td>
</tr></table>
END;

    return $out;
  }

  function partial_sub_sec_head_ynp($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $info = $row['info'];
    $name = $row['varname'];
    $ec = $row['element_count'];
    //$max_score = $row ['score'];
    $widget_nyp = $this->widget_select_ynp_add("{$name}_ynp", $value, $t);
    $head = ($heading) ? "{$heading}<br />" : "";
    $tarea = $this->TEXTAREA("{$name}_comment", $value, "width:100%;height:50px;margin-top:5px;");
    $tareanc = $this->TEXTAREA("{$name}_note", $value, "width:100%;height:50px;margin-top:6px;",
                        'nc');
    $ncval = $this->general->get_arrval($value, $name . '_nc', 'F');
    $checked = '';
    $nscore = "{$name}_score";
    //$this->log->logit("NSCORE: ". $nscore);
    $scoreval = $this->general->get_arrval($value, $nscore, 0);
    if ($ncval == 'T') {
      $checked = 'checked';
      $vis = '';
    } else {
      $ncval = 'F';
      $vis = "display:none;";
    }
    $out = <<<"END"
<table style="width:810px;">
  <tr>
    <td style="padding: 2px 4px;vertical-align:top;">
      <div style="display:inline-block;width:440px;vertical-align:top;">
        <div style="width:438px;display:inline;">
          <div style="display:inline;font-weight:bold;width:25px;vertical-align:top;">{$prefix}</div>
          <div style="display:inline-block;width:405px;">
            <div style="text-decoration:underline;font-weight:bold;display:inline;">{$head}</div>
            <div style="vertical-align:top;display:inline;">{$text}<br />
              <!--div style="width:100%;text-align:right;margin-top:5px;">
                <label><input type="checkbox" id="{$name}" name="{$name}_cb" value="T" {$checked} style="margin-right:8px;"
                              onclick="toggleNCBox(this);">Non-Compliant</label>
                <input type="hidden" id="{$name}_nc" name="{$name}_nc" value="{$ncval}"/>
            </div--></div>
          </div>
          <div
             style="font-style:italic;font-weight:bold;font-size:10px;margin-top:5px;">{$info}</div>
        </div>
      </div>
    </td>
    <td style="vertical-align:top;padding: 2px 4px;width:350px;">
      <div style="margin-right:5px;display:inline;">{$widget_nyp}</div>
      <div>{$tarea}</div>
      <div style="width:100%;margin-top:5px;text-align:left;padding-left:13px;">
                <label><input type="checkbox" id="{$name}" name="{$name}_cb" value="T" {$checked} style="margin-right:8px;"
                              onclick="toggleNCBox(this);">Non-Compliant</label>
                <input type="hidden" id="{$name}_nc" name="{$name}_nc" value="{$ncval}"/>
            </div>
      <div id="div{$name}_nc" style="{$vis}" >
        Non Compliance Notes:<br />
        {$tareanc}
      </div>
    </td>
</tr></table>
END;

    return $out;
  }

  function partial_sub_sec_head_ro($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $info = $row['info'];
    $name = $row['varname'];
    $ec = $row['element_count'];
    $max_score = $row['score'];
    $widget_nyp_ro = $this->widget_select_ynp_ro($name, $value, $t);
    $ynp_ro = "{$name}_ynp";
    $nscore = "{$name}_score";
    //$this->log->logit("NSCORE: ". $nscore);
    $scoreval = $this->general->get_arrval($value, $nscore, 0);
    $this_score = $this->general->get_arrval($value, $ynp_ro, 0);
    $incval = $this->general->get_arrval($value, "{$name}_inc", "{$ec}"); // incomplete counts for this sub section
    $ncval = $this->general->get_arrval($value,
                                                                                $name .
                                                                                     '_nc',
                                                                                    'F');
    $checked = '';
    $head = ($heading) ? "{$heading}<br />" : "";
    //$this->log->logit ( "SRO: " . print_r ( $row, true ) );
    $tarea = $this->TEXTAREA("{$name}_comment", $value,
                          "width:100%;height:50px;margin-top:5px;");
    $tareanc = $this->TEXTAREA("{$name}_note", $value, "width:100%;height:50px;margin-top:6px;",
                        'nc');
    if ($ncval == 'T') {
      $checked = 'checked';
      $vis = '';
    } else {
      $ncval = 'F';
      $vis = "display:none;";
    }
    $out = <<<"END"
  <table style="width:810px;"><tr>
      <td style="padding: 2px 4px;vertical-align:top;">
        <div style="display:inline-block;width:440px;vertical-align:top;">
          <div style="width:438px;display:inline;">
            <div style="display:inline;font-weight:bold;width:25px;vertical-align:top;">{$prefix}</div>
            <div style="display:inline-block;width:395px;">
              <div style="text-decoration:underline;font-weight:bold;display:inline;">{$head}</div>
              <div style="vertical-align:top;display:inline;">{$text}
              </div>
            </div>
            <div
            style="font-style:italic;font-weight:bold;font-size:10px;margin-top:5px;">{$info}</div>
          </div>
      </td>
      <td style="vertical-align:top;padding: 2px 4px;width:350px;">
      <div style="display:inline;float:right;margin-right:21px;">
        <input type="hidden" id="{$name}_inc" name="{$name}_inc" value="{$incval}"/>
        <input class="ro" name="{$name}_score" id="{$name}_score" value="{$scoreval}" rel="{$ec}"
               type="text"  size="2" onclick="set_score('{$name}_score', {$max_score});"> / <b>{$max_score}</b></div>
      <div style="margin-right:5px;display:inline;float:right;">{$widget_nyp_ro}</div>
          <div>{$tarea}</div>
          <div style="width:100%;text-align:left;margin-left:13px;">
                <label><input type="checkbox" id="{$name}" name="{$name}_cb" value="T" {$checked} style="margin-right:8px;"
                              onclick="toggleNCBox(this);">Non-Compliant</label>
                <input type="hidden" id="{$name}_nc" name="{$name}_nc" value="{$ncval}"/>
            </div>
      <div id="div{$name}_nc" style="{$vis}" >
        Non Compliance Notes:<br />
        {$tareanc}
      </div>
      </td>
  </tr></table>
END;

    return $out;
  }

  function partial_sec_element_yna($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    if ($heading) {
      $heading = $heading . '<br />';
    }
    $text = $row['text'];
    $info = $row['info'];
    $name = $row['varname'];
    $mc_yna = $this->widget_select_yna("{$name}_yna", $value, $t);
    $tarea = $this->TEXTAREA("{$name}_comment", $value, "width:100%;height:50px;margin-top:6px;");
    $tareanc = $this->TEXTAREA("{$name}_note", $value, "width:100%;height:50px;margin-top:6px;",
                        'nc');
    $ncval = $this->general->get_arrval($value, $name . '_nc', 'F');
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
      <td style="vertical-align:top;padding: 2px 4px;width:440px;">
        <div style="display:inline-block;vertical-align:top;">
          <div style="width:395px;">
            <div style="width:100%">
              <div style="vertical-align:top;display:inline;">{$prefix}</div>
              <div style="text-decoration:underline;font-weight:bold;vertical-align:top;display:inline;">{$heading}</div>
              <div style="vertical-align:top;display:inline;">{$text}
              </div>
            </div>
          </div>
          <div style="font-style:italic;font-weight:bold;font-size:10px;margin-top:4px;">{$info}</div>
        </div>
      </td>
      <td  style="vertical-align:top;padding: 2px 4px;width:350px;">
        <div style="">{$mc_yna} </div>
        {$tarea}
        <div style="width:100%;text-align:left;margin-left:13px;">
                  <label><input type="checkbox" id="{$name}" name="{$name}_cb" value="T" {$checked} style="margin-right:8px;"
                          onclick="toggleNCBox(this);">Non-Compliant</label>
                  <input type="hidden" id="{$name}_nc" name="{$name}_nc" value="{$ncval}"/>
                </div>
        <div id="div{$name}_nc" style="{$vis}" >
        Non Compliance Notes:<br />
        {$tareanc}
        </div>
      </td>
</tr></table>
END;
    $outx = <<<"END"
  <table style="width=100%;"><tr>
      <td style="vertical-align:top;padding: 2px 4px;width:440px;">
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
        Non Compliance Notes:<br />
        {$tareanc}
        </div>
      </td>
</tr></table>
END;

    return $out;
  }

  function partial_sec_element_ynp($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    if ($heading) {
      $heading = $heading . '<br />';
    }
    $text = $row['text'];
    $info = $row['info'];
    $name = $row['varname'];
    $mc_ynp = $this->widget_select_ynp("{$name}_ynp", $value, $t);
    $tarea = $this->TEXTAREA("{$name}_comment", $value, "width:100%;height:50px;margin-top:6px;");
    $tareanc = $this->TEXTAREA("{$name}_note", $value, "width:100%;height:50px;margin-top:6px;",
                        'nc');
    $ncval = $this->general->get_arrval($value, $name . '_nc', 'F');
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
      <td style="vertical-align:top;padding: 2px 4px;width:440px;">
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
        <div style="">{$mc_ynp} </div>
        {$tarea}
        <div id="div{$name}_nc" style="{$vis}" >
        Non Compliance Notes:<br />
        {$tareanc}
        </div>
      </td>
</tr></table>
END;

    return $out;
  }

  function partial_sec_element($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    if ($heading) {
      $heading = $heading . '<br />';
    }
    $text = $row['text'];
    $info = $row['info'];
    $name = $row['varname'];
    $mc_yn = $this->widget_select_yn("{$name}_yn", $value, $t);
    $tarea = $this->TEXTAREA("{$name}_comment", $value, "width:100%;height:50px;margin-top:6px;");
    $tareanc = $this->TEXTAREA("{$name}_note", $value, "width:100%;height:50px;margin-top:6px;",
                        'nc');
    $ncval = $this->general->get_arrval($value, $name . '_nc', 'F');
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
      <td style="vertical-align:top;padding: 2px 4px;width:440px;">
        <div style="display:inline-block;vertical-align:top;">
          <div style="width:395px;">
            <div style="width:100%">
              <div style="vertical-align:top;display:inline;">{$prefix}</div>
              <div style="text-decoration:underline;font-weight:bold;vertical-align:top;display:inline;">{$heading}</div>
              <div style="vertical-align:top;display:inline;">{$text}
              </div>
            </div>
          </div>
          <div style="font-style:italic;font-weight:bold;font-size:10px;margin-top:4px;">{$info}</div>
        </div>
      </td>
      <td  style="vertical-align:top;padding: 2px 4px;width:350px;">
        <div style="">{$mc_yn} </div>
        {$tarea}
        <div style="width:100%;text-align:left;margin-left:13px;">
                  <label><input type="checkbox" id="{$name}" name="{$name}_cb" value="T" {$checked} style="margin-right:8px;"
                          onclick="toggleNCBox(this);">Non-Compliant</label>
                  <input type="hidden" id="{$name}_nc" name="{$name}_nc" value="{$ncval}"/>
                </div>
        <div id="div{$name}_nc" style="{$vis}" >
        Non Compliance Notes:<br />
        {$tareanc}
        </div>
      </td>
</tr></table>
END;

    return $out;
  }

  function partial_lablevel($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $mc_lab_level = $this->widget_select_lablevel($name, $value, $t);
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

  function partial_labtype($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $mc_lab_type = $this->widget_select_labtype($name, $value, $t);
    $out = <<<"END"
<table style="width:100%;"><tr>
<td style="vertical-align:top;padding-right:10px;width:390px;text-align:right;">
{$text}
</td>
<td style="vertical-align:top;padding: 2px 4px;width:400px;float:left;">
{$mc_lab_type}
</td>
</tr></table>
END;

    return $out;
  }

  function partial_slipta_official($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $val = $this->general->get_arrval($value, $name, 'F');
    //$this->log->logit("VAL: {$name} {$val}");
    $checked = ($val == 'T') ? 'checked' : '';
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

  function partial_slmta_type($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $mc_slmta_status = $this->widget_select_slmtatypes($name, $value, $t);
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

  function partial_labaffil($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $mc_lab_affil = $this->widget_select_labaffil($name, $value, $t);

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

  function partial_date($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $dt = $this->widget_dt($name, $value);
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
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $tarea = $this->TEXTAREA($name, $value);
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

  function partial_pinfo($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $smallint = $this->widget_integer("{$name}_num", $value, 4);
    $mc_yni = $this->widget_select_yni("{$name}_yni", $value, $t);
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

  function partial_pinfo2_i($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $smallint = $this->widget_integer("{$name}_num", $value);
    $mc_yn = $this->widget_select_yn("{$name}_yn", $value, $t);
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
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $smallint = $this->widget_integer("{$name}_num", $value);
    $mc_yn = $this->widget_select_yn("{$name}_yn", $value, $t);
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
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
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
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $i11 = $this->widget_integersmall("{$name}_qnt_d", $value, 4);
    $i12 = $this->widget_integersmall("{$name}_qnt_w", $value, 4);
    $i13 = $this->widget_integersmall("{$name}_qnt_er", $value, 4);
    $i21 = $this->widget_integersmall("{$name}_sqt_d", $value, 4);
    $i22 = $this->widget_integersmall("{$name}_sqt_w", $value, 4);
    $i23 = $this->widget_integersmall("{$name}_sqt_er", $value, 4);
    $i31 = $this->widget_integersmall("{$name}_qlt_d", $value, 4);
    $i32 = $this->widget_integersmall("{$name}_qlt_w", $value, 4);
    $i33 = $this->widget_integersmall("{$name}_qlt_er", $value, 4);
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
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $tarea = $this->TEXTAREA($name, $value, $style = "width:100%;height:400px;");
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
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $out = <<<"END"
  <table style="width:100%;">
  <tr>
    <td width="7%" rowspan="2" class="centertopbold">{$prefix}</td>
    <td rowspan="2" class="topbold">
      {$heading}
    </td>
    <td style="width:13%;" class="centertop">{$t['Date of panel receipt']}</td>
    <td style="width:19%;" class="centertop">{$t['Were results reported within 15 days?']}</td>
    <td style="width:10%;" class="centertopbold">{$t['Results & % Correct']}</td>
  </tr>
  </table>
END;

    return $out;
  }

  function partial_panel_heading($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
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
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $sfield = $this->widget_integer("{$name}_name", $value, 32);
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
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $smallint = $this->widget_integer("{$name}_num", $value, 4);
    $mc_yn = $this->widget_select_yn("{$name}_yn", $value, $t);
    $dt = $this->widget_dt("{$name}_dt", $value, 10);
    $script = '<script> $(function() {$( "' .
         "#{$name}_dt" .
         '" ).datepicker();});</script>';
    $out = <<<"END"
<table style="width:100%;">
  <tr>
    <td style="width:7%;" class="title">{$prefix}</td>
    <td class="panel">{$heading}</td>
    <td style="width:13%;">{$dt} {$script}</td>
    <td style="width:19%;">{$mc_yn}</td>
    <td style="width:10%;">{$smallint}</td>
  </tr>
</table>
END;
    return $out;
  }

  function partial_info($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $tarea = $this->TEXTAREA($name, $value, $style = "width:100%;height:250px;");
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
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $info = $row['info'];
    $out = <<<"END"
<table style="width:100%;"><tr>
    <td width="45%" class="centertopbold">{$prefix}</td>
    <td width="20%" class="centertopbold">{$heading}</td>
    <td width="10%" class="centertopbold">{$text}</td>
    <td class="centertopbold">{$info}</td>
</tr></table>
END;
    return $out;
  }

  function partial_action_plan_data($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $name = $row['varname'];
    $input_style = "width:96%;height:50px;";
    $item = $this->TEXTAREA("{$name}_item", $value, $input_style);
    $input_style = "width:92%;height:50px;";
    $person = $this->TEXTAREA("{$name}_person", $value, $input_style);
    $input_style = "width:83%;height:50px;";
    $time = $this->TEXTAREA("{$name}_time", $value, $input_style);
    $input_style = "width:100%;height:50px;";
    $sign = $this->TEXTAREA("{$name}_sign", $value, $input_style);
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
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    //$text = $row ['text'];
    //$info = $row ['info'];
    $name = $row['varname'];
    $ec = $row['element_count'];
    $max_score = $row['score'];
    $widget_nyp_ro = $this->widget_select_ynp_ro($name, $value, $t);
    $ynp_ro = "{$name}_ynp";
    $this_score = $this->general->get_arrval($value, $ynp_ro, 0);
    $head = ($heading) ? "{$heading}<br />" : "";
    $this_score = $this->general->get_arrval($value, $name, 0);
    $out = <<<"END"
<table style="width:100%;"><tr>
    <td style="padding: 2px 4px;width:722px;background:#ccccff;">
    <div style="font-size:16px;font-weight:bold;background;#ccccff;">{$heading}
</div>
    </td>
    <td style="vertical-align:top;padding: 2px 4px;width:76px;background:#ccccff;">
      <div style="display:inline;">
        <input class="ro" name="{$name}" id="{$name}" value="{$this_score}" rel="{$ec}"
               type="text"  size="2"> / <b>{$max_score}</b></div>
    </td>
</tr></table>
END;

    return $out;
  }

  function partial_sec_element_info($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    // $name = $row['varname'];
    $out = <<<"END"
  {$text}
END;

    return $out;
  }

  function partial_sec_elem_info($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    // $name = $row['varname'];
    $out = <<<"END"
  {$text}
END;

    return $out;
  }

  function partial_sec_elem_info_normal($row, $value, $t) {
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
  }

  function partial_sub_sec_info($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $out = <<<"END"
<div style="width:820px;border:1px solid #ccc;
     background-color:#f0f0f0;padding: 4px;font-size:12px;">
  <i><b>{$heading}</b> {$text}</i>
</div>
END;
    return $out;
  }

  function partial_sec_sec_head($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    return '';
  }

  function partial_part_head($row, $value, $t) {
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

  function partial_img($row) {
    /* paint the image to the screen */
    $heading = $row['heading'];
    $baseurl = $row['baseurl'];
    $out = <<<"END"
<div style="width:100%;">
    <img style="width:797px;" src="{$baseurl}/images/{$heading}" />
</div>
END;
    return $out;
  }

  function partial_bat_sec_head($row) {
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

  function partial_bat_element($row, $value, $t) {
    $prefix = $row['prefix'];
    $heading = $row['heading'];
    $text = $row['text'];
    $info = $row['info'];
    $dinfo = '';
    if ($info != '') {
      $dinfo = "<div style=\"border:1px solid #999;background-color:#eee;font-style:italic;\">{$info}</div>";
    }
    $name = $row['varname'];
    $mc_yna = $this->widget_select_yna_add("{$name}_ynaa", $value, $t);
    $tarea = $this->TEXTAREA("{$name}_comment", $value, "width:100%;height:50px;margin-top:6px;");
    $tareanc = $this->TEXTAREA("{$name}_note", $value, "width:100%;height:50px;margin-top:6px;",
                        'nc');
    $ncval = $this->general->get_arrval($value, $name . '_nc', 'F');
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
      <td style="vertical-align:top;padding: 2px 4px;width:440px;">
        <div style="display:inline-block;vertical-align:top;">
          <div style="width:395px;">
            <div style="width:100%">
              <div style="vertical-align:top;display:inline;">{$prefix}</div>
              <div style="text-decoration:underline;font-weight:bold;vertical-align:top;display:inline;">{$heading}</div>
              <div style="vertical-align:top;display:inline;">{$text}
              </div>
            </div>
          </div>
          <div style="width:395px;font-style:italic;font-weight:bold;font-size:0.8em;line-height:1.4em;margin-top:4px;">{$info}</div>
        </div>
      </td>
      <td  style="vertical-align:top;padding: 2px 4px;width:350px;">
        <div style="">{$mc_yna} </div>
        {$tarea}
        <div style="width:100%;text-align:left;margin-left:13px;">
                  <label><input type="checkbox" id="{$name}" name="{$name}_cb" value="T" {$checked} style="margin-right:8px;"
                          onclick="toggleNCBox(this);">Non-Compliant</label>
                  <input type="hidden" id="{$name}_nc" name="{$name}_nc" value="{$ncval}"/>
                </div>
        <div id="div{$name}_nc" style="{$vis}" >
        Non Compliance Notes:<br />
        {$tareanc}
        </div>
      </td>
</tr></table>
END;
    return $out;
  }

  function partial_bat_comment($row, $value, $t) {
    $name = $row['varname'];
    $tarea = $this->TEXTAREA($name, $value, "width:810px;height:50px;margin:6px 10px 4px 10px;");
    $out = <<<"END"
<div style="width:100%;">
  <div class="bat_comment">
  {$tarea}
  </div>
</div>
END;
    return $out;
  }

  function partial_ynna_ct($row, $value, $t) {
    /*
   * Yes, No and N/A count for the section
   */
    // $prefix = $row ['prefix'];
    $name = $row['varname'];
    $heading = $row['heading'];
    $v_y_ct = $this->general->get_arrval($value, "{$name}_y_ct", 0);
    $v_n_ct = $this->general->get_arrval($value, "{$name}_n_ct", 0);
    $v_na_ct = $this->general->get_arrval($value, "{$name}_na_ct", 0);
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

  function partial_ynp_ct($row, $value, $t) {
    /*
   * Yes, No and Partial count for the section
   */
    // $prefix = $row ['prefix'];
    $name = $row['varname'];
    $heading = $row['heading'];
    $v_y_ct = $this->general->get_arrval($value, "{$name}_y_ct", 0);
    $v_n_ct = $this->general->get_arrval($value, "{$name}_n_ct", 0);
    $v_p_ct = $this->general->get_arrval($value, "{$name}_p_ct", 0);
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
  /*
 * the bottom line
 */
  function calculate_page($rows, $value, $langtag)
  { // $tword) {
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
    $show_only = array(

        'main_heading',
        'main2',
        'banner_rev',
        'banner_rev_border',
        'part_head',
        'sec_head_lab',
        'tab_head3',
        'info_i',
        'img',
        'normal',
        'sec_elem_info',
        'sec_elem_info_normal',
        'sec_element_info',
        'sub_sec_info',
        'sec_total',
        'sec_head_top',
        'sec_head',
        'action_plan_heading',
        'full'
    );
    $ignore_types = array(

        'prof_info_yn_html',
        'pagebreak',
        'bat_element_info',
        'ignore'
    );
    $tlist = $this->general->getTranslatables($langtag); // $tword );
    $tout = array();
    // $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $tout[] = '<table border=0 style="width:825px;">';
    $ctr = 0;
    $slmta = false;
    foreach($rows as $row)
    {
      $ctr ++;
      $type = $row['row_type'];
      if (in_array($type, $ignore_types))
        continue;
      $arow = array();
      $arow['prefix'] = $this->general->get_lang_text($row['prefix'], $row['lpdefault'],
                                                      $row['lplang']);
      $arow['heading'] = $this->general->get_lang_text($row['heading'], $row['lhdefault'],
                                                      $row['lhlang']);
      $arow['text'] = $this->general->get_lang_text($row['text'], $row['ltdefault'],
                                                    $row['ltlang']);
      $arow['varname'] = $row['varname'];
      $arow['info'] = $this->general->get_lang_text($row['info'], $row['lidefault'],
                                                    $row['lilang']); // $row['info'];
      $arow['score'] = $row['score'];
      $arow['baseurl'] = $this->baseurl;
      $arow['element_count'] = $row['element_count'];
      $bpad = 'class="bpad"';

      if (in_array($type, $show_only))
      {
        $bpad = '';
      }
      if ($type == '')
      {
        $this->log->logit("ROW: " . print_r($row, true));
      }
      if (! $slmta && substr($row['varname'], 0, 5) == 'slmta')
      {
        $tout[] = "<div id=\"onlyslmta\">";
        $slmta = true;
      }
      $func = "partial_{$type}";
      $tout[] = "<tr ><td {$bpad}>" .
           // call_user_func("partial_{$type}", $arow, $value, $tlist) .
      $this->$func($arow, $value, $tlist) . '</td></tr>';
      if ($slmta && substr($row['varname'], 0, 5) != 'slmta')
      {
        $tout[] = "</div>";
        $slmta = false;
      }
    }
    $tout[] = '</table>';
    return $tout;
  }
}