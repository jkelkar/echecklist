<?php
/**
 * This implements HTML Helpers
 **/
require_once '../application/models/DbTable/Langword.php';
require_once 'modules/Checklist/fillout.php';
require_once 'modules/Checklist/logger.php';

function field_input($name, $value, $type, $length=0, $style='',
                     $label_text='', $label_style='', $label_class='', $formstyle='table',
		                 $wrapper=array('', ''))
{
	logit("FS: {$name}, {$type}, {$length}, {$style}, {$label_text}, {$label_style}, {$formstyle}, ");
	logit("WRAP: ".implode("\n", $wrapper). ' ' .count($wrapper));
	$out = '';
	if (count($wrapper) != 2) {
		$w1 = $w2 = '';
	} else {
		$w1 = $wrapper[0];
		$w2 = $wrapper[1];
	}
  switch ($formstyle) {
  case 'table':
    $lab = LABEL($name, $label_text, $label_style, $label_class);
    if ($w1 == '') {
    	$inp = INPUT($name, $value, $type, $length, $style, 'f');
    } else {
    	$inp = INPUT_AC($name, $value, $type, $length, $style, 'f');
    }
  
    $out = <<<"END"
<td class="n f right" style=width:400px;">{$lab}</td>
<td class="n f" style=width:400px;">{$w1}{$inp}{$w2}</td>
END;
  default:
  }
  return $out;
}

function field_ready($name, $control,
                     $label_text='', $label_style='', $label_class='', $formstyle='table',
		                 $wrapper=array('', '')) {
  $lab = LABEL($name, $label_text, $label_style, $label_class);
  $w1 = $w2 = '';
  $out = <<<"END"
<td class="n f right" style=width:400px;">{$lab}</td>
<td class="n f" style=width:400px;">{$w1}{$control}{$w2}</td>
END;
  return $out;
}
function createForm() {
  
}
function dumpForm($fields, $langtag, $value=array('_' => '_')) {
  /**
   * $fields is an array
   * '_fields' => ['field1', 'field2' ...]
   * 'field1' => array('name'=> name, 'type' => type, 'length' => length,
   *                   'comment'=>comment, 'label' => label)
   *
   * $value is an array that has all values indexed by fieldname
   **/
  //logit("TOP: {$fields} _fields");
  $tlist = getTranslatables($langtag);
  $_fields = get_arrval($fields, '_fields', '');
  $outlines = array();
  $outlines[] = "<form method=\"post\" action=\"\" " .
    "enctype=\"application/x-www-form-urlencoded\" " .
  	"name=\"thisform\" id=\"thisform\">";
  $outlines[] = '<table style="width:800px;">';
  $acomplete = array();
  $wrap = array('','');
  foreach($fields as $a => $b) {
  	$auto = false;
  	//logit("AB: {$a} == {$b}");
    if ($a[0] == '_') { continue;}
    if (array_key_exists('autocomplete', $b)) {
    	$acomplete[$a] = $b['autocomplete'];
    	$auto = true;
    }
    $outlines[] = '<tr>';
    $name = $a;
    $type = $b['type'];

    $l = get_arrval($b, 'length', '');
    switch ($type) {
    case 'labaffil':
      $control = widget_select_labaffil($name, array(), $tlist); 
      logit("Control: ". $control);
      $outlines[] = field_ready($a, $control,
                                $b['label'], '', 'inp', 'table', $wrap);
      break;
    case 'lablevel':
      $control = widget_select_lablevel($name, array(), $tlist); 
      logit("Control: ". $control);
      $outlines[] = field_ready($a, $control,
                                $b['label'], '', 'inp', 'table', $wrap);
      break;
    case 'text':
      break;
    case 'date':
    case 'datetime':
    case 'integer':
    case 'string':
      //logit("B: {$b}");
      //$length = ($l == '') ? '': "size=\"{$l}\"";
      //logit("V: {$value} {$a}");
      if ($auto) { // autocomplete enabled
      	$wrap[0] = "<div style=\"margin: 3px 0;width: 210px;\">";
      	$wrap[1] = "<ul id=\"{$a}_results\"></ul></div>";
      }
    case 'password':
      $outlines[] = field_input($a, $value,
                                $type, $l, '',
                                $b['label'], '', 'inp', 'table', $wrap);
      break;
    case 'submit':
      $val = get_arrval($b, 'value', '');
      $inp = INPUT($a, $val, $type, $l, '', 'f');
      $out = "<td class=\"n\"></td><td class=\"n f\">{$inp}</td>";
      $outlines[] = $out;
      break;
    default:
    }
    $outlines[] = '</tr>';
  }
  // $outlines[] = "<tr><td class=\"n\"></td><td class=\"n\">
  // <input type=\"submit\" class=\"f\" value=\"Login\" name=\"submit\" >
  // </td></tr>";
  $outlines[] = '</table>';
  $outlines[] = '</form>';
  /**
   * Add in all calls for autocomplte here
   */
  
  foreach ($acomplete as $n => $v) {
  	/*
      foreach($v as $n1 => $v1) {
      logit("1: {$n1} => {$v1}");
      }
    */
  	$url = $callback = '';
  	if (array_key_exists('url', $v)) {
  		$url = $v['url'];
  		//continue;
  	}
    if (array_key_exists('setvals', $v)) {
    	$callback = $v['setvals'];
    	//continue;
    }
  	if (!($url && $callback)) {
  		throw new Exception ('Need both url and setvals for Autocomplete');
  	}
  	$jslines = <<<"END"
<script>
  var labname = '', labid = 0;
  ecAutocomplete('{$n}', '{$url}', {$callback}, '{$callback}');
</script>
END;
  	//logit("JSL {$jslines}");
  	$outlines[] = $jslines;
  }
  //logit("OUTL: " . implode("\n", $outlines));
  return implode("\n", $outlines);
}

/**
 *
 * $(function() {
 //ecAutocomplete('{$n}', '{$url}', '{$callback}');
 *function log( message ) {
 $( "<div>" ).text( message ).prependTo( "#{$n}_results" );
 $( "#{$n}_results" ).scrollTop( 0 );
 }
 \$('#{$n}').autocomplete({
 minLength: 1,
 delay: 80,
 source: '{$url}',
 messages: {
 noResults: '',
 results: function() {}
 },
       
 select: function(event, ui) {
 $('#{$n}').autocomplete('destroy');
 //$(d_id).html('');
 // $(d_id).dialog('destroy');
 // act.su(ui.item.id);
 // Set the id and name to something
 {$callback}('test', ui.item.labname, ui.item.id);
 },
 //appendTo: "#{$n}_results",
        
 })
 .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
 return $( "<li>" )
 .append( "<a style=\"color:green;font-size:22px;\" onclick=\"\" href=\"\"> "
 + item.id + ' ' + item.labname + "</a>" )
 .appendTo( ul );
 };
 });
 *
 */