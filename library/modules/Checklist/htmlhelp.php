<?php
/**
 * This implements HTML Helpers
 **/
require_once 'modules/Checklist/fillout.php';
require_once 'modules/Checklist/logger.php';

function field_input($name, $value, $type, $length=0, $style='', 
                     $label_text='', $label_style = '', $formstyle='table')
{
  switch ($formstyle) {
  case 'table':
    $lab = LABEL($name, $label_text, $label_style);
    $inp = INPUT($name, $value, $type, $length, $style, 'f');
    $out = <<<"END"
  <td class="n f" >{$lab}</td>
  <td class="n f" >{$inp}</td>
END;
  default:
  }
  return $out;
}

function createForm() {
  
}
function dumpForm($fields, $value=array('_' => '_')) {
  /**
   * $fields is an array 
   * '_fields' => ['field1', 'field2' ...]
   * 'field1' => array('name'=> name, 'type' => type, 'length' => length,
   *                   'comment'=>comment, 'label' => label)
   *
   * $value is an array that has all values indexed by fieldname
   **/
  logit("TOP: {$fields} _fields");
  $_fields = get_arrval($fields, '_fields', '');
  $outlines = array();
  $outlines[] = "<form method=\"post\" action=\"\" enctype=\"application/x-www-form-urlencoded\" name=\"thisform\" id=\"thisform\">";
  $outlines[] = '<table>';
  foreach($fields as $a => $b) {
    logit("AB: {$a} == {$b}");
    if ($a[0] == '_') { continue;}
    $outlines[] = '<tr>';
    $type = $b['type'];
    $l = get_arrval($b, 'length', '');
    switch ($type) {
    case 'text':
      break;
    case 'date':
    case 'datetime':
    case 'integer':
    case 'string':
    case 'password':
      logit("B: {$b}");
      //$length = ($l == '') ? '': "size=\"{$l}\"";
      //logit("V: {$value} {$a}");
      $outlines[] = field_input($a, $value,
                                $type, $l, '',
                                $b['label'], '');
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
  //$outlines[] = "<tr><td class=\"n\"></td><td class=\"n\"><input type=\"submit\" class=\"f\" value=\"Login\" name=\"submit\" > </td></tr>";
  $outlines[] = '</table>';
  $outlines[] = '</form>';
  return implode("\n", $outlines);
}