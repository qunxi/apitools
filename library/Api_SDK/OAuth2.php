<?php
require_once 'Api_SDK/OpenAuth.php';

class OAuth2 extends OpenAuth
{
	protected $_authorizeUrl = '';
    protected $_accessTokenUrl = '';
    protected $_appid = '';
    protected $_appsecret = '';
    protected $_host = '';
    
	public function __construct(){
        parent::__construct();
    }
    
    public function getAuthorizeUrl($redirect_uri,
                                    $response_type = 'code',
                                    $state = NULL,
                                    $display = NULL)
    {
    }
    
    public function getAccessToken($type = 'code', $keys){
    
    }
    
    public function executeApi($url, $params, $method, $format, $multi = FALSE){
    
    }
}