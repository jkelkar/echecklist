<?php

class Checklist_Modules_Export
{

  function exportData($audit_id)
  {
    //export data for the given audit_id
    $log = new Checklist_Logger();
    $lab = new Application_Model_DbTable_Lab();
    $data = new Application_Model_DbTable_AuditData();
    $audit = new Application_Model_DbTable_Audit();
    $tmpl = new Application_Model_DbTable_Template();
    $audit_row = $audit->get($audit_id);
    $log->logit('AR: ' . print_r($audit_row, true));
    $tmpl_row = $tmpl->get($audit_row['template_id']);
    $audit_data_rows = $data->getAudit($audit_id);
    $log->logit('AuditData_ct: ' . count($audit_data_rows));
    $lab_row = $lab->getLab($audit_row['lab_id']);
    $log->logit('LABROW: ' . print_r($lab_row, true));
    $alldata = array('lab'=> $lab_row,'audit'=> $audit_row,
        'audit_data'=> $audit_data_rows);
    $serdata = serialize($alldata);
    $serdatal = strlen($serdata);
    $datarows = count($audit_data_rows);
    //$log->logit("SERDATA:\n {$serdata}");
    $log->logit("LEN: {$serdatal}");
    $fname = "{$lab_row['labnum']}_{$tmpl_row['tag']}_{$audit_row['end_date']}.edx";
    return array('data'=> $serdata,'name'=> $fname);
  }

}