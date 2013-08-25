<?php
require_once 'Api_SDK/BaseHttp.php';

class OAuthException extends Exception {
	// pass
}

class OpenAuth{
    protected $_baseHttp;
        
    protected $_callback;
    protected $_authorizeUrl; //获取授权页面(也就是登陆页面的网址)
    protected $_accessTokenUrl;//获取access token的网址

    public function __construct(){
        date_default_timezone_set("Asia/Shanghai");//如果时间不设置的话，可能会和qq服务器不同步，也导致signature invalid
        $this->_baseHttp = new BaseHttp();     
    }
    
    public function setCallbackUrl($callback){
        $this->_callback = $callback;
    }
}