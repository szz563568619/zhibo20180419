<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class visitoripon extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->load->database();
		$this->out_data['visitoripons'] = $this->db->query("select id,ip,totaltime from {$this->db->dbprefix('visitoripon')}")->result_array();
		$redis = $this->redis_conn();
		$this->out_data['visitoripon_limittime_part'] = $redis->get('visitoripon_limittime_part');
		$this->out_data['visitoripon_limittime_all'] = $redis->get('visitoripon_limittime_all');
		$this->out_data['con_page'] = 'visitoripon_list';
		$this->load->view('default', $this->out_data);
	}
	
	/* 删除指定ip */
	function del_visitoripon()
	{
		$this->load->database();
		$id = (int)$this->input->post('id');
		$this->db->delete('visitoripon', array('id' => $id));
	}
	
	/* 设置游客ip最大在线时长 */
	function set_limittime()
	{
		$limittime_part = (int)$this->input->post('limittime_part');
		$limittime_all = (int)$this->input->post('limittime_all');
		$redis = $this->redis_conn();
		$redis->set('visitoripon_limittime_part',$limittime_part);
		$redis->set('visitoripon_limittime_all',$limittime_all);
	}

}