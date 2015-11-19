<?php
class Readattrs extends CI_Controller{
	function index(){
		$this->load->model("comm_model","comm");
		$content = file_get_contents("./csv/Ac Motors.csv");
		$content = str_replace(array("\r\n","\r"),"\n",$content);
		$arrcontent = explode("\n",$content);
		foreach($arrcontent as $k => $v){
			$attrs = explode(",",$v);
			if($k == 0){
				$catname = $attrs[0];
			}else{
				foreach($attrs as $j => $a){
					if(empty($a)){
						continue;
					}
					if($j==0){
						$mattrs[$catname][$attrs[0]] = array();
					}else{
						$mattrs[$catname][$attrs[0]][] = $a;
					}
				
				}
			}
			
			
		}
		
		dump($mattrs);
		foreach($mattrs as $k => $m){
				$catname = $k;
				dump($catname);
				$findcat = $this->comm->find("category",array("catname"=>$catname));
				$catid = $findcat['catid'];
				dump($findcat);
				if(!$findcat){
					dump("catname is not exist");
					die();
				}else{
					if($findcat['parentid']!=0){
						dump("catname is not parent");
						die();
					}
					
				}
			
				$i = 0;
				foreach($m as $option => $value){
					$findoption = $this->comm->find("category_option",array("catid"=>$findcat['catid'],"name"=>$option));
					if($findoption){
						$oid = $findoption['oid'];
					}else{
						$oid = $this->comm->create("category_option",array("catid"=>$findcat['catid'],"name"=>$option));
						
					}
					foreach($value as $v){
							$findov = $this->comm->find("category_option_value",array("oid"=>$oid,"value"=>$v));
							if(!$findov){
								$this->comm->create("category_option_value",array("oid"=>$oid,"value"=>$v));
							}else{
								dump($option." exsit");
							}
					}
					if($i<3){
						$this->comm->update("category_option",array("catid"=>$findcat['catid'],"oid"=>$oid),array("required"=>1,"listorder"=>$i));
					}else{
						$this->comm->update("category_option",array("catid"=>$findcat['catid'],"oid"=>$oid),array("required"=>2,"listorder"=>$i));
					}
					$i++;
				}
			
		}
		
		/*
		foreach($mattrs as $k => $m){
			$catname = $k;
			dump($catname);
			$findcat = $this->comm->find("category",array("catname"=>$catname));
			dump($findcat);
			if(!$findcat){
				dump("catname is not exist");
				die();
			}else{
				if($findcat['parentid']!=0){
					dump("catname is not parent");
					die();
				}
				
			}
			
			
			
			foreach($m as $option => $value){
				$findoption = $this->comm->find("category_option",array("catid"=>$findcat['catid'],"name"=>$option));
				//dump($findoption);
				if($findoption){
					$findov = $this->comm->find("category_option_value",array("oid"=>$findoption['oid'],"value"=>$option));
					if(!$findov){
						$this->comm->create("category_option_value",array("oid"=>$findoption['oid'],"value"=>$option));
					}else{
						dump($option." exsit");
					}
				}
				
				
				$findoption = $this->comm->find("category_option",array("catid"=>$findcat['catid'],"name"=>$option));
				dump($findoption);
				
				if(!$findoption){
					dump("Create");
					die();
					//$this->comm->create("category_option")
				}else{
					if($findoption['required'] and !empty($findoption['value'])){
						dump("already option added");
						die();
					}else{
						dump("update");
						
						$values = implode("||",$value);
						$this->comm->update("category_option",array("catid"=>$findcat['catid'],"oid"=>$findoption['oid']),array("required"=>1,"value"=>$values));
					}
				}
				
			}
				
		}
		*/
		
		
		
		$option = array();
		foreach($mattrs[$catname] as $k => $m){
			if(count($option)<3){
				$option[]=$k;
			}
		}
		
		$this->load->helper("comb");
		global $combres;
		comb($option, 1);
		$one = $combres;
		comb($option, 2);
		comb($option, 3);
		
		//dump($combres);
		dump($one);
		$all = $combres;
		//dump($all);
		
		foreach($one as $k => $v){
			//$v = str_replace(",","",$v);
			foreach($mattrs[$catname][$v] as $value){
				$match_one[] = $value;
				$find = $this->comm->find("attrtag",array("tag"=>$value,"catid"=>$catid));
				if(!$find){
					$op = $this->comm->find("category_option",array("catid"=>$catid,"name"=>$v));
					$ov = $this->comm->find("category_option_value",array("value"=>$value,"oid"=>$op['oid']));
					$linkurl = $ov['oid']."-".$ov['id'];
					$this->comm->create("attrtag",array("tag"=>$value,"catid"=>$catid,"linkurl"=>$linkurl));
				}
			}
			unset($all[$k]);
		}
		dump($match_one);
		dump("===========================取一个".count($match_one)."=======================");
		dump($all);
		
		$total = 0;
		foreach($all as $k =>$v){
			$match_all = array();
			//$v = substr($v,1);
			$option = explode(",",$v);
			//dump($option);
			foreach($option as  $o){
				$match_all[] = $mattrs[$catname][$o];
				//$array[] = $mattrs[$catname][$o];
			}
			
			$result = combination($match_all);
			$total += count($result);
			
			
			foreach($result as $res){
				$linkurl = array();
				$tag = array();
				foreach($res as $j => $r){
					$op = $this->comm->find("category_option",array("catid"=>$catid,"name"=>$option[$j]));
					$ov = $this->comm->find("category_option_value",array("value"=>$r,"oid"=>$op['oid']));
					$linkurl[] = $ov['oid']."-".$ov['id'];
					$tag[] = $r;
				}
				
				$linkurl = implode("_",$linkurl);
				$tag = implode(" ",$tag);
				$find = $this->comm->find("attrtag",array("tag"=>$tag,"catid"=>$catid));
				if(!$find){
					$this->comm->create("attrtag",array("tag"=>$tag,"catid"=>$catid,"linkurl"=>$linkurl));
				}
				
			}
			
		}
		dump($total);
		
	}
	


	
	
}