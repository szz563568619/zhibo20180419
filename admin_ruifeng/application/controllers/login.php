<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class login extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$refer = base_url();
		if($this->input->get('refer')) $refer = urldecode($this->input->get('refer'));
		$this->load->view('login', array('refer' => $refer));
	}

	function check_login()
	{
		$this->load->model('md_login');
		$name = $this->input->post('name');
		$password = $this->input->post('password');
		$result = $this->md_login->check_login($name, $password);
		if($result['status'])
		{
			$this->session->set_userdata('admin_login', 1);
			$this->session->set_userdata('id', $result['id']);
			$this->session->set_userdata('nick', $result['nick']);
			$this->session->set_userdata('permission', $result['permission']);
			$this->session->set_userdata('rid', $result['rid']);
			$this->load->database();
			$this->db->update('admin',array('login_status'=>1),array('id'=>$result['id']));//设置在线状态
		//前台状态判断
		$redis = $this->redis_conn();
		$redis->set('kefu_on_'.$result['id'], 1);
		}
		
		
		echo json_encode($result);
	}

	function logout()
	{
		$this->load->database();
        $this->db->update('admin',array('login_status'=>0),array('id'=>$this->session->userdata('id')));//设置离线状态
		
		//前台状态判断
		$redis = $this->redis_conn();
		$redis->set('kefu_on_'.$this->session->userdata('id'), 0);
		
		$this->session->sess_destroy();
		header("Location:".base_url()."login");
	}
	
	/*连接redis*/
	protected function redis_conn()
	{
		$redis = new Redis();
		$redis_conf = $this->config->item('redis');
		$redis->connect($redis_conf['host'], $redis_conf['port']);
		if($redis_conf['auth'] != '') $redis->auth($redis_conf['auth']);
		$redis->select($redis_conf['db']);
		return $redis;
	}
}