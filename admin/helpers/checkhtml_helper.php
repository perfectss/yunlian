<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('checkhtml')){
	function checkhtml($html) {
		$html = stripslashes($html);
			preg_match_all("/\<([^\<]+)\>/is", $html, $ms);
			$searchs[] = '<';
			$replaces[] = '&lt;';
			$searchs[] = '>';
			$replaces[] = '&gt;';
			
			if($ms[1]) {
				$allowtags = 'img|font|div|table|tbody|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li';//允许的标签
				$ms[1] = array_unique($ms[1]);
				foreach ($ms[1] as $value) {
					$searchs[] = "&lt;".$value."&gt;";
					$value = shtmlspecialchars($value);
					$value = str_replace(array('\\','/*'), array('.','/.'), $value);
					$value = preg_replace(array("/(javascript|script|eval|behaviour|expression)/i", "/(\s+|&quot;|')on/i"), array('.', ' .'), $value);
					if(!preg_match("/^[\/|\s]?($allowtags)(\s+|$)/is", $value)) {
						$value = '';
					}
					$replaces[] = empty($value)?'':"<".str_replace('&quot;', '"', $value).">";
				}
			}
			$html = str_replace($searchs, $replaces, $html);
		//$html = addslashes($html);
		
		return $html;
	}
}