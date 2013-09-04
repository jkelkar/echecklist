<?php

// this contains commonly used functions


/**
 * returns a value if a key exists in the dictionary else
   * returns the $default value <sed in
 */
function get_arrval($arr, $k, $default) {
  $show = 0;
  if ($show > 0) {
    // Uncomment the large block to see the call stack
    logit("GA: " . gettype($arr) . " ->{$k}");
    if (gettype($k) == 'array') {
      logit('ARR: ' . print_r($k, true));
    }

    $callers = debug_backtrace();
    logit("TRACE: {$callers[1]['function']}");
    $trace = debug_backtrace();
    $caller = array_shift($trace);

    echo "Called by {$caller['function']}";
    $caller = array_shift($trace);

    echo "Called by {$caller['function']}";
  }
  /*
   //if (isset($caller['class']))
    //echo " in {$caller['class']}";
  //logit('KEY?: '. key_exists ( $k, $arr ) ? $arr[$k] : $default) . '<br />';
  */
  return key_exists($k, $arr) ? $arr[$k] : $default;
}

function TR($strx, $class = '') {
  return "<tr class=\"{$class}\" >" . implode("\n", $strx) . "</tr>";
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

function get_common_words_translated($value, $words) {
  $trans_list = array();
  foreach($words as $word) {
    $trans_list[$word] = get_arrval($value, $word, $word);
  }
  return $trans_list;
}

function getYNPA($data) {
  // resolve the X mark for the option
  $sp = '&nbsp;';
  $out = array(
      'Y'=> ($data == 'YES') ? 'X' : $sp,
      'YC'=> ($data == 'YES') ? 'green' : '',
      'N'=> ($data == 'NO') ? 'X' : $sp,
      'NC'=> ($data == 'NO') ? 'red' : $sp,
      'P'=> ($data == 'PARTIAL') ? 'X' : $sp,
      'PC'=> ($data == 'PARTIAL') ? 'yellow' : $sp,
      'NA'=> ($data == 'N/A') ? 'X' : $sp,
      'NAC'=> ($data == 'N/A') ? 'yellow' : $sp
  );
  return $out;
}

function fixText($data) {
  return str_replace("\n", '<br />', $data);
}
/**
 * Data declerations
 */
function getYN($t) {
  return array( // "{$t['Select']} ..." => '-',
      "{$t['Yes']}"=> 'YES',
      "{$t['No']}"=> 'NO'
  );
}

function getYNP($t) {
  return array( // "{$t['Select']} ..." => '-',
      "{$t['Yes']}"=> 'YES',
      "{$t['Partial']}"=> 'PARTIAL',
      "{$t['No']}"=> 'NO'
  );
}

function getYNI($t) {
  return array( // "{$t['Select']} ..." => '-',
      "{$t['Yes']}"=> 'YES',
      "{$t['No']}"=> 'NO',
      "{$t['Insufficient Data']}"=> 'I'
  );
}

function getUserTypes($t) {
  return array(
      "{$t['Select']} ..."=> '-',
      "{$t['Admin']}"=> 'ADMIN',
      "{$t['User']}"=> 'USER',
      "{$t['Analyst']}"=> 'ANALYST',
      "{$t['Approver']}"=> 'APPROVER'
  );
}

function getPW($t) {
  return array( // "{$t['Select']} ..." => '-',
      "{$t['Personal']}"=> 'P',
      "{$t['Work']}"=> 'W'
  );
}

function getWP($t) {
  return array( // "{$t['Select']} ..." => '-',
      "{$t['Personal']}"=> 'PERSONAL',
      "{$t['Work']}"=> 'WORK'
  );
}

function getYNA($t) {
  return array( // "{$t['Select']} ..." => '-',
      "{$t['Yes']}"=> 'YES',
      "{$t['No']}"=> 'NO',
      "{$t['N/A']}"=> 'N/A'
  );
}

function getStars($t) {
  return array(
      "{$t['Select']}"=> '-',
      "{$t['Not Audited']}"=> 'N',
      "0 {$t['Stars']}"=> '0',
      "1 {$t['Star']}"=> '1',
      "2 {$t['Stars']}"=> '2',
      "3 {$t['Stars']}"=> '3',
      "4 {$t['Stars']}"=> '4',
      "5 {$t['Stars']}"=> '5'
  );
}

function getLevels($t) {
  return array(
      "{$t['Select']} ..."=> '-',
      "{$t['National']}"=> 'NATIONAL',
      "{$t['Reference']}"=> 'REFERENCE',
      "{$t['Regional/Provincial']}"=> 'REGIONAL',
      "{$t['District']}"=> 'DISTRICT',
      "{$t['Zonal']}"=> 'ZONAL',
      "{$t['Field']}"=> 'FIELD'
  );
}

function getAffiliations($t) {
  return array(
      "{$t['Select']} ..."=> '-',
      "{$t['Public']}"=> 'PUBLIC',
      "{$t['Hospital']}"=> 'HOSPITAL',
      "{$t['Private']}"=> 'PRIVATE',
      "{$t['Research']}"=> 'RESEARCH',
      "{$t['Non-hospital outpatient clinic']}"=> 'NONHOSPITAL',
      "{$t['Other - please specify']}"=> 'OTHER'
  );
}

function getSLMTATypes($t) {
  return array(
      "{$t['Select']} ..."=> '-',
      "{$t['Baseline Audit']}"=> 'BASE',
      "{$t['Midterm Audit']}"=> 'MIDTERM',
      "{$t['Exit Audit']}"=> 'EXIT',
      "{$t['Surveillance Audit']}"=> 'SERV',
      "{$t['Other']}"=> 'OTHER'
  );
}

function getSLMTAType($t) {
  return array(
      "{$t['Select']} ..."=> '-',
      "{$t['SLMTA']}"=> 'YES',
      "{$t['Non SLMTA']}"=> 'NO',
      "{$t['Both']}"=> 'ANY'
  );
}

function getReportTypes($t) {
  return array(
      "{$t['Select']} ..."=> '-',
      "{$t['Export Audits To Excel']}" => 'audits2excel',
      "{$t['Compare scores in a Spider Chart']}" => 'spiderchart',
      "{$t['Campare scores in a Bar Chart']}" => 'barchart',
      "{$t['Show Incomplete Audit']}" => 'incompletechart'
  );
}

function getAuditTypes($t) {
  return array(
      "{$t['Select']} ..."=> '-',
      "{$t['BioSafety']}" => 'BAT',
      "{$t['SLIPTA']}" => 'SLIPTA',
      "{$t['TB']}" => 'TB',
  );
}

//* reverses keys and values */
function rev($a, $t) {
  $arr = call_user_func($a, $t);
  $revarr = array();
  foreach($arr as $a => $b) {
    $revarr[$b] = $a;
  }
  return $revarr;
}

/*
 * the bottom line
*/
function get_lang_text($base, $default, $sp_lang) {
  /**
   * $base contains the original text
   * $default contain default text from lang
   * $sp_lang contains language specific text - but is not always available
   */
  //logit ( "{$base} -- {$default} -- {$sp_lang}" );
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
function getTranslatables(/*$tword,*/ $langtag) {
  $lang_word = new Application_Model_DbTable_Langword();
  $tword = $lang_word->getWords($langtag);
  $words = array(
      'Yes',
      'No',
      'N/A',
      'Partial',
      'Select',
      'Admin',
      'User',
      'Analyst',
      'Approver',
      'Insufficient Data',
      'Personal',
      'Work',
      'Insufficient data',
      'Not Audited',
      'Star',
      'Stars',
      'National',
      'Reference',
      'Regional/Provincial',
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
      'Non SLMTA Audit',
      'SLMTA',
      'Non SLMTA',
      'Both',
      'Baseline Audit',
      'Midterm Audit',
      'Exit Audit',
      'Surveillance Audit',
      'Other',
      'Export Audits To Excel',
      'Compare scores in a Spider Chart',
      'Campare scores in a Bar Chart',
      'BioSafety',
      'SLIPTA',
      'TB',
      'Show Incomplete Audit'
  );
  $tlist = get_common_words_translated($tword, $words);
  //logit('TLIST: '. print_r($tlist, true));
  //logit('WORDS: '. print_r($words, true));
  return $tlist;
}