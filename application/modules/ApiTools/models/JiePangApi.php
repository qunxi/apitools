<?php
require_once 'Api_SDK/JiePang/OAuth2.php';
class Apitools_Model_JiePangApi extends Apitools_Model_ApiAbstract
{
	const APP_KEY = '100417';
	const APP_SKEY = '0db862aadb72c7c0656e19ac8e1b7ebb';
	
    public function __construct(){
        $this->_dbModel = new Apitools_Model_Db_JiePang();
        $this->_sdkClient = new JiePang_OAuth2(self::APP_KEY, self::APP_SKEY);
    }
    
    public function run($url, $parameters, $method, $format = 'json')
    {
        $auth = Utils_Factory::getAuthSession('jiepang');
        //var_dump($auth);
         //unset($auth->token);
        if (isset($auth->token) && $auth->token /*&& $auth->created + $auth->token['expires_in'] > time()*/)
        {
            //var_dump($auth->token['access_token']);
            //$parameters = $this->filterParams($parameters);
            $parameters['access_token'] = $auth->token['access_token'];
            $result['response'] = $this->_sdkClient->executeApi($url, $parameters, $method); 
            return $result;
        }
        else{
            unset($auth->token);
   		    //不建议怎么写回调地址
   		    $callback = 'http://'. $_SERVER['SERVER_NAME'] .  '/apitools/ApiTools/Api/jiepang';
   		    $url = $this->getAuthorizeURL($callback);   	
   		    return $url;
        }
    }
    
    public function setAccessToken($auth_code, $callback)
    {
        $auth= Utils_Factory::getAuthSession('jiepang');
   	    if (!isset($auth->token))
   	    {
	   		$keys = array( 'code' => $auth_code,
	   	        		   'redirect_uri' => $callback );
	   		try {
	   			$token = $this->_sdkClient->getAccessToken('code', $keys);
	   		
	   			if ($token != null && isset($token['access_token'])){
	   				$auth->token = $token;
	   				$auth->created = time();
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