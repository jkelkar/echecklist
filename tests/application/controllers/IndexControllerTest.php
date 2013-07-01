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

    public function testIndexActionLF()
    {
      $params = array('action' => 'index', 'controller' => 'Index', 'module' => 'default');
      $urlParams = $this->urlizeOptions($params);
      $url = $this->url($urlParams);
      // $url = '/albums/index/index';
      $this->dispatch($url);

      // assertions
      $this->assertQueryCount('a#new' , 1);
      $this->assertQueryCount('a', 1);
      $this->assertQueryContentRegex('a#new', '*Add new album*');
      $this->assertQueryContentContains('h1', 'My Albums');
      $this->assertSame('A', 'A');
      $this->assertTrue('B'> 'A');
    }
}



