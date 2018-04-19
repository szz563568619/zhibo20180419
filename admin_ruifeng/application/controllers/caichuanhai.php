<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class caichuanhai extends CI_Controller {

	private $redis = null;
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function index()
	{
		$redis = self::_redis_conn();
		$room_list = $this->_get_room_list();
		foreach($room_list as $v)
		{
			$redis->zremrangebyrank("room_{$v['id']}", 0, -1);
			$redis->zremrangebyrank("innerpeo_{$v['id']}", 0, -1);
		}


		$yesterday = date("Y-m-d",strtotime('-1 day'));
		$this->db->query("delete from {$this->db->dbprefix('chat_list')} where date_format(time,'%Y-%m-%d')<='{$yesterday}'");

		$day5ago = date("Y-m-d",strtotime('-5 day')); /*5天前*/
		$this->db->query("delete from {$this->db->dbprefix('visitor')} where date_format(time,'%Y-%m-%d')<='{$day5ago}'");
		$this->db->query("delete from {$this->db->dbprefix('visitor_chat')} where date_format(time,'%Y-%m-%d')<='{$day5ago}'");
		$this->db->query("delete from {$this->db->dbprefix('visitoripon')} where totaltime = 0");
	}

	private function _get_room_list()
	{
		return $this->db->query("select id,name from {$this->db->dbprefix('room')}")->result_array();
	}

	/*连接redis*/
	private function _redis_conn()
	{
		if($this->redis != null) return $this->redis;

		$this->redis = new Redis();
		$redis_conf = $this->config->item('redis');
		$this->redis->connect($redis_conf['host'], $redis_conf['port']);
		if($redis_conf['auth'] != '') $this->redis->auth($redis_conf['auth']);
		$this->redis->select($redis_conf['db']);
		return $this->redis;
	}
}