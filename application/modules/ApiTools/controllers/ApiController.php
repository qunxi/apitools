<?php
require_once 'Api_SDK/Utils/Factory.php';

class Apitools_ApiController extends Zend_Controller_Action
{

	private $_apiModel = null;
    public function init()
    {
       
    }

    public function indexAction()
    {
    	$JSON_URL = 'http://tools.jb51.net/tools/json/json_editor.htm';
		$file = file_get_contents( $JSON_URL );
		echo $file;
    }
    
    protected function getApiModel($provider){
        switch ($provider) {
            case 'qq':
                return new Apitools_Model_QQApi();
            case 'sina' :
                return new Apitools_Model_SinaApi();
            case 'jiepang' :
                return new Apitools_Model_JiePangApi();
            default:
                return null;
            break;
        }
    }
    
    public function sinaAction()
    {
        $sinaApi = new Apitools_Model_SinaApi();
        $this->view->provider = 'sina';
        $this->view->apiList = $sinaApi->initialzieApiList();

        $sinaAuth = Utils_Factory::getAuthSession('sina');
   	    $request = $this->getRequest();
   	   	if (!isset($sinaAuth->token) && $request->getParam('code'))
   	   	{
   	   	    //不建议这么写回调地址
   	   	    $callback = 'http://' . $request->getHttpHost() . $request->getBaseUrl() . '/ApiTools/Api/sina';
   	   	    $sinaApi->setAccessToken($request->getParam('code'), $callback);
   	   	    $this->_redirect($callback);
   	   	} 
    }
    
    public function switchAction()
    {
        $request = $this->getRequest();
        $apiModel = null;
        if ($request->isXmlHttpRequest()){

            $provider = $request->getQuery('provider');
            $apiModel = $this->getApiModel($provider);

            $catalog_id = $request->getQuery('catalog');
            $api_id = $request->getQuery('api');

            if ($catalog_id && $apiModel)
            {
                $apis = $apiModel->getApisOfCatalog($catalog_id);
                $this->_helper->getHelper('Json')->sendJson($apis);
        	}
        	if ($api_id && $apiModel)
        	{
        	    $api = $apiModel->getApiDetailsInfo($api_id);
        		$this->_helper->getHelper('Json')->sendJson($api);
        	}
        }
    }
    
    public function executeAction()
    {
       $this->_helper->getHelper('viewRenderer')->setNoRender();
       
   	   $param_names = array();
   	   $apiModel = null;
   	   
   	   $request = $this->getRequest();
   	   if ($request->isPost())
   	   {
   	      $param_names = $request->getPost();
   	      $apiModel = $this->getApiModel($param_names['provider_name']);
   	      
    	  $result = $apiModel->run($param_names['api'], $param_names, $param_names['method']);
    	 // var_dump($result);
    	  if (is_string($result)){ //返回authorize url;
    	     
    	   	  $this->_helper->getHelper('Json')->sendJson(array('url'=>$result));
    	  }
    	  else{
    	   	  $this->_helper->getHelper('Json')->sendJson($result);
    	  }
   	   }
    }
    
    public function qqAction()
    {      
        $qqApi = new Apitools_Model_QQApi();
        $this->view->provider = 'qq';
        $this->view->apiList = $qqApi->initialzieApiList();
        
        $request = $this->getRequest();
        $params = $request->getParams();
        
        $qqAuth = Utils_Factory::getAuthSession('qq');
        
        if ( isset($params['oauth_token']) && $params['oauth_token']
             && isset($params['oauth_verifier']) && $params['oauth_verifier'])
        {
            //不建议这么写回调地址
            $callback = 'http://' . $request->getHttpHost() . $request->getBaseUrl() . '/ApiTools/Api/qq';
            $qqApi->setAccessToken(array('oauth_token'=>$params['oauth_token'],
                                         'oauth_verifier'=>$params['oauth_verifier']));
            $this->_redirect($callback);
        } 
        
      
    }
    
    public function googleAction(){
    	echo file_get_contents('http://code.google.com/intl/zh-CN/more/table');
    	
        //print_r('敬请期待...');
    }
    
    public function baiduAction(){
        print_r('敬请期待...');
    }
    
    public function kaixinAction(){
        print_r('敬请期待...');
    }
    
    public function renrenAction(){
        print_r('敬请期待...');
        $webCallback = $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']; 
        $renrenApi = new Apitools_Model_RenRenApi($webCallback);
        $this->view->apiList = $renrenApi->initialzieApiList();
    }
    
    public function jiepangAction(){
        print_r('敬请期待...');
        $jiepangApi = new Apitools_Model_JiePangApi();
        
        $this->view->provider = 'jiepang';
        $this->view->apiList = $jiepangApi->initialzieApiList();

        $auth = Utils_Factory::getAuthSession('jiepang');
        $request = $this->getRequest();
        if (!isset($auth->token) && $request->getParam('code'))
        {
            //不建议这么写回调地址
            $callback = 'http://' . $request->getHttpHost() . $request->getBaseUrl() . '/ApiTools/Api/jiepang';
            $jiepangApi->setAccessToken($request->getParam('code'), $callback);
            //$this->_redirect($callback);
        } 
        
    }
    
    public function taobaoAction(){
       print_r('敬请期待...');
    }
}

