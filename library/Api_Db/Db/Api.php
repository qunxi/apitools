<?php
require_once 'Entity.php';

class Db_Model_Api extends Db_Model_Entity
{       
        protected $_properties = array('id'=>null,
    								  'name'=>null,
    								  'method'=>null,
    								  'is_auth' => 1,
    								  'is_login' => 1,
    								  'auth_type' => null,
    								  'param_names' => null,
    								  'param_select'=>null,
    								  'param_types'=>null,
    								  'param_desc'=>null,
    								  'format'=>null,
    								  'api_desc'=>null,
    								  'catalog_id'=>null);
    
        protected $_table = 'apis';
        
        public function __construct()
        {
            parent::__construct();
        }
        
        public function getApiSummaryByCatalog($catalog_id)
        {
            $apis = $this->getAdapter()
                        ->select()
                        ->from( $this->_table, array('id','api_desc','name','method') )
                        ->where('catalog_id = ?', $catalog_id)
                        ->query()
                        ->fetchAll();
		    return $apis;
        }
        
        public function getApiDetailsByApi($api_id)
        {
            $details = $this->getAdapter()
        					->select()
        					->from( $this->_table, '*')
        					->where('id = ?', $api_id)
        					->query()
        					->fetch();
		    return $details;
        }
        
        public function isExistApi($catalog_id, $name)
        {
        	$details = $this->getAdapter()
        					->select()
        					->from( $this->_table, '*')
        					->where('name = ?', $name)
        					->where('catalog_id = ?', $catalog_id)
        					->query()
        					->fetch();
        	if ($details){
        		return true;
        	}
        	else{
        		return false;
        	}
        }
        
        public function addApi()
        {
            if($this->getAdapter()->insert($this->_table, $this->_properties)){
                return $this->getAdapter()->lastInsertId($this->_table);
            }
        }
}

