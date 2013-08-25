<?php
require_once 'Api_SDK/OpenAuth.php';

class OAuth1a extends OpenAuth
{
	protected $_requestTokenUrl; //获取auth token 的网址
	
    public function __construct(){
        parent::__construct();
    }
    
}