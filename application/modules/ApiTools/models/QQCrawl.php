<?php
class Apitools_Model_QQCrawl extends Apitools_Model_CrawlAbstract{
	
	protected $_wikiBasePath = 'http://wiki.open.t.qq.com';
	protected $_indexUrl = 'http://wiki.open.t.qq.com/index.php/API%E6%96%87%E6%A1%A3';
	
	
	public function __construct(){
		$this->_dbModel = new Apitools_Model_Db_QQ();
		$this->_indexFile = file_get_contents($this->_indexUrl);
	}
	
	public function getApisSummaryInfo(){
		$pattern = '/<th.*?<a .*?>(.*?)<\/a>(?:[\w\W]*?)(?=<th)/i';
		preg_match_all($pattern, $this->_indexFile, $matches);
		$catalogs = array();
		
		for($i = 0; $i < count($matches[1]); $i++) {
			$conetent = $matches[0][$i];
			$pattern = '/<tr>\s+<td> ([\w\W]*?)\s<\/td>\s+<td>.*?<a href="(.*?)".*?>(.*?)<\/a>/i';
			$catalogs[$matches[1][$i]] = array();
			preg_match_all($pattern, $conetent, $result);
			for($j = 0; $j < count($result[1]); ++$j){
				$api = array('name'=>$result[1][$j], 
							 'url'=>$this->_wikiBasePath . $result[2][$j], 
							 'desc'=>$result[3][$j]);
				array_push($catalogs[$matches[1][$i]], $api);
			}
		}
		$summary['qq'] = $catalogs;
		return $summary;
	}
	
	public function getApiDetailsInfo($api_url, $api_name)
	{
		$content = file_get_contents($api_url);
		
		$pattern_desc = '/<h1 id="firstHeading" class="firstHeading">([\w\W]*?)<div>/i';
		preg_match_all($pattern_desc, $content, $matches_desc);
		$result = new stdClass();
		$result->desc = $matches_desc[1][0];
		
		$pattern = '/<table .*?>([\w\W]*?)<\/table>/i';
		preg_match_all($pattern, $content, $matches);
		
		$result->name = $api_name;
	    $pattern = '/<tr>[\w\W]*?<td> +([\w\W]*?)\s+<\/td>\s*<td> +([\w\W]*?)\s+<\/td><\/tr>/i';
        preg_match_all($pattern, $matches[1][1], $request);
        //$requestParams = array();
        $requestParams = new stdClass(); 
        //var_dump($matches[1]);
        for($i = 0; $i < count($request[1]); $i++){
            if ($request[1][$i] == 'url'){
                //$requestParams['name'] = 
            }
            elseif ($request[1][$i] == '格式'){
                $result->format = $request[2][$i];
            }
            elseif ($request[1][$i] == 'http请求方式'){
                $result->method = $request[2][$i];
            }
            elseif ($request[1][$i] == '是否需要鉴权'){
                $result->bAuth = $request[2][$i];
            }
            elseif ($request[1][$i] == '请求数限制'){
            }
        }
		//$result = $this->getRequestInfo($matches[1][1], $result);
		//$result = $this->getParamsInfo($matches[1][2], $result);
		
	    $pattern = '/<tr>[\w\W]*?<td.*?> ([\w\W]*?)\s*?<\/td>\s*<td> *?([\w\W]*?)\s*?<\/td><\/tr>/i';
        preg_match_all($pattern, $matches[1][2], $paramters);
        for ($i = 0; $i < count($paramters[1]); ++$i) {
            $result->paramNames[$i] = $paramters[1][$i];
            $result->paramDesc[$i] = $paramters[1][$i];
            $result->paramTypes[$i] = 'int';
            $result->paramNeeds[$i] = true;          
        }
		return $result;
	}
    
    /**/
	protected function getRequestInfo($request, $result){
		$pattern = '/<tr>[\w\W]*?<td> +([\w\W]*?)\s+<\/td>\s*<td> +([\w\W]*?)\s+<\/td><\/tr>/i';
		preg_match_all($pattern, $request, $matches);
		//$requestParams = array();
		$requestParams = new stdClass(); 
		//var_dump($matches[1]);
		for($i = 0; $i < count($matches[1]); $i++){
			if ($matches[1][$i] == 'url'){
				//$requestParams['name'] = 
			}
			elseif ($matches[1][$i] == '格式'){
				$result->format = $matches[2][$i];
			}
			elseif ($matches[1][$i] == 'http请求方式'){
				$result->method = $matches[2][$i];
			}
			elseif ($matches[1][$i] == '是否需要鉴权'){
				$result->bAuth = $matches[2][$i];
			}
			elseif ($matches[1][$i] == '请求数限制'){
			}
		}
		return $result;
	}
	
	protected function getParamsInfo($match, $result){
		$pattern = '/<tr>[\w\W]*?<td.*?> ([\w\W]*?)\s*?<\/td>\s*<td> *?([\w\W]*?)\s*?<\/td><\/tr>/i';
		preg_match_all($pattern, $match, $result);
		for ($i = 0; $i < count($result[1]); ++$i) {
			$result->paramNames[$i] = $result[1][$i];
			$result->paramDesc[$i] = $result[2][$i];
			$result->paramTypes[$i] = 'int';
			$result->paramNeeds[$i] = true;          
		}
		return $result;
		//return array('param_names'=>$paramNames, 'param_descs'=>$paramDesc);
	}
}