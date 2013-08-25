<?php
interface Apitools_Model_IApiSelector
{
    /*获取api的分类目录
    *	
    */
}

//根据所选的API提供商构造出api
abstract class Apitools_Model_ApiAbstract 
			   implements Apitools_Model_IApiSelector
{
    /* $src 是指提供API的公司
     * 比如sina,qq, 开心....
     * 
     * */
	protected $_dbModel = null;	//database object
	protected $_sdkClient = null;	//api object
	
 	public function getCatalogs(){
 		if ($this->_dbModel != null){
        	return $this->_dbModel->getCatalogs();
 		}
 		else{
 			throw new Exception("the object is null");
 		}
    }
    
    /*
     * 参数：clid 目录id*/
    public function getApisOfCatalog($clid) {
        if ($this->_dbModel != null){
        	return $this->_dbModel->getApisOfCatalogs($clid);
        }else{
        	throw new Exception("the object is null");
        }
    }
    
    /*
     * 获得详细的信息，这个函数对参数进行了一定的处理 */
    public function getApiDetailsInfo($apiid){
    	if ($this->_dbModel != null){
        	$apiInfo = $this->_dbModel->getApiRawInfo($apiid);
			if (!empty($apiInfo)){
				$apiInfo['method'] = $this->getHttpType($apiInfo['method']);
				$apiInfo['format'] = $this->getSupportType($apiInfo['format']);
				$apiInfo['param_names'] = explode("|", $apiInfo['param_names']);
				$apiInfo['param_select'] = explode("|", $apiInfo['param_select']);
				$apiInfo['param_desc'] = explode("|", $apiInfo['param_desc']);
				$apiInfo['param_types'] = explode("|", $apiInfo['param_types']);
			}
			return $apiInfo;
        }else{
            throw new Exception("the object is null");
        }
    }
    
    //abstract public function filterParams();
    /*
     * 初始化API列表
     * 比如选中sina,就会出现sina的目录分类,api以及对应的参数
     * */
    public function initialzieApiList()
    {
        $apilist = new stdClass();
        $apilist->catalogs = $this->getCatalogs();
        if (!empty($apilist->catalogs)){
            $apilist->apis = $this->getApisOfCatalog( $apilist->catalogs[0]['id']); //默认初始化第一个目录为选中目录
            if (!empty($apilist->apis)){
                $apilist->apiDetails = $this->getApiDetailsInfo($apilist->apis[0]['id']); //默认初始化第一个API为选中API
                }
            }
        return $apilist;
    }
    
    /*http 的请求类型*/
	const HTTP_GET = 0;
	const HTTP_POST = 1;
	const HTTP_PUT = 2;
	const HTTP_DELETE = 3;
	/*api 支持的返回格式*/
	const JSON = 0;
	const XML = 1;
	const JSON_XML = 2;
	
	private function getSupportType($t)
	{
		switch ($t) {
			case self::JSON:
			 	return array('Json');
			case self::XML:
				return array('Xml');
			case self::JSON_XML:
				return array('Json', 'Xml');
		}
	}
	
	private function getHttpType($d)
	{
		switch ($d)
		{
			case self::HTTP_GET:
				return "GET";
			case self::HTTP_POST:
				return "POST";
			case self::HTTP_PUT:
				return "PUT";
			case self::HTTP_DELETE:
				return "DELETE";
		}
	}
	
    abstract public function run($url, $parameters, $http_type, $format = 'json');
   	
}



