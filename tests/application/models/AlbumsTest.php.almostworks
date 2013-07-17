class AlbumsTest extends Zend_Test_PHPUnit_DatanbaseTestCase
{

  private $_connectionMock;
  
  /**
   * Returns the test database connection.
   *
   * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
   */
  
  protected function getConnection()
  {
    if ($this->_connectionMock == null) {
      $connection = Zend_Db::factory
	('Pdo_Mysql', 
	 array(
	       'host' => 'localhost',
	       'username' => 'root',
	       'passwprd' => '3ntr0py',
	       'dbname' => 'mydb_test'
	       ));
      $this->_connectionMock = $this->createZendDbConnection
	(
	 $connection, 'zfunittests'
	 );
      Zend_Db_TableAbstract::setDeafultAdapter($connection);
    }
    return $this->_connectionMock;
  }

  /**
   * @return PHPUnit_Extensions_Database_DataSet_IDataSet
   */
  protected function getDataSet()
  {
    return $this->createFlatXmlDataSet
      (
       dirname(__FILE__) . '/_files/albumsSeed.xml'
       );
  }

  // start the tests here
  public function testAlbumsContent()
  {
    $albumsTable = new Albums();
    $rowset = $ablumsTable->fetchAll();
    //echo 'Data: ' . $rowset . '\n';
  }

}