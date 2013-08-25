<?php
require_once 'Api_SDK/QQ/OAuth1a.php';
//require_once 'Api_SDK/QQ/OpenApiV3.php';
class Apitools_Model_QQApi extends Apitools_Model_ApiAbstract
{
	const APP_ID = '';
	const APP_KEY = '801107450';
	const APP_SECRET = '62e1b97304b9eb5179ce684e71bc6ecf';
	const VERSION = 3;
		
	public function __construct(){
   		$this->_dbModel = new Apitools_Model_Db_QQ();
   		$this->_sdkClient = new QQ_OAuth1a( self::APP_ID, self::APP_KEY, self::APP_SECRET);
   	}
   	
   	public function run($url, $parameters, $method, $format = 'json')
   	{   
   		$auth = Utils_Factory::getAuthSession('qq');
   		
   		if (isset($auth->token['auth_token']) && $auth->token['auth_token']){
   		    $parameters = $this->filterParams($parameters);
   		    $result['response'] = $this->_sdkClient->executeApi( $url, $parameters, $method);
   		    
   		    return $result;
   		}
   		else{
   			
   		   //不建议怎么写回调地址
            $callback = 'http://'. $_SERVER['SERVER_NAME'] .  '/apitools/ApiTools/Api/qq';
            $url = $this->_sdkClient->getAuthorizeUrl($callback); 
                 
            return $url;
   		}
   	   
   	}
   	
   	public function filterParams($parameters)
   	{
   	    $params = array();
   	    $auth= Utils_Factory::getAuthSession('qq');
   	    $parameters['oauth_token'] = $auth->token['auth_token'];
   	    foreach ($parameters as $name => $value) {
   	        if ($name != 'provider_name' && $name != 'api' && $name != 'method') { //过滤没必要的字段
                if ($name == 'format'){
   	                $params[$name] = 'json';
   	            }
   	            else{
   	                if ($parameters[$name]!== null){
   	                    $params[$name] = $parameters[$name];
   	                }
   	            }
   	        }
   	    }//获得参数
   	    return $params;
   	}
   	
    public function setAccessToken($tokens)
    {
        $this->_sdkClient->getAccessToken($tokens);
    }
   	
   	public function test()
   	{
   	    return $this->_sdkClient->getAuthorizeUrl('http://127.0.0.1');    
   	}
}