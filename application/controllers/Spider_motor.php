<?php
class Spider_motor extends CI_Controller{
	function index(){
		ini_set('max_execution_time', 300);
		$useragent = "Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1";
		$timestamp=time();
		$this->load->model("comm_model","comm");
		$this->load->model("category_model","category");
		$this->load->helper("getstr");

        $upload_path= FCPATH."skin/images";

		$title=getstr($this->input->post("title"),255,1,1,1);
		if(empty($title)){
			echo "failed";
			log_message('error',"Post title empty");
			exit;
		}

		$sexword=array("Vibrator","Pink Leopard","Stimulator","G-Spot","california exotics","sex","sexual","sexy","Circumcision","Stimulation","Penis","vibe","Clitoral"
,"Penis Enlarger","Vaginal","Adult Toys","Personal Massager","Pink Lady","cook ring","vagina","Cigarette","condom","vibrator","cock","personal Lubricant"
,"Toy-G","urethral","Vibrating Ring","masturbation","masturbators","Virgin","vibrators","G spot","Vibrating Wand","cigar","anal","vibrating ball","Fat Ring","bullet","wet towel","Love Lounger"
,"Nandrolone phenylpropionate","Climax","dildo","Women massaging","Artificial Pussy","Silicone Finger Ring","Fresh pussy","Gynecological Hydrogel","delay spray","Delay wet tissue","Male Enhancement"
,"Exercise Balls","Classic Double Balls","Geisha","Pussy","Premature Ejaculation","Double Dong","OTO tablets","Princess doll","Fleshlight","Massaging Wand","Roman emperor","NITERIDER","love doll"
,"contraceptive","spermicide","sperm","Black Ant","beads Pulse","Rabbits Rings","Rabbits Ring","Love Making","Make Love","love ball","Power Love","Pornography","marijuana","drug","breast","masturbator","Original","inflatable doll","Kinekt","nipple cover","nipple tape");
		foreach($sexword as $sex){
			if(stripos(strtolower($title),$sex)!==false){
				echo "标题Sexy过滤";
				//spClass('spLog')->info("Post title has sex:".$title);
				exit;
			}
		}

		//内容
		$content=getstr($this->input->post("sell_content"),0,1,1,1);
		$introduce=substr($content,0,255);



		//分类名->type
		//$type=$this->input->post("type");

		/*
		$keyword=$this->input->post("sell_keyword");

		$sell_keyword=explode("</a>",$keyword);
		array_pop($sell_keyword);
		foreach($sell_keyword as $zd){
			$sell_kws[]=trim($zd);
		}
		$keyword=join($sell_kws,",");
		if(strlen($keyword)>255){
			$keyword=substr($keyword,0,255);
		}

		if(empty($keyword)){
			$keyword = $type ? $type : '';
		}
		*/
		$keyword = '';

		//公司
		$company=getstr($this->input->post("company"),150,1,1,1);
		if(empty($company)){
			echo "failed";
			die();
		}
		//最小起订量
		$minamount=getstr($this->input->post("minamount"),100,1,1);
		if(!preg_match("/\d+/i",$minamount,$newmin)){
			$minamount=1;
		}else{
			$minamount=floatval($newmin[0]);
		}


		//供应能力
		$amount=getstr($this->input->post("amount"),100,1,1);
		if(!preg_match("/\d+/i",$amount,$newamount)){
			$amount=0;
		}else{
			$amount=floatval($newamount[0]);
		}


		//发货时间
		$days=getstr($this->input->post("days"),100,1,1);
		if(!preg_match("/\d+/i",$days,$newdays)){
			$days=0;
		}else{
			$days=floatval($newdays[0]);
		}

		//品牌
		$brand=getstr($this->input->post("brand"),100,1,1);

		//型号
		$model=getstr($this->input->post("model"),100,1,1);

		//图片
		$thumb = $ali_imgurl = $this->input->post("sell_thumb");


		//产品属性
		$tmp_sku='';
		$sku=getstr($this->input->post("sku"),0,1,1);
		$sku=htmlspecialchars_decode($sku);
		$option=array();
		$option_value=array();
		if(preg_match_all("/<span class=\"attr-name\".*>(.*)<\/span>.*<span class=\"attr-value\".*>(.*)<\/span>/isU",$sku,$newsku)){
			foreach($newsku[1] as $s){
				$s = str_ireplace(":","",$s);
				$option[]=substr(trim($s),0,50);
			}
			foreach($newsku[2] as $s){
				$option_value[]=substr(strip_tags(trim($s)),0,255);
			}
		}


		/*
		$newsku=explode("</td>",$sku);
		$newsku=array_chunk($newsku,2);
		if(count($newsku[count($newsku)-1]) < 2){
			array_pop($newsku);
		}


		$option=array();
		$option_value=array();
		foreach($newsku as $s){
			if($s){
				$temp=strip_tags(ucwords(strtolower(trim($s[0]))));
				$option[]=substr(trim(strtr($temp,':',' ')),0,50);
				$option_value[]=substr(strip_tags(ucwords(strtolower(trim($s[1])))),0,255);
			}
		}

		*/

		//公司类型
		$mode=getstr($this->input->post("mode"),100,1,1);
		//价格
		$price=getstr($this->input->post("price"),50,1,1);
		if(strpos($price,"US")===false){
			$minprice=0;
			$maxprice=0;
			$unit='';
			$currency='';
		}else{
			$tmp_unit=explode("/",$price);
			$unit=trim($tmp_unit[1]);
			$tmp_price=$tmp_unit[0];
			$tmp_price=str_replace(array("$","US"),"",$tmp_price);

			if(strpos($tmp_price,"-")===false){
				$minprice=floatval($tmp_price);
				$maxprice=floatval($tmp_price);
			}else{
				$tmp_p=explode("-",$tmp_price);
				$minprice=floatval($tmp_p[0]);
				$maxprice=floatval($tmp_p[1]);
			}
			$currency="US";
		}

		//单位
		//$unit=strtolower(getstr($this->input->post("unit"),30,1,1,1));

		//区域
		$areaname=getstr($this->input->post("area"),30,1,1);
		if(empty($areaname)){
			$areaname="China";
		}
		//$areaname=explode(",",$areaname);
		//$areaname=trim(array_pop($areaname));

		$areaname=ucfirst(strtolower(trim($areaname)));
		$areas=$this->comm->findAll("area");
		foreach($areas as $f){
			if(stripos($areaname,$f['areaname'])!==false){
				$areaid=$f['areaid'];
				break;
			}
		}
		if(!isset($areaid)){
			$this->db->insert("area",array("areaname"=>$areaname,"arrchildid"=>''));
			$areaid = $this->db->insert_id();
		}


		//公司国家
		$com_country=getstr($this->input->post("com_country"),30,1,1);
		if(empty($com_country)){
			$com_country="China";
		}
		$com_country=strtolower(trim($com_country));
		if($com_country=="china (mainland)"){
			$com_country = "China";
		}
		$com_country = ucfirst($com_country);
		foreach($areas as $df){
			if(stripos($com_country,$df['areaname'])!==false){
				$com_areaid=$df['areaid'];
				break;
			}
		}
		if(!isset($com_areaid)){
			$this->db->insert("area",array("areaname"=>$com_country,"arrchildid"=>''));
			$com_areaid = $this->db->insert_id();
		}

		/*$findcomarea=$this->comm->find("area",array("areaname"=>$com_country));
		if($findcomarea){
			$com_areaid=$findcomarea['areaid'];
		}else{
			$this->db->insert("area",array("areaname"=>$com_country,"arrchildid"=>''));
			$com_areaid = $this->db->insert_id();
		}*/

		//公司地址
		$address=getstr($this->input->post("address"),255,1,1);

		//省份
		$regcity=getstr($this->input->post("regcity"),30,1,1);

		//联系人
		$truename=getstr($this->input->post("truename"),30,1,1);
		$gender=explode(" ",$truename);
		$gender=trim($gender[0]);
		if($gender=='Ms.' || $gender=='ms.'){
			$gender=1;
		}else{
			$gender=0;
		}


		//联系电话
		$telephone=getstr($this->input->post("telephone"),50,1,1);

		//公司邮编
		$zipcode=getstr($this->input->post("zip"),20,1,1);

		//手机
		$mobile=getstr($this->input->post("mobile"),50,1,1);

		//传真
		$fax=getstr($this->input->post("fax"),50,1,1);

		//主营产品
		$business=getstr($this->input->post("business"),255,1,1);


		//员工人数
		$size=getstr($this->input->post("employees"),100,1,1);

		//成立年份
		$regyear=getstr($this->input->post("regyear"),4,1,1);

		//主要市场
		$markets=getstr($this->input->post("markets"),255,1,1);

		//年销售额
		$volume=getstr($this->input->post("revenue"),100,1,1);

		//出口百分比
		$export=getstr($this->input->post("export"),100,1,1);

		//管理体系认证
		//$icp=getstr($this->input->post("icp"),100,1,1);

		//注册号
		$regno=getstr($this->input->post("regno"),100,1,1);

		//发证机关
		$authority=getstr($this->input->post("authority"),100,1,1);

		//注册资本
		//$capital=getstr($this->input->post("capital"),30,1,1);
		//$capital=trim(str_ireplace(array("RMB","US",","),"",$capital));
		//$capital=floatval($capital);

		//公司图片
		$company_thumb = $this->input->post("com_thumb");
		$company_thumb = "http://i01.i.aliimg.com/img/company/".$company_thumb;

		//公司主页
		$homepage = getstr($this->input->post("homepage"),255,1,1);

		/*
		//提取分类
		$allcat=htmlspecialchars_decode($this->input->post("catname"));
		$arraycat=explode(">",$allcat);
		$catname=array_pop($arraycat);
		*/
		$catid = $this->input->post("catid");

		$check_title=$title.$company;
		$cmd5=md5($check_title);
		$findsell=$this->comm->find("check_sell",array("cmd5"=>$cmd5));

		//港口
		$port = getstr($this->input->post("port"),50,1,1);
		//港口
		$payment = getstr($this->input->post("payment"),100,1,1);
		$payment = str_ireplace("MoneyGram","",$payment);
		$payment = str_ireplace(",,",",",$payment);

		//url
		$localurl=$this->input->post("com_url");

		if($findsell){
			echo "产品已经存在";
			die();
		}


		//其他参数赋值
		$tmpname='';
		$status=3;
		$linkurl=preg_replace("/[^a-zA-Z0-9]+/","-",$title);
		$username=preg_replace("/[^a-zA-Z0-9]+/"," ",$company);
		foreach(explode(" ",$username) as $v){
			$tmpname.=strtolower(substr($v,0,1));
		}
		$username=$tmpname;
		if(strlen($username)>27){
			$username=substr($username,0,27);
		}

		$username=$username.mt_rand(0,9999);
		$password="b2bcaiji@test.com";
		$md5password=md5($password);
		$email=$username."@test.com";
		//部门
		$department=getstr($this->input->post("department"),30,1,1);
		//职位
		$career=getstr($this->input->post("career"),30,1,1);

		$regtime=$timestamp;
		$regip=$_SERVER["REMOTE_ADDR"];

		$findcompany=$this->comm->find("company",array("company"=>$company));
		if(!$findcompany){
			if(empty($company_thumb)){
				$company_thumb='/skin/img/logo.png';
			}else{
				$company_thumb=str_replace($upload_path,"",$this->img_download($company_thumb,$upload_path."/company/",0));
			}
			$company_content=$this->input->post("com_content");
			$company_introduce=$this->input->post("com_introduce");
			if(!$company_introduce){
				$company_introduce=getstr($company_content,0,1,1,-1);
				$company_introduce=substr($company_introduce,0,200);
			}
			$company_content=getstr($company_content,0,1,1,1);

			if(empty($company_introduce) || empty($company_content)){
				$company_introduce=$company;
				$company_content=$company;
			}

			if(empty($regyear)){
				$regyear=1990;
			}

			$member_record=array(
				"username"=>$username,
				"company"=>$company,
				"passport"=>$username,
				"password"=>$md5password,
				"payword"=>$md5password,
				"email"=>$email,
				"gender"=>$gender,
				"truename"=>$truename,
				"mobile"=>$mobile,
				"department"=>$department,
				"career"=>$career,
				"groupid"=>6,
				"regid"=>6,
				"areaid"=>$com_areaid,
				"edittime"=>$timestamp,
				"regip"=>$regip,
				"regtime"=>$regtime,
				"vmail"=>1,
				"cj"=>1
			);
			$this->db->insert("member",$member_record);
			$userid=$this->db->insert_id();

			if($userid){
				$company_record=array(
					"userid"=>$userid,
					"username"=>$username,
					"groupid"=>6,
					"company"=>$company,
					"areaid"=>$com_areaid,
					"mode"=>$mode,
					"regyear"=>$regyear,
					"regcity"=>$regcity,
					"business"=>$business,
					"telephone"=>$telephone,
					"fax"=>$fax,
					"mail"=>$email,
					"address"=>$address,
					"zipcode"=>$zipcode,
					"homepage"=>$homepage,
					"introduce"=>$company_introduce,
					"size"=>$size,
					"markets"=>$markets,
					"volume"=>$volume,
					"export"=>$export,
					"regno"=>$regno,
					"authority"=>$authority,
					"thumb"=>$company_thumb
				);
				$this->db->insert("company",$company_record);
				$this->db->insert("company_data",array("userid"=>$userid,"content"=>$company_content));

			}

		}else{
			$username=$findcompany['username'];
			$userid=$findcompany['userid'];
		}

		/*$find_comurl=$this->comm->find("comurl",array("userid"=>$userid));
		if(!$find_comurl){
			$this->db->insert("comurl",array("userid"=>$userid,"company"=>$company,"url"=>$localurl));
		}*/

		if(!$findsell){
			if(empty($thumb)){
				$thumb='/skin/img/logo.png';
			}else{
				$thumb=str_replace($upload_path,"",$this->img_download($thumb,$upload_path."/sell/",1));
			}

			$pageurl = $ali_url =$this->input->post("url");
			$aliid = substr($pageurl,0,strrpos($pageurl,"/"));

			$com_catname = getstr($this->input->post("catname"),100);


			//$companycatname=trim($arr[count($arr)-2]);
			if(!empty($com_catname)){
				if(strpos($com_catname,"&amp;gt;")!==false){
					$array_catname = explode("&amp;gt;",$com_catname);
					$companycatname = $array_catname[0];
				}else{
					$companycatname = $com_catname;
				}


				$findtype=$this->comm->find("type",array("tname"=>$companycatname,"userid"=>$userid));
				if(!$findtype){
					$this->db->insert("type",array("tname"=>$companycatname,"userid"=>$userid));
					$mycatid=$this->db->insert_id();
				}else{
					$mycatid=$findtype['tid'];
				}
			}else{
				$mycatid=0;
			}


			if(!$catid){
				echo "the catid was not failed";
				die();
			}

			$newrecord=array(
				"catid"=>$catid,
				"mycatid"=>$mycatid,
				"areaid"=>$areaid,
				"level"=>1,
				"elite"=>1,
				"title"=>$title,
				"subtitle"=>'',
				"introduce"=>$introduce,
				"model"=>$model,
				"brand"=>$brand,
				"unit"=>$unit,
				"minprice"=>$minprice,
				"maxprice"=>$maxprice,
				"currency"=>$currency,
				"minamount"=>$minamount,
				"amount"=>$amount,
				"days"=>$days,
				"keyword"=>$keyword,
				"thumb"=>$thumb,
				"username"=>$username,
				"userid"=>$userid,
				"groupid"=>6,
				"pptword"=>'',
				"company"=>$company,
				"truename"=>$truename,
				"telephone"=>$telephone,
				"mobile"=>$mobile,
				"address"=>$address,
				"email"=>$email,
				"addtime"=>$timestamp,
				"edittime"=>$timestamp,
				"adddate"=>date("Y-m-d",$timestamp),
				"editdate"=>date("Y-m-d",$timestamp),
				"status"=>$status,
				"linkurl"=>$linkurl,
				"aliid"=>$aliid,
				"port"=>$port,
				"payment"=>$payment,
				"cj"=>1
			);
			$this->db->insert("sell",$newrecord);
			$itemid=$this->db->insert_id();

			if($itemid){
				$ali_url = substr($ali_url,0,255);
				$ali_imgurl = substr($ali_imgurl,0,255);
				$this->db->insert("sell_data",array("itemid"=>$itemid,"content"=>$content,"ali_url"=>$ali_url,"ali_imgurl"=>$ali_imgurl));
				$parentids = $this->comm->find("category",array("catid"=>$catid));
				$parentids = $parentids['arrparentid'].",".$catid;
				$parentids = explode(",",$parentids);

				foreach($parentids as $df){
					$this->db->set("item","item+1",FALSE);
					$this->db->where("catid",$df);
					$this->db->update("category");
				}

				foreach($option as $k => $o){
					$findoption=$this->comm->find("category_option",array("name"=>$o,"catid"=>$catid));
					if(!$findoption){
						$this->db->insert("category_option",array("name"=>$o,"catid"=>$catid));
						$oid = $this->db->insert_id();
					}else{
						$oid = $findoption['oid'];
					}
					$this->db->insert("category_value",array("itemid"=>$itemid,"oid"=>$oid,"value"=>$option_value[$k],"catid"=>$catid));
					$value_id = $this->db->insert_id();

					$did=0;
					$length=strlen($option_value[$k]);
					if($length>=3 && $length<=30){
						$rs_0=$this->comm->find("category_default_option",array("value"=>$option_value[$k]));

						$rs=$this->comm->find("category_default_option",array("value"=>$option_value[$k],"catid"=>$catid,"oid"=>$oid));

						if($rs_0 && $rs){
							//$default_attr=array("id"=>$rs['id'],"catid"=>$catid);
							$this->db->set("num","num+1",FALSE);
							$this->db->where(array("id"=>$rs['id'],"oid"=>$oid));
							$this->db->update("category_default_option");
							$did=$rs['id'];
						}elseif($rs_0){
							$my_id=$rs_0['id'];
							$default_attr=array("id"=>$my_id,"catid"=>$catid);
							$did=$my_id;
							$default_attr['value']=$option_value[$k];
							$default_attr['oid']=$oid;
							$default_attr['num']=1;
							$this->comm->create("category_default_option",$default_attr);
						}else{
							$maxid=$this->comm->find("category_default_option","","","max(id)");
							$maxid=$maxid ? $maxid['max(id)'] : 0;
							$maxid++;
							$default_attr=array("id"=>$maxid,"catid"=>$catid);
							$did=$maxid;

							$default_attr['value']=$option_value[$k];
							$default_attr['catid']=$catid;
							$default_attr['oid']=$oid;
							$default_attr['num']=1;
							$this->comm->create("category_default_option",$default_attr);
						}

						/*$de_temp=$this->comm->findCount("category_default_option",$default_attr);
						$default_attr['value']=$option_value[$k];
						$default_attr['oid']=$oid;
						$default_attr['num']=1;
						if(!$de_temp){
							$this->comm->create("category_default_option",$default_attr);
						}*/
					}

					$this->db->update("category_value",array("did"=>$did),array("id"=>$value_id));

					$this->db->set("item","item+1",FALSE);
					$this->db->where("oid",$oid);
					$this->db->update("category_option");

				}

				$option_values=$this->comm->findAll("category_value",array("itemid"=>$itemid),"oid asc");
				$tmp_op=array();
				foreach($option_values as $v){
					$tmp_op[]=$v['oid'];
				}
				$option_values=implode(",",$tmp_op);
				$subtitle = $this->subtitle($itemid);
				$linkurl = preg_replace("/[^a-zA-Z0-9]+/","-",$subtitle);
				$this->db->update("sell",array("pptword"=>$option_values,"subtitle"=>$subtitle,"linkurl"=>$linkurl),array("itemid"=>$itemid));

				$this->db->insert("check_sell",array("cmd5"=>$cmd5,"sid"=>$itemid));

				echo "success";
			}else{
				echo "failed";
			}


		}

	}

