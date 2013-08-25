<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initDb()
    {
        $config = $this->getOptions();
        $dbAdapter = Zend_Db::factory($config['db']['adapter'], $config['db']['params']);
      // var_dump($config['db']['params']);
      //$dbAdapter = Zend_Db::factory("pdo_sqlite", array("dbname"=> APPLICATION_PATH . '/../data/apitools.sqlite')); //sqlite
        Zend_Db_Table::setDefaultAdapter($dbAdapter);
        Zend_Registry::set('dbAdapter', $dbAdapter);
    }
}

