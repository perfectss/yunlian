<?php
class Category_model extends MY_Model {
	
	public $gcategory;
	public $catid;
	
    function __construct()
    {
		parent::__construct();
		$category = array();
		$cate = array();
		$cate = $this->findAll("category");
		foreach($cate as $v){
			$category[$v['catid']] = $v; 
		}
		
		$this->gcategory = $category;
    }
	/**
	* 添加分类
	* @param $arr_category 数组 array("字段名"=>"值")
	*/
	function add($arr_category){
		$this->db->insert("category",$arr_category);
		$catid = $this->db->insert_id();
		$this->catid = $catid;
		if($arr_category['parentid']){
			$arr_category['catid'] = $this->catid;
			$this->gcategory[$this->catid] = $arr_category;
			$arrparentid = $this->get_arrparentid($catid,$this->gcategory);
		}else{
			$arrparentid = '0';
		}
		
		$this->db->update("category",array("arrparentid"=>$arrparentid,"listorder"=>$catid),array("catid"=>$catid));
		
		if($arr_category['parentid']){
			$childs = '';
			$childs .= ",".$catid;
			$parents = array();
			$parents = $this->get_arrparentid($catid,$this->gcategory,FALSE);
			foreach($parents as $catid) {
				$arrchildid = $this->gcategory[$catid]['child'] ? $this->gcategory[$catid]['arrchildid'].$childs : $catid.$childs;
				$this->db->update("category",array("child"=>1,"arrchildid"=>$arrchildid),array("catid"=>$catid));
			}
		}
		return $catid;
		
		
	}
	
	/**
	* 获取父类ID
	* @param $catid 分类ID
	* @param $gcategory 全部分类
	* @param $type 返回字符串或者数组 默认返回字符串
	*/
	function get_arrparentid($catid,$gcategory,$type=TRUE){
		if($gcategory[$catid]['parentid']){
			$parents = array();
			$cid = $catid;
			while($catid) {
				if($gcategory[$cid]['parentid']) {
					$parents[] = $cid = $gcategory[$cid]['parentid'];
				} else {
					break;
				}
			}
			if($type === TRUE){
				$parents[] = 0;
				return implode(',', array_reverse($parents));
			}else{
				return $parents;
			}
		}else{
			return '0';
		}
	}
	
	/**
	* 删除分类 有子分类的无法删除，修复父级分类
	* @param $catid 分类ID
	*/
	function del($catid){
		
		$findcat = $this->find("category",array("catid"=>$catid));
		if(!$findcat){
			return FALSE;
		}
		if($findcat['child'] == 0){
			$this->db->delete("category",array("catid"=>$catid));
			$findparent = $this->find("category",array("parentid"=>$findcat['parentid']));
			if(!$findparent){
				$this->db->update("category",array("child"=>0),array("catid"=>$findcat['parentid']));
			}
			$parents = $this->get_arrparentid($catid,$this->gcategory,FALSE);
			foreach($parents as $cid) {
				$arrchildid = str_replace(",".$catid,"",$this->gcategory[$cid]['arrchildid']);
				$this->db->update("category",array("arrchildid"=>$arrchildid),array("catid"=>$cid));
			}
			return TRUE;
		}else{
			return FALSE;
		}
		
	}	
}