<?php 

/**
 * This is required to bring in the database model that is to be tested
 */
require_once '../application/models/DbTable/User.php';
require_once 'application/models/ChecklistAbstract.php';

class UserTest extends Tests_Model_Checklist_Abstract
{
  
  /**
   * @return PHPUnit_Extensions_Database_DataSet_IDataSet
   */
  protected function getDataSet()
  {
    /* this loads the initial set of rows into the table */
    return $this->createFlatXmlDataset
      (
       dirname(__FILE__) . '/_files/userSeed.xml'
       );
  }
  
  public function testUserInsertedIntoDb()
  {
    $userTable = new Application_Model_DbTable_User();
    
    $data = array
      (
       'username' => "watcher",
       'name' => "Lookie Loo",
       'user_type' => "USER", 
       'password' => "watcher", 
        'languages' => "EN"
       );
    
    $newid = $userTable->insert($data);
    echo "User NewId: {$newid}\n";
    $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet
      (
       $this->getConnection()
       );
    $ds->addTable('user', 'SELECT * from user');
    
    $this->assertDataSetsEqual
      (
       $this->createFlatXmlDataset
       (dirname(__FILE__) . "/_files/userInserted.xml"),
       $ds
       );
    
    //echo $this;
    $rowset = $userTable->fetchAll();
    foreach($rowset as $row) {
      $this->assertSame($row['id'], $row['id']);
    }
    
  }

  public function testUserSelect()
  {
    $userTable = new Application_Model_DbTable_User();
    //echo 'AClass: ' . $albumsTable;
    $rows = $userTable->getUsers();
    foreach($rows as $row) {
      // echo "User: name: {$row['name']}\n";
    }
  }
}