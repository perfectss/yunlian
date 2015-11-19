<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model('comm_model','comm');
        $this->load->library('encrypt');
        $username = $this->input->cookie('username', TRUE);
        $hash_1 = $this->input->cookie('hash_1', TRUE);
        $this->username = $this->encrypt->decode($username,$hash_1);
    }
}