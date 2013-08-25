<?php
require_once 'Api_SDK/QQ/OAuth1a.php';
//require_once 'Api_SDK/QQ/OpenApiV3.php';
class Apitools_Model_RenRenApi extends Apitools_Model_ApiAbstract
{
    const APP_ID = '';
    const APP_KEY = '801107450';
    const APP_SECRET = '62e1b97304b9eb5179ce684e71bc6ecf';
    const VERSION = 3;
    //const APP_HOST = '113.108.20.23';
    
    public function __construct(){
        $this->_dbModel = new Apitools_Model_Db_RenRen();
        //$this->_sdkClient = new QQ_OAuth1a( self::APP_ID, self::APP_KEY, self::APP_SECRET);
    }
    
    public function run($url, $parameters, $http_type, $format = 'json')
    {
       
    }
}