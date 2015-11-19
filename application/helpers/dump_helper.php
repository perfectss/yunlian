<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('dump'))
{
	function dump($vars){
		$content = "<div align=left><pre>\n" . htmlspecialchars(print_r($vars, true)) . "\n</pre></div>\n";
		echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body>{$content}</body></html>"; 
		return;
	}
}
/* End of file dump_helper.php */
/* Location: ./application/helpers/dump_helper.php */
