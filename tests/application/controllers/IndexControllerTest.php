<?php

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testIndexAction()
    {
        $params = array('action' => 'index', 'controller' => 'Index', 'module' => 'default');
        $urlParams = $this->urlizeOptions($params);
        $url = $this->url($urlParams);
        $this->dispatch($url);
        
        // assertions
        $this->assertModule($urlParams['module']);
        $this->assertController($urlParams['controller']);
        $this->assertAction($urlParams['action']);
        //$this->assertQueryContentContains("div#welcome h3", "This is your project's main page");
    }

    public function getConstr($path)
    {
      /**
       * This is a way to figure out what value the constrains is being
       * compared against
       */
      $constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
      echo 'CONSTRAINT: ' ;
      print_r($constraint);
      $content    = $this->response->outputBody();
      echo 'content' . "\n";
      print_r($content);
      //echo $constraint->evaluate($content, __FUNCTION__, $count) .'c';
      //if (!$constraint->evaluate($content, __FUNCTION__, $count)) {
      //  $constraint->fail($path, $message);
      //
    }

    public function testIndexActionLF()
    {
      $params = array('action' => 'index', 'controller' => 'Index', 'module' => 'default');
      $urlParams = $this->urlizeOptions($params);
      $url = $this->url($urlParams);
      // $url = '/albums/index/index';
      $this->dispatch($url);

      // assertions
      $this->assertQueryCount('a#new' , 1);
      require_once 'Zend/Test/PHPUnit/Constraint/DomQuery.php';
      /**
       * This is a way to figure out what value the constrains is being
       * compared against
       *
       * $this->getConstr('a');
       */
      //$this->getConstr('a#new');

      $this->assertQueryCount('a', 7);
      $this->assertQueryContentRegex('a#new', '*Add new album*');
      $this->assertQueryContentContains('h1', 'My Albums');
      $this->assertSame('A', 'A');
      $this->assertTrue('B'> 'A');
    }
}



