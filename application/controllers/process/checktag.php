<?php
class Checktag extends CI_Controller{
	function index(){
		$this->load->model("comm_model","comm");
		$this->load->library('Sphinxclient','','sphinx');
        $this->sphinx->SetServer ('127.0.0.1', 9312);
        $this->sphinx->SetConnectTimeout(1);
        $this->sphinx->SetArrayResult(true);
		$this->sphinx->SetMatchMode(SPH_MATCH_PHRASE);
		
		$page = $this->uri->rsegment(3,0);
		
		
		$tagindex = $this->comm->findAll("tagindex","","id desc","","{$page},1000");
		foreach($tagindex as $tag){
			//$tag['tag'] = "Stepper Motors";
			dump($tag['tag']);
			$strcount = count(explode(" ",$tag['tag']));
			$this->sphinx->ResetFilters();
			$this->sphinx->SetSortMode(SPH_SORT_EXTENDED,"@id asc");
			$this->sphinx->SetFilter("catname_len",array($strcount));
			$res = $this->sphinx->Query($tag['tag'],'category');
			//dump($res);
			if(!empty($res['matches'])){
				echo("<font color=red>Matched Category</font><br/>");
				$category = $this->comm->find("category",array("catid"=>$res['matches'][0]['id']));
				if(!$category['parentid']){
					$siteurl = "catalog/index/".$category['catid']."/".$category['linkurl'];
				}else{
					$siteurl = "sell_list/index/catid_".$category['catid']."/".$category['linkurl'];
				}
				
				dump($siteurl);
			}else{
				
				$this->sphinx->ResetFilters();
				$this->sphinx->SetSortMode(SPH_SORT_EXTENDED,"tag_len asc");
				$res1 = $this->sphinx->Query($tag['tag'],'motors_attrtag');
				//dump($res1);
				if(!empty($res1['matches'])){
					echo("<font color=blue>Matched AttrsTag</font><br/>");
					$attrtag = $this->comm->find("attrtag",array("id"=>$res1['matches'][0]['id']));
					$attrtag['title'] = $attrtag['tag']." ".$attrtag['catname'];
					$attrtag['titlelinkurl']  = preg_replace("/[^0-9a-z]+/i","-",$attrtag['title']);
					$siteurl = "attr_list/index/".$attrtag['catid']."/".$attrtag['linkurl']."/".$attrtag['titlelinkurl'];
					if(count(explode(" ",$attrtag['title'])) - $strcount > 1){
						$siteurl = "slist/index/".$tag['id']."/".$tag['linkurl'];
					}
					dump($siteurl);
				}else{
					dump("Not Matched");
					$siteurl = "slist/index/".$tag['id']."/".$tag['linkurl'];
					dump($siteurl);
				}
			}
			$this->comm->update("tagindex",array("id"=>$tag['id']),array("siteurl"=>$siteurl));
			//die();
		}
		$page = $page + 1000;
		if($page<14232){
			echo "<script>location.href='/index.php/process/checktag/index/".$page."'</script>";
		}
	}
}