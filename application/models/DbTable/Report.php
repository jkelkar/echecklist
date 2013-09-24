<?php

/**
 * This implements the model for Page tags
 *
 */

class Application_Model_DbTable_Report extends Checklist_Model_Base {
  protected $_name = 'report';
  protected $_primary = 'id';

  public function init(){
    parent::init();
  }

  public function getReportId($name) {
    /**
     * Get the report_id from this name
     */
    //$this->setMetadataCacheInClass(false);
    $sql = <<<"END"
select report_id
  from report
 where tabpos=0
   and position=0
   and name='{$name}'
 order by tabpos, position
END;
    $this->log->logit("SQL: {$sql}");
    $rows = $this->queryRows($sql);
    if (! $rows) {
      throw new Exception("Could not find report_id");
    }
    $row = $rows[0];
    $rid = (int) $row['report_id'];
    return $rid;
  }

  public function getReportRows($rid) {
    /**
     * Get all rows for this report
     */
    $rid = (int) $rid;
    $sql = <<<"END"
select *
  from report where report_id = {$rid}
 order by tabpos, position
END;
    $rows = $this->queryRows($sql);
    if (! $rows) {
      throw new Exception("Could not find Report Information");
    }
    return $rows;
  }

  public function runQuery($sql) {
    $rows = $this->queryRows($sql) ;
    return $rows;
  }
}

