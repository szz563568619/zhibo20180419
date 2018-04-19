<?php

class md_login extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	function check_login($name, $password)
	{
		$result = array('status' => true, 'msg' => '');
		$query = $this->db->query("select id,nick,permission,rid from {$this->db->dbprefix('admin')} where name='{$name}' and password='".md5($password)."'");
		if($query->num_rows() > 0)
		{
			$query = $query->row_array();
			$result = array_merge($result, $query);
		}
		else
		{
			$result = array('status' => false, 'msg' => '用户名或密码错误，请重新输入');
		}
		return $result;
	}

	// private function _get_ip()
	// {
	// 	$ip = $_SERVER['REMOTE_ADDR'];
	// 	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '')
	// 	{
	// 		$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	// 		$ip = $ip[0];
	// 	}
	// 	return $ip;
	// }

}