<?php 

/**
 * This is required to bring in the database model that is to be tested
 */
require_once '../application/models/DbTable/Lab.php';
require_once 'application/models/ChecklistAbstract.php';

class LabTest extends Tests_Model_Checklist_Abstract
{
  /**
   * @return PHPUnit_Extensions_Database_DataSet_IDataSet
   */
  protected function getDataSet()
  {
    /* this loads the initial set of rows into the table */
    return $this->createFlatXmlDataset
      (
       dirname(__FILE__) . '/_files/labSeed.xml'
       );
  }
  
  public function testLabInsertedIntoDb()
  {
    $labTable = new Application_Model_DbTable_Lab();
    
    $data = array
      (
       'labname' => "Noch Ein Lab, Inc",
       'labnum' => "lab-010",
       'description' => "Come to us when you need quick results.",
       'street' => "126 Link Road ",
       'street2' => "Arrangements",
       'city' => "Lagos",
       'state' => "",
       'country' => "Nigeria",
       'county_code' => "GH",
       'postcode' => "",
       'level' => "N",
       'affiliation' => "O"
       );
    
    $newid = $labTable->newLab($data);
    echo "New Id: {$newid}\n";
    // insert($data);
    
    $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet
      (
       $this->getConnection()
       );
    $ds->addTable('lab', 'SELECT * from lab');
    
    $this->assertDataSetsEqual
      (
       $this->createFlatXmlDataset
       (dirname(__FILE__) . "/_files/labInserted.xml"),
       $ds
       );
    
    //echo $this;
    $rowset = $labTable->fetchAll();
    foreach($rowset as $row) {
      // echo 'ROW: ' . $row['id'] . ' ' . $row['artist'] . ' ' . $row['title'] . "\n";
      $this->assertSame($row['id'], $row['id']);
    }
    
  }

  public function testLabSelect()
  {
    $albumsTable = new Application_Model_DbTable_Lab();
    //echo 'AClass: ' . $albumsTable;
    $rows = $albumsTable->getLabs();
    foreach($rows as $row) {
      //echo "Lab: name: {$row['labname']}\n";
    }
    //echo 'Rows: ' . $rows;
  }
}