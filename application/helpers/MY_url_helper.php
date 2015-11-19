<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('site_url'))
{
	 function site_url($uri = '',$domain = '')
	 {
	  if(function_exists('rewrite')){
		$uri=rewrite($uri);
	  }
	  $CI =& get_instance();
	  return $CI->config->site_url($uri);
	 }
}

if (!function_exists('rewrite'))
{
	 function rewrite($url){
	  $CI=&get_instance();
	  $CI->load->config('rewrite',TRUE);
	  $rewrite=$CI->config->item('rewrite');

	  ksort($rewrite['pattern']);
	  ksort($rewrite['replace']);

	  $url=preg_replace($rewrite['pattern'],$rewrite['replace'],$url,1);
	  return $url;
	 }
}
if (!function_exists('sell_url'))
{
	function sell_url($uri = ''){
	  $CI =& get_instance();
	  $CI->load->config("site",TRUE);
	  $site = $CI->config->item("site");
	  $thisurl = $CI->config->site_url();
	  $uri = str_replace($thisurl,$site['sell_domain'],$uri);
	  return $uri;
	}
}

if (!function_exists('company_url'))
{
	function company_url($uri = '',$replace){
	  $CI =& get_instance();
	  $CI->load->config("site",TRUE);
	  $site = $CI->config->item("site");
	  $thisurl = $CI->config->site_url();
	  $uri = str_replace($thisurl,"http://".$replace.".motors-biz.com/",$uri);
	  return $uri;
	}
}

if (!function_exists('main_url'))
{
	function main_url($uri = ''){
	  $CI =& get_instance();
	  $CI->load->config("site",TRUE);
	  $site = $CI->config->item("site");
	  $thisurl = $CI->config->site_url();
	  $uri = str_replace($thisurl,$site['main_domain'],$uri);
	  return $uri;
	}
}

