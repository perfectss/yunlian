<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pub extends MY_Controller{
	function __construct(){
		parent::__construct();
	}
		
	function left(){
		$data['site'] = $site = $this->config->item('site');
		$this->load->view('public/left',$data);
		
	}
	
	function hreder(){
        $this->load->view('header');
        $this->load->view('login/login', $data);
        $this->load->view('footer');
	}
		
	function get_ip(){
		$ip=$this->input->post('ip',TRUE);
		$rs=$this->db->query("select  *  from `wl_ip` where INET_ATON('{$ip}') between INET_ATON(startIp) and INET_ATON(endIp);");
		$rs=$rs->result_array();
		echo $msg=$rs[0]['Country']."(".$rs[0]['Local'].")";
	}
}