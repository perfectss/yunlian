<?php
class Readattrs extends CI_Controller{
	function index(){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit','14048M');
		$this->load->model("comm_model","comm");
		$catid = $this->uri->rsegment(3,0);
		$mattrs = array();
		$category = $this->comm->find("category",array("catid"=>$catid));
		if(!$category){
			dump($catid);
			die();
		}
		if($category['collect']){
			dump("already make");
			die();
		}
		$catid = $category['catid'];
		$catname = $category['catname'];
		
		$options = $this->comm->findAll("category_option",array("catid"=>$catid,"required"=>1),"listorder asc");
		if(!$options){
			dump($catid);
			//$this->comm->update("category",array("catid"=>$catid),array("collect"=>1));
			//$redirect_cat = $this->comm->find("category","parentid=0 and collect=0","catid asc");
			//dump($redirect_cat);
			//echo "<script>location.href='/process/readattrs/index/".$redirect_cat['catid']."'</script>";
			die("not attrs");
		}
		
		
		foreach($options as $op){
			$ov = $this->comm->findAll("category_option_value",array("oid"=>$op['oid']));
			foreach($ov as $v){
				$mattrs[$category['catname']][$op['name']][] = $v['value'];
			}
			
		}
		
		dump($mattrs);
		
		$option = array();
		foreach($mattrs[$catname] as $k => $m){
			//if(count($option)<3){
				$option[]=$k;
			//}
		}
		
		$this->load->helper("comb");
		global $combres;
		if(count($option)>4){
			$combnum = 4;
		}else{
			$combnum = count($option);
		}
		for($i=1;$i<=$combnum;$i++){
			comb($option, $i);
			if($i==1){
				$one = $combres;
			}
		}
		
		
		//dump($combres);
		//dump($one);
		
		$all = $combres;
		//dump($all);
		//die();
		
		foreach($one as $k => $v){
			//$v = str_replace(",","",$v);
			foreach($mattrs[$catname][$v] as $value){
				$match_one[] = $value;
				$find = $this->comm->find("attrtag",array("tag"=>$value,"catid"=>$catid));
				if(!$find){
					$op = $this->comm->find("category_option",array("catid"=>$catid,"name"=>$v));
					$ov = $this->comm->find("category_option_value",array("value"=>$value,"oid"=>$op['oid']));
					$linkurl = $ov['oid']."-".$ov['id'];
					$attrs = $ov['oid']."|".$ov['id'];
					$this->comm->create("attrtag",array("tag"=>$value,"catid"=>$catid,"catname"=>$catname,"attrs"=>$attrs,"linkurl"=>$linkurl,"num"=>1));
				}
			}
			unset($all[$k]);
		}
		dump($match_one);
		dump("===========================取一个".count($match_one)."=======================");
		//dump($all);
		
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
				$attrs = array();
				$tag = array();
				foreach($res as $j => $r){
					$op = $this->comm->find("category_option",array("catid"=>$catid,"name"=>$option[$j]));
					$ov = $this->comm->find("category_option_value",array("value"=>$r,"oid"=>$op['oid']));
					$linkurl[] = $ov['oid']."-".$ov['id'];
					$attrs[] =  $ov['oid']."|".$ov['id'];
					$tag[] = $r;
					
				}
				$num = count($attrs);
				$linkurl = implode("_",$linkurl);
				$attrs = implode(",",$attrs);
				$tag = implode(" ",$tag);
				$this->db->query("lock tables wl_attrtag write");
				$find = $this->comm->find("attrtag",array("tag"=>$tag,"catid"=>$catid));
				
				if(!$find){
					$this->comm->create("attrtag",array("tag"=>$tag,"catid"=>$catid,"catname"=>$catname,"attrs"=>$attrs,"linkurl"=>$linkurl,"num"=>$num));
								
				}
				$this->db->query("unlock tables");	
			}
			//释放内存
			unset($result);
			
		}
		dump($total);
		$this->comm->update("category",array("catid"=>$catid),array("collect"=>1));
		//$redirect_cat = $this->comm->find("category","parentid=207 and collect=0","listorder ASC");
		//dump($redirect_cat);
		//echo "<script>location.href='/process/readattrs/index/".$redirect_cat['catid']."'</script>";
		
	}
	


	
	
}