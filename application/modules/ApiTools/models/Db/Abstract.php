<?php
abstract class Apitools_Model_Db_Abstract
{
	const SINA = 1;
	const QQ = 2;
	const RENREN = 3;
	const KAIXIN = 4;
	const JIEPANG = 5;
	
	protected $_type = null;
	protected $_apiEntity = null;
	protected $_catalogEntity = null;

	public function getCatalogs()
	{
		return $this->_catalogEntity->getCatalogsByProvider($this->_type);
	}
	
	public function getApisOfCatalogs($catalog_id)
	{
		return $this->_apiEntity->getApiSummaryByCatalog($catalog_id);
	}
	
	//获得数据库中未处理的api信息，比如参数之类
	public function getApiRawInfo($api_id)
	{
		return $this->_apiEntity->getApiDetailsByApi($api_id);
	}

	public function isExistCatalog($name){
		return $this->_catalogEntity->isExistCatalog($name, $this->_type);
	}
	
	public function isExistApi($catalog_id, $name){
		return $this->_apiEntity->isExistApi($catalog_id, $name);
	}
	
	public function addApi2Db($apiInfo){
	    $this->_apiEntity->setProperties($apiInfo);
	   return $this->_apiEntity->addApi();
	}
	
	public function addCatalog2Db($catalogInfo){
		 $catalogInfo['provider_id'] = $this->_type;
		 var_dump($catalogInfo);
		 $this->_catalogEntity->setProperties($catalogInfo);
	     return $this->_catalogEntity->addCatalog();
	}

}