<?php
class Apitools_Model_SinaCrawl extends Apitools_Model_CrawlAbstract
{
	protected $_wikiBasePath = 'http://open.weibo.com';
	protected $_indexUrl = 'http://open.weibo.com/wiki/API%E6%96%87%E6%A1%A3_V2';


	public function __construct()
	{
		$this->_indexFile = file_get_contents( $this->_indexUrl );
		$this->_dbModel = new Apitools_Model_Db_Sina();
	}
	
	
	public function getApisSummaryInfo(){
	    
	     $pattern = '/<table[\w\W]*?<th .*?><span.*?>(.*?)<\/span>([\w\W]*?<\/table>)/i';
		 preg_match_all($pattern, $this->_indexFile, $mainCatalog);
		 $catalogs = array();
	     for ($i = 0; $i < count($mainCatalog[1]); ++$i){
		     $pattern = '/<td .*?>(.*)\s*<\/td>([\w\W]*?)(?:(?=<td )|(?=<\/table))/i';
		     preg_match_all($pattern, $mainCatalog[2][$i], $subCatalog);
	         for ($j = 0; $j < count($subCatalog[1]); ++$j){
    			 $catalog_name = $mainCatalog[1][$i]. ':' . $subCatalog[1][$j];
    			 $catalogs[$catalog_name] = $this->getApiLists($catalog_name, $subCatalog[2][$j] );
		     }
	     }
	     $summary['sina'] = $catalogs;
		 return $summary;
	}
		
	
	private function getApiLists($catalog, $content){
	    $pattern = '/<td><a href="(.*?)" .*?>(.*?)<\/a>\s*<\/td>\s*<td>(.*?)\s*(?=<)/i';
	    preg_match_all($pattern, $content, $matches);

		$api_links = $matches[1];
		$api_names = $matches[2];
		$api_desc = $matches[3];
		$apis = array();
		for ($i = 0; $i < count($api_names); ++$i){
		    $api = array('name' => $api_names[$i], 'url'=> $this->_wikiBasePath.$api_links[$i], 'desc'=>$api_desc[$i]);
			array_push($apis, $api);
		}
		return $apis;
	}
	
	protected function getApiDetailsInfo($apiAddress, $apiName){
		$content = file_get_contents($apiAddress);
		$pattern = '/<h2 .*?<span class="mw-headline" .*?>(.+)<\/span><\/h2>\s*(?:<p>[\w\W]*?<\/p>|<table[\w\W]*?<\/table>)/i';
		
		preg_match_all($pattern, $content, $matches);
		$apiProperties = new stdClass();
		$apiProperties->name = $apiName;
		for($i = 0; $i < count($matches[1]); ++$i)
		{
			if ($matches[1][$i] == '支持格式' 
				|| $matches[1][$i] == 'HTTP请求方式' )
			{
				$pattern = '/<p><span .*>(.+)<\/span>[\w\W]*?<\/p>/i';
				$result = array();
				preg_match_all($pattern, $matches[0][$i], $result);
				if ($matches[1][$i] == '支持格式'){ //获取具体的支持格式json
					$apiProperties->format = $result[1][0];
				}
				else{//获取http请求方式
					$apiProperties->method = $result[1][0];
				}
			}
			elseif($matches[1][$i] == $apiName)
			{
				$pattern = '/<p>(.+)[\w\W]*?<\/p>/i';
				$result = array();
				preg_match_all($pattern, $matches[0][$i], $result);
				$apiProperties->desc = $result[1][0];
			}
			elseif ($matches[1][$i] == '请求参数'){//获得具体参数
				$pattern = '/<td .*>(.+)[\w\W]*?<\/td>/i';
				$result = array();
				preg_match_all($pattern, $matches[0][$i], $result);
				if( count($result[1]) % 4 == 0 )
				{
					$index = 0;
					for ($i = 0; $i < count($result[1]); $i += 4){
						$apiProperties->paramNames[$index] = $result[1][$i] ;
						$apiProperties->paramNeeds[$index] = $result[1][$i+1] ;
						$apiProperties->paramTypes[$index] = $result[1][$i+2];
						$apiProperties->paramDesc[$index] = $result[1][$i+3];
						$index++;
					}
				}
			}
		}
		return $apiProperties;
	}
		
	/*below code is the extra interface for sina*/
    public function getApisSummaryInfo_Sina(){
	    $pattern = '/<h2>\s*<span +class="mw-headline">(.+)<\/span><\/h2>[\w\W]*?<\/p>/i';
		preg_match_all($pattern, $this->_indexFile, $matches);
		
		$summary = array();
		for ($i = 0; $i < count($matches[1]); ++$i){
		    $summary[$matches[1][$i]] = $this->getSmallCatalogInfo($matches[1][$i], $matches[0][$i]);
		}
		return $summary;
	}
	
	
	private function getSmallCatalogInfo($catalog, $content){
	    $pattern = '/<h3>\s*<span +class="mw-headline">(.+)<\/span><\/h3>[\w\W]*?<\/ul>/i';
		preg_match_all($pattern, $content, $matches);
		$catalogs = array();
	    for ($i = 0; $i < count($matches[1]); ++$i){
			$catalog_name = $catalog. ':' . $matches[1][$i];
			$catalogs[$catalog_name] = $this->getApiLists($catalog_name, $matches[0][$i] );
		}
		return $catalogs;
	}
}