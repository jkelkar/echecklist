<?php 

require_once "PHPUnit/Extensions/Database/TestCase.php";

class AlbumsTest extends PHPUnit_Extensions_Database_TestCase
{
  protected $dbconn;
  /**
   * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
   */
  public function getConnection()
  {
    $database = 'mydb';
    try {
      $conn = new PDO('mysql:host=localhost;dbname=' .  $database, 'root', '3ntr0py');
      //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
      echo 'ERROR: ' . $e->getMessage();
    }
    $this->dbconn = $conn;
    return $this->createDefaultDBConnection($conn, $database);
      //$conn; //$this->createDefaultDBConnection($pdo, $database);
  }

  /**
   * @return PHPUnit_Extensions_Database_DataSet_IDataSet
   */
  public function getDataSet()
  {
    $co = $this->getConnection();
    return $co->createDataSet(array('albums'));
    /*
      $conn= $co->getConnection();
      // echo $conn;
      $data = $conn->query("SELECT * FROM albums");
      // $stmt->execute();
      
      $outdata = array();
      foreach($data as $row) {
      print_r($row);
      $outdata[] = $row;
      }
      //return $outdata;
    */
  }

  public function testThis()
  {
    $data = $this->getDataSet();
    //print_r($data);
    //$this->assertSame('A', 'A');
    
    $conn = $this->dbconn;
    $stmt = $conn->prepare("SELECT * FROM albums");
    $stmt->execute();
    // print 'data:'; print_r($data);
    $outdata = array();
    //echo 'data: ' . $stmt->fetch();     
    $i = 1;
    while($row = $stmt->fetch()) { 
      print_r($row);
      $outdata[] = $row;                                                                               
      $this->assertSame($row['id']+0, $i);
      $i++;
    }                                                                                                 
    //return $outdata;
  }
}