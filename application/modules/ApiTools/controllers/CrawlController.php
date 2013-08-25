<?php

class Apitools_CrawlController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    	//$sina = new Apitools_Model_SinaCrawl();
        //$this->_apis = $sina->getSinaApisInfo();
        //var_dump($this->_apis);
    	//var_dump('12');
    }

    public function indexAction()
    {
         $qq = new Apitools_Model_QQApi();
	    var_dump($qq->test());
	    //$this->view->url = $qq->test();
    }
    
    public function insertAction()
    {
    	//$this->_helper->getHelper('viewRenderer')->setNoRender();
    	$request = $this->getRequest();
        if ($request->isXmlHttpRequest())
        {
        	$catalog_name = $request->getQuery('catalog');
        	$apiName = $request->getQuery('api');
        	$url = $request->getQuery('url');
        	$provider = $request->getQuery('provider');
        	//var_dump($provider);
        	//$sina = new Apitools_Model_SinaCrawl();
        	$crawModel = $this->getCrawlObj($provider);
        	$crawModel->addCrawlData2Db($catalog_name, $apiName, $url);
        	//$result = $sina->getApiDetailsInfo($url, $apiName);   
        	//$sina->insertApiInfo2Db($cgid, $url, $apiName);
        	//var_dump($result);
        	//$this->view->result = $result;
        	//$this->_redirect('ApiTools/crawl/insert');
        	$this->_helper->getHelper('Json')->sendJson('OK');
        }
    }

    private function getCrawlObj($provider){
        if ($provider == 'qq'){
            return new Apitools_Model_QQCrawl();
        }
        elseif($provider == 'sina'){
            return new Apitools_Model_SinaCrawl();
        }
        elseif ($provider == 'jiepang'){
            return new Apitools_Model_JiePangCrawl();
        }
        elseif ($provider == 'renren'){
            return new Apitools_Model_RenRenCrawl();
        }
    }
    
    public function sinaAction(){
        $sinaCrawl = new Apitools_Model_SinaCrawl();
        $this->getApisSummaryInfo($sinaCrawl);
       
    }
    
	public function qqAction()
	{
		$qqCrawl = new Apitools_Model_QQCrawl();
		$this->getApisSummaryInfo($qqCrawl);
	}
	
	public function jiepangAction()
	{
		$jiepangCrawl = new Apitools_Model_JiePangCrawl();
		$this->getApisSummaryInfo($jiepangCrawl);
	}

	public function renrenAction()
	{
		$renrenCrawl = new Apitools_Model_RenRenCrawl();
		$this->getApisSummaryInfo($renrenCrawl);
	}
	 
	private function getApisSummaryInfo($crawlMode)
	{ 
	    $this->view->summary = $crawlMode->getApisSummaryInfo();
		$request = $this->getRequest();
        if ($request->isXmlHttpRequest())
        {
        	$catalog = $request->getQuery('catalog');
        	$type = $request->getQuery('type');
        	$catalog = trim($catalog);
        	$apis = $this->view->summary[$type][$catalog];
        	$this->_helper->getHelper('Json')->sendJson($apis);
        } 
	}
	
	public function testAction(){
		/*$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();
		
		$qqCrawl = new Apitools_Model_QQCrawl();
		$request = $this->getRequest();
		 if ($request->isXmlHttpRequest()){
	        	$catalog = $request->getQuery('catalog');
	        	$api_name = $request->getQuery('api');
	        	$api_url = $request->getQuery('url');
	        	$ret = $qqCrawl->getApiDetailsInfo($api_name, $api_url);
				$this->_helper->getHelper('Json')->sendJson($ret);
	        }
	   */
		}
	    
}