	function img_download($url,$path="./file/upload/",$imgwater=0){
		$curl = curl_init($url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		$imageData = curl_exec($curl);
		$errno = curl_errno($curl);
		$err_message = curl_error($curl);
		curl_close($curl);
		$name=explode("/",$url);
		$datetmp=date("Ymd");
		if($errno){
			log_message('error', $err_message);
			return '/skin/img/logo.png';
		}
		$filename=$path.$datetmp."/".preg_replace("/[^a-zA-Z0-9_\-\.\!]/","",array_pop($name));
		if(!file_exists($path.$datetmp."/")){
			mkdir($path.$datetmp."/",0777,true);
		}
		$tp = @fopen($filename,'a');
		fwrite($tp, $imageData);
		fclose($tp);
		$path=array(
				'img_path'=>$filename,	//原图片所在目录
				'logo_path'=>FCPATH."skin/images/pic_f.png"	//原始logo路径
			);
		return $imgwater ? $this->img_watermark($path) : $filename;
	}

	function preg_substr($pattern,$pattern1,$subject){
		preg_match($pattern,$subject,$arr,PREG_OFFSET_CAPTURE);
		$subject1=substr($subject,$arr[0][1]+strlen($arr[0][0]));
		if(empty($arr[0][1])){
			return '';
		}
		preg_match($pattern1,$subject1,$arr1,PREG_OFFSET_CAPTURE);
		$content=substr($subject1,0,$arr1[0][1]);
		$content=preg_replace($pattern,"",$content);
		return $content;
	}

	function add_spider_url(){
		ini_set('max_execution_time', '0');
		$this->load->model("comm_model","comm");
		$rs=$this->comm->findAll("category",array("child"=>0,"collect"=>0),"catid asc","catid,catname","0,10");
		//1676
		//dump(count($rs));
		$str="";
		$c=$j=0;
		$total=100;
		foreach ($rs as $v){
			$temp='';
			$temp=strtolower($v['catname']);
			if (strstr($temp,"motor")){
				$j++;
				$temp=explode(" ",$temp);
				$temp=join("_",$temp);
				for ($i=1;$i<=$total;$i++){
					//http://www.alibaba.com/products/F0/dc_motors/5.html
					$str.="http://www.alibaba.com/products/F0/".$temp."/".$i.".html\r\n";
					$c++;
				}
				$this->comm->update("category",array("catid"=>$v['catid']),array("collect"=>-1));
			}
		}

		file_put_contents(FCPATH."wood/motor_spider.txt", $str);
		if($c==$j*$total){
			echo "success";
		}
	}

	function img_watermark($path){
		$this->load->library('image_lib');
		$config['source_image'] = $path['img_path'];
		$config['wm_type'] = 'overlay';
		$config['wm_overlay_path'] = $path['logo_path'];
		$config['quality'] = 90;
		$config['wm_opacity'] = 100;
		$config['wm_vrt_alignment'] = 'middle';
		$config['wm_hor_alignment'] = 'left';
		$config['wm_vrt_offset'] = '-10';
		$this->image_lib->initialize($config);
		$this->image_lib->watermark();
		return $path['img_path'];
	}

	function subtitle($itemid){
		$this->load->model("comm_model","comm");
		$this->load->model("category_option_model","category_option");
		$findsell = $this->comm->find("sell",array("itemid"=>$itemid));
		if(!$findsell){
			return "";
		}



		$category = $this->comm->find("category",array("catid"=>$findsell['catid']));
		$catname = $category['catname'];
		$attr = $this->category_option->getSellOption($itemid);
		if(!$attr){
			return $catname;
		}

		$parentCategory = $this->comm->find("category",array("catid"=>$category['parentid']));
		if(stripos($parentCategory['catname'],"Parts")!==false){
			return $catname;
		}

		$tmpoptions = $this->comm->findAll("category_option",array("catid"=>$findsell['catid'],"required"=>1),"listorder asc","","1,4");
		$options = array();
		foreach($tmpoptions as $v){
			$options[] = $v['name'];
		}
		$attr1 = $this->serviceSellCustomOption($attr,$options);

		foreach ($attr as $kk => $vv) {
			//Type
			if (!$attr1['Type']) {
				$attr1['Type'] =  stristr($vv['name'], 'Type') ? $vv['value'] : '';
			}

		}
		$type = $attr1['Type'];
		unset($attr1['Type']);
		$attrTitle = '';
		foreach($attr1 as $av){
			$attrTitle .= $av." ";
		}

		if($type){
            $attrTitle.=" ".$type." (".$catname.") ";
        }else{
            $attrTitle .=" ".$catname." ";
        }

		if(strlen($attrTitle)>255){
			$attrTitle = substr($attrTitle,0,255);
		}

		return  $attrTitle;
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
}