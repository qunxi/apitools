<?php
require_once 'Api_SDK/OAuth2.php';

class JiePang_OAuth2 extends OAuth2{
	
    protected $_authorizeUrl = 'https://jiepang.com/oauth/authorize';
    protected $_accessTokenUrl = 'https://jiepang.com/oauth/token';
	
	protected $_appid;
	protected $_appsecret;
	protected $_host = 'http://api.jiepang.com/v1/';
	
    public function __construct($appid, $appsecret){
        parent::__construct();
        $this->_appid = $appid;
        $this->_appsecret = $appsecret;
    }
    
    public function getAuthorizeUrl($callback_url, $response_type = 'code', 
                                    $state = NULL, $display = NULL)
    {
        $params = array();
        $params['client_id'] = $this->_appid;
        $params['redirect_uri'] = $callback_url;
        $params['response_type'] = $response_type;
        $params['state'] = $state;  

        return $this->_authorizeUrl . "?" . http_build_query($params);
    }
    
    public function getAccessToken($type= 'code', $keys){
    	$params = array();
    	$params['client_id'] = $this->_appid;
        $params['client_secret'] = $this->_appsecret;
        
        if ($type === 'code'){
        	
        	$params['grant_type'] = 'authorization_code';
            $params['code'] = $keys['code'];
            $params['redirect_uri'] = $keys['redirect_uri'];
            $params['client_id'] = $this->_appid;
            $params['client_secret'] = $this->_appsecret;
        }
        else {
            throw new OAuthException("wrong auth type" . $token['error']
                . $token['error_description'] );
        }
        
        /*$file1 = fopen("C:\\1.txt", 'w');
	   	fwrite($file1, $this->_accessTokenUrl);
	   	fclose($file1);*/
        $response = $this->oAuthRequest($this->_accessTokenUrl, 'POST', $params);
        
        
        //fwrite($file1, $response);
	   
        
	   	$token = json_decode($response, true);
        
        if ( isset($token['error']) ) {
            throw new OAuthException("get access token failed." . $token['error'] 
                . $token['error_description']);
        } 
        
        return $token;
    }
    
    protected function oAuthRequest($url, $method, $parameters, $format = 'json', $multi = FALSE) 
    {
        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
            $url = "{$this->_host}{$url}.{$format}";
            var_dump($url);
        }
        $headers = array();
        if (isset(Utils_Factory::getAuthSession('jiepang')->token['access_token']) 
            && Utils_Factory::getAuthSession('jiepang')->token['access_token'] != NULL)
        {
            $headers[] = "Authorization: OAuth2 " . Utils_Factory::getAuthSession('sina')->token['access_token'];
        }
        $response = $this->_baseHttp->http($url, $method, $parameters, $multi, $headers);
        
        return $response;
    }
    
    public function executeApi($url, $params, $method, $format = 'json', $multi = FALSE )
    {
        //var_dump($this->_host. $url);
        //var_dump($params);
    	$response = $this->oAuthRequest($this->_host . $url, $method, $params, $format, $multi);
        
    	if ($format == 'json'){
             return json_decode($response);
        }
        return $response;
    }
}