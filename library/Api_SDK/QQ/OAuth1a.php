<?php
require_once 'Api_SDK/OAuth1a.php';

class QQ_OAuth1a extends OAuth1a
{   
    protected $_appid;
    protected $_appkey;
    protected $_appsecret;
    
    protected $_requestTokenUrl = 'https://open.t.qq.com/cgi-bin/request_token';
    protected $_authorizeUrl = 'https://open.t.qq.com/cgi-bin/authorize'; 
    protected $_accessTokenUrl = 'https://open.t.qq.com/cgi-bin/access_token';
   
    private $_host = 'http://open.t.qq.com/api/'; //api的base path
    
    public function __construct($appid, $appkey, $appSecret){
        parent::__construct();
        $this->_appid = $appid;
        $this->_appkey = $appkey;
        $this->_appsecret = $appSecret;
    }
    
    public function getAuthorizeUrl($callback_url){
        $tokens = $this->getRequestToken($callback_url);
        if ($tokens && isset($tokens['oauth_token'])){
            return $this->_authorizeUrl . '?oauth_token=' . $tokens['oauth_token']; 
        }
        return NULL;
    }
    
    public function getAccessToken($params){
    	$qqAuth = Utils_Factory::getAuthSession('qq');
      	
        $response = $this->oAuthRequest('GET', $this->_accessTokenUrl, 
                                        array('oauth_token' => $params['oauth_token'],
                                  			  'oauth_verifier' => $params['oauth_verifier']));
         parse_str($response, $tokens);

    	 if (isset($tokens['oauth_token']) && $tokens['oauth_token'] != NULL
    	 	 && isset($tokens['oauth_token_secret']) && $tokens['oauth_token_secret'] != NULL)
    	 {
    	 	$qqAuth = Utils_Factory::getAuthSession('qq');
            $qqAuth->token['auth_token'] = $tokens['oauth_token'];
            $qqAuth->token['auth_token_secret'] = $tokens['oauth_token_secret'];
    	 }
    	 return $tokens;
    }
    
    public function executeApi($url, $params, $method, $format = 'json', $multi = FALSE ){
          $response = $this->oAuthRequest($method, $this->_host . $url, $params, $multi);
          if ($format == 'json'){
             return json_decode($response);
          }
          return $response;
    }
    
    protected function getRequestToken($callback_url){
       
        $response = $this->oAuthRequest('GET', $this->_requestTokenUrl, array('oauth_callback' => $callback_url));
        
        parse_str($response, $tokens);
    
    	 if (isset($tokens['oauth_token']) && $tokens['oauth_token'] != NULL
    	 	 && isset($tokens['oauth_token_secret']) && $tokens['oauth_token_secret'] != NULL
    	 	 && isset($tokens['oauth_callback_confirmed']) && $tokens['oauth_callback_confirmed'] == 'true')
    	 {
    	 	$qqAuth = Utils_Factory::getAuthSession('qq');
    	 	$qqAuth->token['auth_token'] = $tokens['oauth_token'];
    	 	$qqAuth->token['auth_token_secret'] = $tokens['oauth_token_secret'];
    	 	
    	 	return $tokens;
    	 }
    	 return NULL;
    }
    
    protected function makeSignature($url, $method, $params)
    {
    	$qqAuth = Utils_Factory::getAuthSession('qq');
    	
        $secretKey = array(rawurlencode($this->_appsecret), 
                           rawurlencode(isset($qqAuth->token['auth_token_secret']) ? $qqAuth->token['auth_token_secret'] : ''));
        $secret = join('&', $secretKey); //这里也是会出现一些signature invalid 现象
       
        $baseString = $this->buildBaseString($method, $url, $params);

        return base64_encode( hash_hmac('sha1', $baseString, $secret, true) );
    }
    
    protected function buildBaseString($method, $url, $params){
        $baseString = strtoupper($method) . '&' . rawurlencode($url) . '&';
        
        uksort($params, 'strcmp');
        $query_array = array();
        foreach ($params as $key => $value) {
            /*注意：每一个参数都要进行一次encode,否则仅拼接一次后才rawurlencode就会出现signature invalid
                                    尤其是oauth_callback，由于有：和\所以会进行两次encode，(:=>%A3 => %25%A3)(\=>%2F=>%25%2F)*/
            array_push($query_array, rawurlencode($key) . '=' . rawurlencode($value));
        }
        $query_string = join('&', $query_array);
        
        return $baseString . rawurlencode($query_string);
    }
    
    protected function oAuthRequest($method, $url, $params, $multi = FALSE)
    {
        /*$params['format'] = 'json';注意这个参数不能参与到basestring中进行计算，否则也会出现signature invalid
                          他只在非授权请求的时候才降入到basestring的运算中*/
        $params['oauth_consumer_key'] = $this->_appkey; 
        $params['oauth_nonce'] =  md5( mt_rand() . microtime() );
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $params['oauth_timestamp'] = time();
        $params['oauth_version'] = '1.0';
        
        $oauth_signature = $this->makeSignature($url, $method, $params);
        $params['oauth_signature'] = $oauth_signature;
    	if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
			$url = "{$this->_host}{$url}";
		}
		
        $headers = array();
        if (isset(Utils_Factory::getAuthSession('qq')->token['auth_token']) 
            && Utils_Factory::getAuthSession('qq')->token['auth_token'] != NULL)
        {
            $headers[] = "Authorization: OAuth2 " . Utils_Factory::getAuthSession('qq')->token['auth_token'];
        }
        
        return $this->_baseHttp->http($url, $method, $params, $multi, $headers);
    }
}