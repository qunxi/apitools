<?php
require_once 'Entity.php';

class Db_Model_Catalog extends Db_Model_Entity
{
    protected $_properties = array('id'=>null,
	                             'name'=>null,
	                             'provider_id'=>null);
    
    protected $_table = 'catalogs';
    //protected $_apiEntity = null;
    
    public function __construct()
    {
        parent::__construct();
        //$this->_apiEntity = new Db_Model_Api($data)
    }
    
    public function getCatalogsByProvider($provider_id)
	{
		$catalogs = $this->getAdapter()
    			   ->select()
                   ->from($this->_table)
                   ->where('provider_id = ?', $provider_id)
                   ->query()
                   ->fetchAll();
                    
        return $catalogs;
	}
    
	public function isExistCatalog($name, $provider_id)
	{
		$result = $this->getAdapter()
    			   ->select()
                   ->from($this->_table, array('id', 'name'))
                   ->where('name = ?', $name)
                   ->where('provider_id = ?', $provider_id)
                   ->query()
                   ->fetch();
        if ($result){
        	return $result['id'];
        }
        else{
        	return false;
        }
	}
	
	public function addCatalog()
	{
	    var_dump($this->_properties);
	    if ($this->getAdapter()->insert($this->_table, $this->_properties)){
	            return $this->getAdapter()->lastInsertId();
	    }
	    return false;
	}
}