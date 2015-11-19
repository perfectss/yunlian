<?php
class Option_service extends MY_Service{
	function __construct()
	{
		parent::__construct();
		$this->load->model("Category_option_model","category_option");
		$this->load->model("Category_model","category");
		$this->load->model("Sell_model","sell");
	}
	
	/**
	 * 返回自定义展示的属性
	 * @param $attrs array("属性名称"=>array(属性值的数组)|属性值)
	 * @param $arrayOptions array("属性名称","属性名称2",.....)
	 * @return  array  array("power"=>"1500w","Voltage"=>'')  没有该属性 对应值为空值
	 Custom
	 */
	public function serviceSellCustomOption($attrs,$arrayOptions){
		if(!is_array($attrs) or !is_array($arrayOptions)){
			throw new Exception("param attrs or arrayOptions is not Array");
		}
		$customAttrs = array();
		foreach($arrayOptions as $ao){
			foreach($attrs as $ov){
				if(stripos($ov['name'],$ao)!==false){
					$customAttrs[$ao] = $ov['value'];
					continue;
				}
			}
			if(!isset($customAttrs[$ao])){
				$customAttrs[$ao] = '';
			}
		}
		
		return $customAttrs;
	}
	
	/**
	 * 对统计到属性按照分类默认属性进行排序
	 * $attrs array("属性名称"=>array(属性值的数组)|属性值)
	 * 
	 */
	public function serviceDefaultOptionOrder($attrs,$catid){
		if(!is_array($attrs)){
			throw new Exception("param $attrs is not Array");
		}
		
		$defaultOption = $this->category_option->getCategoryDefaultOption($catid);
		$newattrs = array();
		foreach($attrs as $option => $value){
			foreach($defaultOption as $op => $ov){
				if(stripos($option,$op)!==false){
					$newattrs[$option] = $value;
					unset($attrs[$option]);
					continue;
				}
			}
		}
		if(empty($newattrs)){
			return $attrs;
		}else{
			return array_merge($newattrs,$attrs);
		}
	}
	
	
	/**
	 * 返回唯一化地址 分类下的默认所有属性
	 * $arrayAttrids array("oid的值"=>"option_value表的id值",.....)
	 * 
	 */
	public function serviceDefaultOptionCanonical($arrayAttrids,$catid){
		if(!is_array($arrayAttrids)){
			throw new Exception("arrayAttrids Param is not Array");
		}
	
		$thisCategory = $this->category->getCategory($catid);
		$defaultOption = $this->category_option->getCategoryDefaultOption($catid);
		foreach($defaultOption as $name => $ov){
			$attrs = $arrayAttrids;
			$newvids = array();
			foreach($ov as $k => $value){
				$tmpsql = '';
				if(array_key_exists($value['oid'],$attrs)){
					unset($attrs[$value['oid']]);
				}
				$newvids = array($value['oid']=>$value['id']) + $attrs;
				$linkurl = array();
				$opvalue = array();
				
				foreach($newvids as $oid => $nv){
					$linkurl[] = $oid."-".$nv;
					$tmpov = $this->comm->find("category_option_value",array("id"=>$nv));
					$opvalue[] = $tmpov['value'];
				}
				$defaultOption[$name][$k]['linkurl'] =  implode("_",$linkurl);
				$defaultOption[$name][$k]['title'] = implode(" ",$opvalue)." ".$thisCategory['catname'];
				$defaultOption[$name][$k]['titlelinkurl'] = preg_replace("/[^0-9a-z]+/i","-",$defaultOption[$name][$k]['title']);
				
			}
		}
		return $defaultOption;
	}
}