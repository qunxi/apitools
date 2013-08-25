<?php
abstract class Apitools_Model_CrawlAbstract
{
    protected $_dbModel = null;
    protected $_indexFile = null;
    
    abstract protected function getApiDetailsInfo($apiDescAddr, $apiName);
        
    
    public function addCrawlData2Db($catalog_name, $api_name, $webAddr)
    {
       
        $catalogId = $this->_dbModel->isExistCatalog($catalog_name);
        // var_dump($catalogId);
        if (is_bool($catalogId) && !$catalogId)
        {
            $catalogData = array('name' => $catalog_name);
            $catalogId = $this->_dbModel->addCatalog2Db($catalogData);
        }
            if ($catalogId && 
                !$this->_dbModel->isExistApi($catalogId, $api_name))
            {
                $result = $this->getApiDetailsInfo($webAddr, $api_name);
                //var_dump($result);
                $apiInfo = array('name' => $result->name,
                                 'method' => $result->method,
                                 'is_auth' => 1,
                                 'is_login' => 1,
                                 'auth_type' => 0, 
                                 'param_names' => join($result->paramNames, '|'),
                                 'param_types' => join($result->paramTypes, '|'),
                                 'param_select' => join($result->paramNeeds, '|'),
                                 'param_desc' => join($result->paramDesc, '|'),
                                 'format' => $result->format,
                                 'api_desc' =>$result->desc,
                                 'catalog_id' => $catalogId
                                 );
                 //print_r($apiInfo['param_desc']);
                //var_dump($apiInfo);
                $this->_dbModel->addApi2Db($apiInfo);              
                return true;
            }
            return false;

    }

}