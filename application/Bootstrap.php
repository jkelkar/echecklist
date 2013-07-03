<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
  /**
   * echo $config->db->adapter . " xx\n";
   $db = Zend_Db::factory($config->db->adapter, $config->db->config->asArray());
   Zend_Db_Table::setDefaultAdapter($db);
   Zend::register('db', $db);
  */
  /**
   * Again we register the databsae with the registry and with Zend_Db_Table. As our models
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
  

}
