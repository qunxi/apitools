<?php
require_once 'Api_SDK/Sina/OAuth2.php';
require_once 'Api_SDK/Utils/Factory.php';

class Apitools_Model_SinaApi extends Apitools_Model_ApiAbstract
{
	const WEB_AKEY = '3465020084';
	const WEB_SKEY = 'db13eac819f442198ebf5445c57c5a14';
	const VERSION = 2;
	
	//static $CALLBACK_URL = '';
	//private $_webcallback = '';
	
   	public function __construct(){
   		$this->_dbModel = new Apitools_Model_Db_Sina();
   		$this->_sdkClient = new Sina_OAuth2(self::WEB_AKEY, self::WEB_SKEY);
   		//var_dump($webcallback);
   		//$this->_webcallback .= $webcallback;
   		//parent::$CALLBACK_URL .= $webcallback;
   	}
   	
   	
   	public function run($url, $parameters, $method, $format = 'json')
   	{
   		$auth = Utils_Factory::getAuthSession('sina');
   		if (isset($auth->token) && $auth->token && $auth->created + $auth->token['expires_in'] > time())
   		{
   			//print_r($parameters);
   		    $parameters = $this->filterParams($parameters);

   		    $result['response'] = $this->_sdkClient->executeApi($url, $parameters, $method); 
   		    return $result;
   		}
   		else{
   		    unset($auth->token);
   		    //不建议怎么写回调地址
   		    $callback = 'http://'. $_SERVER['SERVER_NAME'] .  '/apitools/ApiTools/Api/sina';
   		    $url = $this->getAuthorizeURL($callback);   	
   		    return $url;
   		}
   		
   		
   		/*$ret = $this->_sdkClient->http_info;
   		$req_info['url'] = $this->_sdkClient->url;
   		$req_info['content_type'] = $ret['content_type'];
   		$req_info['request_header'] = $ret['request_header'];
   		$result['request'] = $req_info;*/
   		
   		//return $result;
   	}
   	
    public function filterParams($parameters)
   	{
   	    $params = array();
   	    $auth= Utils_Factory::getAuthSession('sina');
   	    foreach ($parameters as $name => $value) {
   	        if ($name != 'source' && 
   	            $name != 'provider_name' && 
   	            $name != 'api'&& $name != 'method') { //过滤没必要的字段
   	            if ($name == 'access_token'){
   	                $params[$name] = $auth->token['access_token'];
   	            }
   	            else{
   	                if ($parameters[$name]){
   	                    $params[$name] = $parameters[$name];
   	                }
   	            }
   	        }
   	    }//获得参数
   	   
   	    return $params;
   	}
   	
   	public function setAccessToken($auth_code, $callback)
   	{
   	    $auth_sina= Utils_Factory::getAuthSession('sina');
   	    if (!isset($auth_sina->token))
   	    {
	   		$keys = array( 'code' => $auth_code,
	   	        		   'redirect_uri' => $callback );
	   		try {
	   			$token = $this->_sdkClient->getAccessToken('code', $keys);
	   			if ($token != null && isset($token['access_token'])){
	   				$auth_sina->token = $token;
	   				$auth_sina->created = time();
	   			}
	   		}
	   		catch (Exception $e) {
	   	       print_r('bad luck');
	   		}
   	    }
   	}
   	
   	public function getAuthorizeURL($callback)
   	{
   		return $this->_sdkClient->getAuthorizeURL($callback);
   	}
   	

}
