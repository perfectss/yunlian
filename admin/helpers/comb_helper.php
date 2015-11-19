<?php
function comb($arr, $len=0, $str="") {
		global $combres;
		$arr_len = count($arr);
		if($len == 0){
			$combres[] = $str;
		}else{
			for($i=0; $i<$arr_len-$len+1; $i++){
				$tmp = array_shift($arr);
				if(!empty($str)){
					$aa = $str.",".$tmp;
				}else{
					$aa = $tmp;
				}
				comb($arr, $len-1, $aa);
			}
		}
	}
	
	function combination($array){
        /*
		$array = array();
		$arguments = func_get_args();
        foreach($arguments as $argument){
            if(is_array($argument) === true){
                $array[] = $argument;
            }else{
                $array[] = array($argument);
            }
        }
		*/
        $size = count($array);

        if($size === 0){
            return array();
        }else if($size === 1){
            return is_array($array[0]) === true ? $array[0] : array();
        }else{
            $result = array();
            $a = $array[0];
            array_shift($array);
            if(is_array($array) === false){
                return $result;
            }

            foreach($a as $val){
                $b = call_user_func("combination", $array);
                foreach($b as $c){
                    if(is_array($c) === true){
                        $result[] = array_merge(array($val), $c);
                    }else{
                        $result[] = array($val, $c);
                    }
                }
            }
            return $result;
        }
    }