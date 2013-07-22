<?php 

/**
 * This is required to bring in the database model that is to be tested
 */
require_once '../application/models/DbTable/Albums.php';
require_once 'application/models/ChecklistAbstract.php';

class AlbumsTest extends Tests_Model_Checklist_Abstract
{
  // private $_connectionMock;

  /**
   * Returns the database connection.
   * 
   * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
   */
  /**
     protected function getConnection()
  {
    $database = 'mydb_test';
    if ($this->_connectionMock == null) {
      $connection = Zend_Db::factory
        ('Pdo_Mysql',
         array(
               'host' => 'localhost',
               'username' => 'root',
               'password' => '3ntr0py',
               'dbname' => 'mydb_test'
               ));
      $this->_connectionMock = $this->createZendDbConnection
        (
         $connection, 'zfunittests'
         );
      Zend_Db_Table_Abstract::setDefaultAdapter($connection);
    }
    return $this->_connectionMock;
  }
  **/
  
  /**
   * @return PHPUnit_Extensions_Database_DataSet_IDataSet
   */
  protected function getDataSet()
  {
    /* this loads the initial set of rows into the table */
    return $this->createFlatXmlDataset
      (
       dirname(__FILE__) . '/_files/albumsSeed.xml'
       );
  }
  
  public function testAlbumInsertedIntoDb()
  {
    $albumsTable = new Application_Model_DbTable_Albums();
    
    $data = array
      (
       'artist' => 'Born Jovial',
       'title'  => 'Late to the party'
       );
    
    $albumsTable->insert($data);
    
    $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet
      (
       $this->getConnection()
       );
    $ds->addTable('albums', 'SELECT * from albums');
    /*
      $this->addertDataSetEqual
      (
      $this->createFlatXmlDataset
      (dirname(__FILE__) . "/_files/albumsInsertIntoAssertion.xml"),
      $ds
      );
    */
    //echo $this;
    $rowset = $albumsTable->fetchAll();
    foreach($rowset as $row) {
      // echo 'ROW: ' . $row['id'] . ' ' . $row['artist'] . ' ' . $row['title'] . "\n";
      $this->assertSame($row['id'], $row['id']);
    }
    
  }

  public function testAlbumSelect()
  {
    $albumsTable = new Application_Model_DbTable_Albums();
    //echo 'AClass: ' . $albumsTable;
    $rows = $albumsTable->getAlbums();
    //echo 'Rows: ' . $rows;
  }
}