<?php
class Category_option_model extends MY_Model
{

    function __construct()
    {
        parent::__construct();
		$this->load->model("category_model","category");
    }
	
	static $table = "category_option";
	
	/**
     * 获取属性名称
     * @param $oid int
     * @return array 
     */
	
	public function getOption($oid){
		$oid = intval($oid);
		if(!is_int($oid) or empty($oid)){
			throw new Exception("param oid is not Int");
		}
		return $this->find(self::$table,array("oid"=>$oid));
	}
	
	
    /**
     * 获取商品属性
     * @param $itemid
     * @return array  array([属性名称]=>array("name"=>"power","value"=>"1500W"),[属性名称1].....)
     */
    public function getSellOption($itemid){
		$rs = $this->findAll("category_value",array("itemid"=>$itemid));
		if(!$rs){
			return false;
		}
		$op_value = array();
		foreach($rs as $k => $r){
			$option = $this->getOption($r['oid']);
			$op_value[$option['name']]['name'] = $option['name'];
			$op_value[$option['name']]['value'] = $r['value'];
		}
        return $op_value;
    }
	
	 /**
     * 获取分类下默认属性和值  
     * return array("属性名"=>array(category_option_value全字段))
     */
	public function getCategoryDefaultOption($catid){
		$notOption = array(3511,3512,3513,3514,3515,3516,3517,3518,3519,3520,3521);
		if(in_array($catid,$notOption)){
			return false;
		}
		$doption = $this->findAll(self::$table,array("catid"=>$catid,"required"=>1),"listorder asc");
		if(!$doption){
			$topCategory = $this->category->getTopParentCategory($catid);
			$doption = $this->findAll(self::$table,array("catid"=>$topCategory['catid'],"required"=>1),"listorder asc");
			if(!$doption){
				return false;
			}
		}
		foreach($doption as $k => $v){
			$op_value[$v['name']] = $this->comm->findAll("category_option_value",array("oid"=>$v['oid']),"id asc");
		}
		return $op_value;
	}
	
	/**
	 * 返回唯一化地址 分类下的属性
	 * $arrayAttrids array("oid的值"=>"option_value表的id值",.....)
	 * 
	 */
	public function getCategoryOptionCanonical($arrayAttrids,$catid){
		if(!is_array($arrayAttrids) || empty($arrayAttrids)){
			throw new Exception("arrayAttrids Param is not Array Or Empty");
		}
		$notOption = array(3511,3512,3513,3514,3515,3516,3517,3518,3519,3520,3521);
		if(in_array($catid,$notOption)){
			return false;
		}
		$tmpsql = '';
		$num = count($arrayAttrids);
		foreach($arrayAttrids as $oid => $iid){
			$tmpsql.= " and FIND_IN_SET('".$oid."|".$iid."',attrs)";
		}
		$OptionCanonical = $this->comm->find("attrtag","catid = {$catid} and num = {$num} {$tmpsql}");
		
		if(!$OptionCanonical){
			$topCategory = $this->category->getTopParentCategory($catid);
			$OptionCanonical = $this->comm->find("attrtag","catid = {$topCategory['catid']} and num = {$num} {$tmpsql}");
			if(!$OptionCanonical){
				return false;
			}
			$thisCategory = $this->category->getCategory($catid);
			$OptionCanonical['catname'] = $thisCategory['catname'];
		}
		
		$OptionCanonical['title'] = $OptionCanonical['tag']." ".$OptionCanonical['catname'];
		$OptionCanonical['titlelinkurl']  = preg_replace("/[^0-9a-z]+/i","-",$OptionCanonical['title']);
		return $OptionCanonical;
	}




}