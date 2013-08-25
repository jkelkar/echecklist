<?php

function exportData($audit_id) {
  //export data for the given audit_id
  $lab = new Application_Model_DbTable_Lab();
  $data = new Application_Model_DbTable_AuditData();
  $audit = new Application_Model_DbTable_Audit();
  $audit_row = $audit->get($audit_id);
  logit('AR: ' . print_r($audit_row, true));
  $audit_data_rows = $data->getAudit($audit_id);
  logit('AuditData_ct: ' .  count($audit_data_rows));
  $lab_row = $lab->getLab($audit_row['lab_id']);
  logit('LABROW: ' . print_r($lab_row, true));
  $alldata = array (
      'lab' => $lab_row,
      'audit' => $audit_row,
      'audit_data' => $audit_data_rows
  );
  $serdata = serialize($alldata);
  $serdatal = strlen($serdata);
  $datarows = count($audit_data_rows);
  //logit("SERDATA:\n {$serdata}");
  logit("LEN: {$serdatal}");
  $fname = "{$lab_row['labnum']}_{$audit_row['tag']}_{$audit_row['end_date']}.edx";
  return array (
      'data' => $serdata,
      'name' => $fname
  );
}