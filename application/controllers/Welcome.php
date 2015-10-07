<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

function __construct(){
	parent::__construct();
	$this->load->model('timesheet_model','timesheet');
}
	public function index()
	{
		$entries = $this->timesheet->get_for_user('1',array('start_time'=>'15:00','end_time'=>'21:00','day'=>'2015-10-06'));
		$data['entries'] = $entries;
		$data['target'] = "welcome_message";
		$data['title'] = "Hello";
		$this->load->view('page/index',$data);
	}
}
