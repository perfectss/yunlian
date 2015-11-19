<?php if(!defined('BASEPATH')) exit('NO direct script access allowed');
class Main extends MY_Controller{
	function __construct(){
		parent::__construct();
	}
    public function index(){
        header('Content-Language:en');
        $data['title'] = "51yunlian.com, foreign products, information, business integrated portal";
        $data['keywords'] = "51yunlian.com, foreign products, information, business";
        $data['description']="51yunlian.com, a comprehensive portal, hoping to help you understand the global product information, so as to choose the right products, and to find the quality of the business.";

        $this->load->view('header',$data);
        $this->load->view('main');
        $this->load->view('footer');
    }
}