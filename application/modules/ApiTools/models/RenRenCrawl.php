<?php
class Apitools_Model_RenRenCrawl extends Apitools_Model_CrawlAbstract{
    
    protected $_wikiBasePath = 'http://wiki.dev.renren.com/';
    protected $_indexUrl = 'http://wiki.dev.renren.com/wiki/API';
    
    
    public function __construct(){
        $this->_dbModel = new Apitools_Model_Db_RenRen();
        $this->_indexFile = file_get_contents($this->_indexUrl);
    }
    
    public function getApisSummaryInfo(){
        
         $pattern = '/<th rowspan=.*?>(.*?)\s*<\/th>[\w\W]*?(?=<th)/i';
         preg_match_all($pattern, $this->_indexFile, $mainCatalog);
         $catalogs = array();
         for ($i = 0; $i < count($mainCatalog[1]); ++$i){
             $pattern = '/<td>\s*<a href="(.*?)".*?>(.*?)<\/a>\s*<\/td><td>(.*?)\s*<\/td>/i';
             preg_match_all($pattern, $mainCatalog[0][$i], $apis);
             $catalog_name = trim($mainCatalog[1][$i]);
             $catalogs[$catalog_name] = array();
             for ($j = 0; $j < count($apis[1]); ++$j){
                 
                 $api = array('name'=>$apis[2][$j], 
                             'url'=>$this->_wikiBasePath . $apis[1][$j], 
                             'desc'=>$apis[3][$j]);
                 array_push($catalogs[$catalog_name], $api); 
             }
         }
         $summary['renren'] = $catalogs;
         return $summary;
    }
    
    public function getApiDetailsInfo($apiAddress, $apiName){
    	$content = file_get_contents($apiAddress);
        //$pattern = '/<tr>\s*<th scope="row">(.*?)<\/th>\s*<td>(.*?)<\/td>\s*<td>(.*?)<\/td>\s*<td>(.*?)<\/td>/i';
        $pattern = '/<p class="paragraph">\s*(.*?)<\/p>|<tr>\s*<th scope="row">(.*?)<\/th>\s*<td>(.*?)<\/td>\s*<td>(.*?)<\/td>\s*<td>(.*?)<\/td>/i';
        preg_match_all($pattern, $content, $params);
        $apiProperties = new stdClass();
        $apiProperties->name = $apiName;
        $apiProperties->desc = $params[1][0];
        $apiProperties->format = 'json';
        $apiProperties->method = 'post'; //renren都用post
        for ($i = 1; $i < count($params[2]); ++$i){
            $apiProperties->paramNames[$i] = $params[2][$i] ;
            $apiProperties->paramNeeds[$i] = $params[3][$i] ;
            $apiProperties->paramDesc[$i] = $params[5][$i];
            $apiProperties->paramTypes[$i] = $params[4][$i];
        }
       //  var_dump($apiProperties);
        return $apiProperties;
    }
}