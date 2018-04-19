<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class innerpeo extends MY_Controller {

	protected $redis;
	private $rid;
	function __construct()
	{
		parent::__construct();
		$this->rid = $this->input->post('rid');
		if(!$this->rid) $this->rid = $this->session->userdata('rid');
		if(!$this->rid) $this->rid = '001';
		parent::is_forbidden();
	}

	public function index()
	{
		show_404();
	}

	/*获取新加入的用户*/
	function get_innerpeo()
	{
		$this->redis = parent::redis_conn();
		//获取前11条数据//先查询出已经存在的用户
		$list = $this->redis->lgetrange("g_innnerpeo_list",0,12);
		echo json_encode($list);
	}
	
	function get_initpeo(){
		$this->load->database();
		$initpeo = $this->db->query("select initpeo from {$this->db->dbprefix('room')} where id='{$this->rid}'")->row()->initpeo;
		return $initpeo?$initpeo:8888;
	}

}