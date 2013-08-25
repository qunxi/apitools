<?php
require_once 'Api_SDK/OAuth2.php';

class Sina_OAuth2 extends OAuth2{

	//protected $_requestTokenUrl; //获取auth token 的网址
    protected $_authorizeUrl = 'https://api.weibo.com/oauth2/authorize';
    protected $_accessTokenUrl = 'https://api.weibo.com/oauth2/access_token';

    protected $_appid;
    protected $_appsecret;
    protected $_host = 'https://api.weibo.com/2/';
    
	public function __construct($appid, $appsecret, $callback = NULL){
		parent::__construct();
		$this->_appid = $appid;
		$this->_appsecret = $appsecret;
		//$this->_callback = $callback;
	}
	
	public function getAuthorizeUrl($callback_url, $response_type = 'code', $state = NULL, $display= NULL){
		$params = array();
		$params['client_id'] = $this->_appid;
		$params['redirect_uri'] = $callback_url;
		$params['response_type'] = $response_type;
		$params['state'] = $state;
		$params['display'] = $display;
		return $this->_authorizeUrl . "?" . http_build_query($params);
	}
	
	function getAccessToken( $type = 'code', $keys ) {
		$params = array();
		$params['client_id'] = $this->_appid;
		$params['client_secret'] = $this->_appsecret;
		if ( $type === 'token' ) {
			$params['grant_type'] = 'refresh_token';
			$params['refresh_token'] = $keys['refresh_token'];
		} elseif ( $type === 'code' ) {
			$params['grant_type'] = 'authorization_code';
			$params['code'] = $keys['code'];
			$params['redirect_uri'] = $keys['redirect_uri'];
		} elseif ( $type === 'password' ) {
			$params['grant_type'] = 'password';
			$params['username'] = $keys['username'];
			$params['password'] = $keys['password'];
		} else {
			throw new OAuthException("wrong auth type");
		}
        
		$response = $this->oAuthRequest($this->_accessTokenUrl, 'POST', $params);
		$token = json_decode($response, true);
		if ( is_array($token) && !isset($token['error']) ) {
			
		} else {
			throw new OAuthException("get access token failed." . $token['error']);
		}
		return $token;
	}
	
	protected function oAuthRequest($url, $method, $parameters, $format = 'json', $multi = FALSE) {

		if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
			$url = "{$this->_host}{$url}.{$format}";
		}
		
		$headers = array();
		if (isset(Utils_Factory::getAuthSession('sina')->token['access_token']) 
		    && Utils_Factory::getAuthSession('sina')->token['access_token'] != NULL)
		{
        	$headers[] = "Authorization: OAuth2 " . Utils_Factory::getAuthSession('sina')->token['access_token'];
        }
		$response = $this->_baseHttp->http($url, $method, $parameters, $multi, $headers);
		
		return $response;
	}
	
    public function executeApi($url, $params, $method, $format = 'json', $multi = FALSE ){
    	$response = $this->oAuthRequest($this->_host . $url . '.' . $format, $method, $params, $format, $multi);
        
    	if ($format == 'json'){
             return json_decode($response);
        }
        return $response;
    }
}