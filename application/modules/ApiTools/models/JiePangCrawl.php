<?php
class Apitools_Model_JiePangCrawl extends Apitools_Model_CrawlAbstract{

	protected $_wikiBasePath = 'http://dev.jiepang.com/doc/get/';
    protected $_indexUrl = 'http://dev.jiepang.com/doc';
    
    public function __construct(){
        $this->_dbModel = new Apitools_Model_Db_JiePang();
        $this->_indexFile = file_get_contents($this->_indexUrl);
        $pattern = '/<div class="aside-sections">([\w\W]*?)<\/div>/i';
        preg_match_all($pattern, $this->_indexFile, $matches);
        $this->_indexFile = $matches[1][0];
    }
    
    public function getApisSummaryInfo(){
        $pattern = '/<h2>(.*?)<\/h2>\s*<ul>([\w\W]*?)<\/ul>/i';
        preg_match_all($pattern, $this->_indexFile, $match_catalogs);
        $catalogs = array();
        for ($i = 0; $i < count($match_catalogs[1]); ++$i){
        	$pattern = '/<li><a href="(.*?)">\/(.*?)<\/a><\/li>/i';
        	preg_match_all($pattern, $match_catalogs[2][$i], $match_apis);
        	$catalog_name = $match_catalogs[1][$i];
        	$catalogs[$catalog_name] = array();
            for ($j = 0; $j < count($match_apis[2]); ++$j){
                $api = array('name' => $match_apis[2][$j], 'url' => $this->_wikiBasePath.$match_apis[2][$j]);
                array_push($catalogs[$catalog_name], $api);
            }
        }
        $summary['jiepang'] = $catalogs;
        return $summary;
    }
    
    protected function getApiDetailsInfo($apiAddress, $apiName){
        $content = file_get_contents($apiAddress);
        $pattern = '/<div class="section" .*>([\w\W]*?)<\/div>/i';
        
        preg_match_all($pattern, $content, $matches);
        $apiProperties = new stdClass();
        $apiProperties->name = $apiName;
        $apiProperties->method = 'GET';
        $apiProperties->format = 'json';
        //$matches[1][0];//desc
        //$matches[1][4];//parameter;
        $pattern_desc = '/<div class="section" .*>\s*<p>(.*?)<\/p>\s*<\/div>/i';
        preg_match_all($pattern_desc, $content, $desc);
        $apiProperties->desc = $desc[1][0];
        $pattern_param = '/<p class="title">(.*?)<\/p>\s*<p class="note">(.*?)<\/p>[\w\W]*?<p>(.*?)<\/p>\s*<p>[\w\W]*?<\/span>(.*?)<\/p>/i';
        preg_match_all($pattern_param, $content, $params);
        //print_r($matches[1][4]);
        //var_dump($params);
        for ($i = 0; $i < count($params[1]); ++$i){
            $apiProperties->paramNames[$i] = $params[1][$i] ;
            $apiProperties->paramNeeds[$i] = $params[2][$i] ;
            $apiProperties->paramDesc[$i] = $params[3][$i];
            $apiProperties->paramTypes[$i] = $params[4][$i];
        }
        //var_dump($apiProperties);
        return $apiProperties;
    }
}