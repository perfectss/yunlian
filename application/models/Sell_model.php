<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sell_model extends MY_Model{
	function __construct()
	{
		parent::__construct();
		$this->load->model("category_model","category");
	}

   
	static $table = "sell";
	
	
	private function getProductsByCatid($catid,$orderby,$limit){
		$limit = intval($limit);
		if(!is_int($limit) or empty($limit)){
			throw new Exception("param limit is not Int");
		}
		if(!$catid){
			$products = $this->findAll(self::$table,array("status"=>3),$orderby,"*",$limit);
		}else{
			$thisCategory = $this->category->getCategory($catid);
			if($orderby == "addtime desc"){
				$products = $this->findAll(self::$table." use index(status_catid_addtime)","status = 3 and catid in({$thisCategory['arrchildid']})",$orderby,"*",$limit);
			}else{
				$products = $this->findAll(self::$table,"status = 3 and catid in({$thisCategory['arrchildid']})",$orderby,"*",$limit);
			}
			
		}
		return $products;
	}
	
	
	
	public function getSell($itemid){
		$itemid = intval($itemid);
		if(!is_int($itemid) or empty($itemid)){
			throw new Exception("param itemid is not Int");
		}
		$rs = $this->find(self::$table,array("itemid"=>$itemid));
		if($rs){
			$sell_data = $this->find("sell_data",array("itemid"=>$itemid));
			$rs['content'] = $sell_data['content'];
		}
		return $rs;
		
	}
	
	
	
    /**
     * 查询最新的产品
     * 
     */
	public function getLatestProducts($limit){
		return $this->getProductsByCatid(0,"addtime desc",$limit);
		
	}
	
	/**
     * 查询最热门的产品
     * 
     */
	public function getHotProducts($limit){
		return $this->getProductsByCatid(0,"hits desc",$limit);
	}
	
	 /**
     * 查询分类下最新的产品
     * 
     */
	public function getCategoryLatestProducts($catid,$limit){
		return $this->getProductsByCatid($catid,"addtime desc",$limit);
		
	}
	
	/**
     * 查询分类下最热门的产品
     * 
     */
	public function getCategoryHotProducts($catid,$limit){
		return $this->getProductsByCatid($catid,"hits desc",$limit);
	}
	
	 /**
     * 商品热度+1
     * @param $itemid
     */
    public function addSellHits($itemid){
        $this->db->set("hits","hits+1",FALSE);
        $this->db->where("itemid",$itemid);
        $this->db->update(self::$table);
    }

	

}
