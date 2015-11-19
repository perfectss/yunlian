<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model('comm_model','comm');
        $this->load->helper('url');
        $this->load->library('session');
        $this->username = $this->session->userdata('username');
        $this->password = $this->session->userdata('password');
        if (!$this->username || !$this->password){
            redirect(site_url("reg_login/login"));
        } elseif (!$rs=$this->comm->findCount("member", array("username"=>$this->username,"password"=>$this->password))){
            redirect(site_url("reg_login/login"));
        }
    }
}