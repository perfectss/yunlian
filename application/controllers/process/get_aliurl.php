<?php
class Get_aliurl extends CI_Controller{
	function index(){
			$this->load->model("comm_model","comm");
			$catid = $this->uri->rsegment(3,0);
			$page = $this->uri->rsegment(4,1);
			$ch= curl_init();
			$useragent="Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36";
			$header = array(
					"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
					"Connection: keep-alive",
					"Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3",
					"Host: www.alibaba.com"
				);
		
			
			$category = $this->comm->find("category",array("catid"=>$catid));
			if(!$category){
				die("category not exist");
			}
		
			
			$searchword = preg_replace("/\s{2,}/"," ",$category['catname']);
			$searchword = preg_replace("/[^0-9a-z]+/i"," ",$searchword);
			$searchword = str_ireplace(" ","_",$searchword);
			
			
			$pageurl = "http://www.alibaba.com/products/F0/".$searchword."/".$page.".html";
			dump($pageurl);
			$options = array(
					CURLOPT_URL => $pageurl,
					CURLOPT_TIMEOUT => 30,
					//CURLOPT_HTTPHEADER => $header,
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_USERAGENT => $useragent,
					CURLOPT_FOLLOWLOCATION => 1,
					CURLOPT_HEADER => 1,
					//CURLOPT_COOKIEFILE => $cookie,
					//CURLOPT_COOKIEJAR => $cookie,
					//CURLOPT_ENCODING => 'gzip,deflate',
					CURLOPT_AUTOREFERER => true
			);
			curl_setopt_array($ch, $options);
			$body = curl_exec($ch);
			$errno = curl_errno($ch);
			$err_message = curl_error($ch);
			curl_close($ch);
			if($errno){
				echo $err_message;
				die();
			}
			//dump($body);
			if(preg_match("/<div id=\"J-items-content\">/i",$body) != false){
				$areabody=$this->preg_substr1("/<div id=\"J-items-content\">/Uis","/<div id=\"J-recommend-supplier\".*><\/div>/Uis",$body);
				preg_match_all("/<span class=\"unverified\">Verified Supplier<\/span> \- <\/span><\s*a\s*.*href=\"(.*)\".*>.*<\s*\/a\s*>.*<\/div>/Uis",$areabody,$companysurl);
				preg_match_all("/<h2 class=\"title\"><\s*a\s*.*href=\"(.*)\".*>.*<\s*\/a\s*>.*<\/h2>/Uis",$areabody,$arrlinks);
				dump("matched");
			}else{
				if(preg_match("/<div class=\"view-label\">View <strong>/i",$body) != false){
					dump("matched11");
					$areabody=$this->preg_substr1("/<div class=\"view-label\">View <strong>/Uis","/<div class=\"ui2-pagination-show\">/Uis",$body);
				}else{
					dump("matched22");
					$areabody=$this->preg_substr1("/<div class=\"l-list-item-main-wrap\" id=\"J-l-list-item-main-wrap\">/Uis","/<div class=\"l-sub-main-wrap\">/Uis",$body);
				}
				preg_match_all("/<div class=\"stitle util-ellipsis\">.*<i class=\"ui2-icon ui2-icon-gs-year-num\d+\"><\/i><\/a>.*<\s*a\s*.*href=\"(.*)\".*>.*<\s*\/a\s*>.*<\/div>/Uis",$areabody,$companysurl);	
				preg_match_all("/<h2 class=\"title\">.*<\s*a\s*.*href=\"(.*)\".*>.*<\s*\/a\s*>.*<\/h2>/Uis",$areabody,$arrlinks);

			}
			
			//dump($arrlinks[1]);
			foreach($arrlinks[1] as $k => $pageurl){
				$pos = stripos($pageurl,".html");
				$pageurl = substr($pageurl,0,$pos+5);
				$aliid = $this->preg_substr("_","\.html",$pageurl);
				
				$urllink = substr($pageurl,strrpos($pageurl,"/"));
				$company = str_ireplace("company_profile.html#top-nav-bar","",$companysurl[1][$k]);
				$realurl = $company."product/".$aliid.$urllink;
				echo "<a href='".$realurl."'>".$catid."</a><b>".$realurl."</b>";
			}
		
			die();
			/*
			$links=$arrlinks[1];
			$totalurl = array();
			foreach($links as $k=>$alink){
				if(preg_match("/product\-detail/Uis",$alink)){
					if(empty($contentpageforbid)){
						$totalurl[]=$alink;
					}
				}
			}
			
			$totalurl=array_unique($totalurl);
			*/
			
			dump(count($totalurl));
			if(count($totalurl)==0){
				//log_message("error","{$page} geturl error");
				dump("error");
			}
			dump($totalurl);
			dump($areabody);
			
	}
	
	
	function makecat(){
		$this->load->model("comm_model","comm");
		//二级 select * from wl_category where parentid in(SELECT catid FROM `wl_category` WHERE parentid=0)
		//三级 SELECT * FROM `wl_category` where (`arrparentid` REGEXP '([0-9]+,){2}')=1
		//$cats = $this->comm->findAll("category","parentid=0","catid asc");
		//$cats = $this->db->query("SELECT * FROM `wl_category` where (`arrparentid` REGEXP '([0-9]+,){2}')=1")->result_array();
		$cats = $this->comm->findAll("category","catid in(3512,3513,3514,3515,3516,3517,3518,3519,3520,3521)");
		$content = "";
		foreach($cats as $k => $c){
			for($i=1;$i<=50;$i++){
				$content.= "http://www.motors-biz.com/process/get_aliurl/index/".$c['catid']."/".$i."\n";
			}

			$fp = fopen(FCPATH."cats/cats_{$k}.txt","wb");
			fwrite($fp,$content);
			fclose($fp);
			$content = "";
			
		}
		die("success");
	}
	
	function make(){
		die("error");
		$this->load->model("comm_model","comm");
		$cats = $this->comm->findAll("attrtag","","id asc");
		$content = "";
		foreach($cats as $k => $c){
			//$category = $this->comm->find("category",array("catid"=>$c['catid']));
			//$searchword = rawurlencode($c['tag']." ".$category['catname']);
			$searchword = rawurlencode($c['tag']);
			for($i=1;$i<=5;$i++){
				$content.= "http://www.motors-biz.com/process/get_aliurl/index/".$searchword."/".$c['catid']."/".$i."\n";
			}
			
			if($k!=0 and $k % 100 == 0){
				$fp = fopen(FCPATH."cats/cats_{$k}.txt","a+");
				fwrite($fp,$content);
				fclose($fp);
				$content = "";
			}
		}
		die("success");
	}
	
	function preg_substr1($pattern,$pattern1,$subject){
		preg_match($pattern,$subject,$arr,PREG_OFFSET_CAPTURE);
		$subject1=substr($subject,$arr[0][1]);
		preg_match($pattern1,$subject1,$arr1,PREG_OFFSET_CAPTURE);
		$content=substr($subject1,0,$arr1[0][1]);
		$content=preg_replace($pattern,"",$content);
		return $content;
	}
	
	function preg_substr($pattern,$pattern1,$subject){
		$re = preg_match("/".$pattern."(.*)".$pattern1."/isU",$subject,$arr);
		if($re){
			$content = $arr[1];
			return $content;
		}else{
			return false;
		}
		
	}
}