<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
  /**
   * Again we register the databsae with the registry and with Zend_Db_Table. 
   * As our models
   * are derived from Zend_Db_table the registery will rarely be used.
   */
  
  protected function _initView()
  {
    // Initialize view
    $view = new Zend_View();
    $view->doctype('HTML5'); /*'XHTML1_STRICT'); */
    $view->headTitle('My Albums');
    $view->env = APPLICATION_ENV;
    
    // Add it to the ViewRenderer
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper
      (
       'ViewRenderer'
       );
    $viewRenderer->setView($view);
    
    // Return it, so that it can be stored by the bootstrap
    return $view;
  }
  
  public function _initConditions()  {
    $config = $this->getOptions();
    
    if (isset($config['resources']))
      {
        
        $registry = Zend_Registry::getInstance();
        
        $registry->db = $config['resources']['db'];
        
      }
  }

  /*protected function _initPlaceholders()  {
    $baseurl = Zend_Controller_Front::getInstance ()->getBaseUrl ();
    $this->bootstrap('View');
    $view = $this->getResource('View');
    //$view->doctype('XHTML1_STRICT');
    
    // Set the initial title and separator:
    $view->headTitle('eChecklist')
      ->setSeparator(' :: ');
    
    // Set the initial stylesheet:
    $csslist = array('/css/styles.css', '/css/dtree.css');
    foreach($csslist as $f) {
      $view->headLink()->appendStylesheet("{$baseurl}{$f}");
    }
    $jslist = array('/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js', '/js/helpers.js');
    foreach($jslist as $f) {
      $view->headScript()->appendFile("{$baseurl}{$f}");
    }
    }*/
  /**
   //$config = new Zend_Config_Ini('/var/www/zftest/application/configs/application.ini',
   //				APPLICATION_ENV);
   
   // Instantiate the database 
   / *$db = Zend_Db::factory('PDO_MYSQL', array(
   'host': $config->db->params->host,
   'username': $config->db->params->username,
   'password': $config->db->params->password,
   'dbname': $config->db->params->dbname
   ));
  */
}
