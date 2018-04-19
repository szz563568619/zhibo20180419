<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class captcha extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		
	}

	function get_captcha()
	{
		$this->load->library('lib_captcha');
		$code = $this->lib_captcha->getCaptcha();
		$this->session->set_userdata('captcha', strtolower($code));
		$this->lib_captcha->showImg();
	}
}