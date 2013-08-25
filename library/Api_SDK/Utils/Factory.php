<?php
class Utils_Factory
{
  
    public static function getAuthSession($provider){
    	Zend_Session::start();
        switch ($provider){
        	case 'qq':  //return token: token['auth_token']\token['oauth_token_secret']
                return new Zend_Session_Namespace('Auth_QQ');
        	case 'sina':// return tokens: token['access_token']\token['expire_in']\token['refresh_token'];
        		return new Zend_Session_Namespace('Auth_Sina');
            case 'jiepang':// return tokens: token['access_token']\token['expire_in']\token['refresh_token'];
                return new Zend_Session_Namespace('Auth_JiePang');
        }
    }
    
    /*public static function getAuthCookie($provider){
        switch ($provider){
        	case 'qq':
        		setc
        	case 'sina':
        }
    }*/
    
    public static function getCallBackUrl($provider){
        switch ($provider){
            case 'qq':  
              //  return $_SERVER['SERVER_NAME'] . ''
            case 'sina':
               // $auth_sina = new Zend_Session_Namespace('Auth_Sina');
                break;
        }
    }
}