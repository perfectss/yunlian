<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('getstr')){
	function saddslashes($string) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = saddslashes($val);
			}
		} else {
			$string = addslashes($string);
		}
		return $string;
	}

	//取消HTML代码
	function shtmlspecialchars($string) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = shtmlspecialchars($val);
			}
		} else {
			$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
				str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
		}
		return $string;
	}
	
	function sstripslashes($string) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = sstripslashes($val);
			}
		} else {
			$string = stripslashes($string);
		}
		return $string;
	}
	/*
	$string 字符串
	$lenght 长度
	$in_slashes 传入的字符有slashes
	$in_slashes 输出字符有slashes
	$html 是否去掉html标签
	*/
	
	function getstr($string, $length, $in_slashes=0, $out_slashes=0, $html=0,$charset="utf-8") {

		$string = trim($string);

		if($in_slashes) {
			//传入的字符有slashes
			$string = sstripslashes($string);
		}
		if($html < 0) {
			//去掉html标签
			$string = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $string);
			$string = shtmlspecialchars($string);
		} elseif ($html == 0) {
			//转换html标签
			$string = shtmlspecialchars($string);
		}
		
		if($length && strlen($string) > $length) {
			//截断字符
			$wordscut = '';
			if(strtolower($charset) == 'utf-8') {
				//utf8编码
				$n = 0;
				$tn = 0;
				$noc = 0;
				while ($n < strlen($string)) {
					$t = ord($string[$n]);
					if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
						$tn = 1;
						$n++;
						$noc++;
					} elseif(194 <= $t && $t <= 223) {
						$tn = 2;
						$n += 2;
						$noc += 2;
					} elseif(224 <= $t && $t < 239) {
						$tn = 3;
						$n += 3;
						$noc += 2;
					} elseif(240 <= $t && $t <= 247) {
						$tn = 4;
						$n += 4;
						$noc += 2;
					} elseif(248 <= $t && $t <= 251) {
						$tn = 5;
						$n += 5;
						$noc += 2;
					} elseif($t == 252 || $t == 253) {
						$tn = 6;
						$n += 6;
						$noc += 2;
					} else {
						$n++;
					}
					if ($noc >= $length) {
						break;
					}
				}
				if ($noc > $length) {
					$n -= $tn;
				}
				$wordscut = substr($string, 0, $n);
			} else {
				for($i = 0; $i < $length - 1; $i++) {
					if(ord($string[$i]) > 127) {
						$wordscut .= $string[$i].$string[$i + 1];
						$i++;
					} else {
						$wordscut .= $string[$i];
					}
				}
			}
			$string = $wordscut;
		}
		
		if($out_slashes) {
			//$string = saddslashes($string);
		}
		return trim($string);
	}
}