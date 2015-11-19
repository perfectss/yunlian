<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reg_login extends CI_Controller {
	function __construct(){
		parent::__construct();
        $this->load->helper('url');
		$this->load->library('form_validation');
		$this->load->model('comm_model','comm');
		$this->load->library('session');		
	}
	
	function login(){
		$data['title'] = "管理员登录";
		$data['username']=$data['password']=$msg='';
		$code=0;
		if ($_POST){
			$data['username'] = $member['username']=$username=strip_tags($this->input->post('username',TRUE));
			$data['password'] = $member['password'] = $password =md5(strip_tags($this->input->post('password',TRUE)));
			$fields="username,password,loginip,logintime,logintimes,admin,groupid,userid";
			$rs=$this->comm->find("member", $member,"",$fields);
			if ($rs){
				if ($rs['groupid']==1 and $rs['admin']==1){
					$code=1;
				}elseif ($rs['groupid']==5){
					$code=5;
				}else{
					$data['msg']="您无登录权限";
					$url=$this->load->view('login/login',$data,TRUE);
					echo $url;
					die();
				}
				$msg='登录成功';
                $data['username'] = $rs['username'];
                $this->session->set_userdata('userid',$rs['userid']);
				$this->session->set_userdata('username',$username);
				$this->session->set_userdata('password',$password);
			}elseif ($this->comm->findCount("member", array('username'=>$member['username']))){
				$msg='密码输入错误';
				$code=0;
			}else {
				$msg='用户名输入错误';
				$code=0;
			}
		}
        if($code == 0) {
            $data['msg'] = $msg;
            $data['code'] = $code;
            $this->load->view('header');
            $this->load->view('login/login', $data);
            $this->load->view('footer');
        }
        else
        {
            redirect(site_url('main/index'));
        }
	}
	
	function logout(){
		$array_session = array('username' => '', 'password' => '', 'hash_1' => '', 'hash_2' => '');
		$this->session->unset_userdata($array_session);
		redirect(site_url('reg_login/login'));
	}
}