<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class change extends MY_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$userlist = $this->db->query("select username,password,adminid,telephone,dateline,logintime from userlist where adminid <> 14 and dateline <>2000000000 and adminid <> 16")->result_array();
		$arr_gid = array(1 => 0, 3 => 5, 4 => 6, 5 => 7, 6 => 8, 7 => 9, 8 => 3, 15 => 2, 17 => 4);
		foreach($userlist as $v)
		{
			if(isset($arr_gid[$v['adminid']]))
			{
				$this->db->insert('member', array('name' => $v['username'], 'password' => md5($v['password']), 'phone' => $v['telephone'], 're_time' => date("Y-m-d H:i:s", $v['dateline']), 'login_time' => date("Y-m-d H:i:s", $v['logintime']), 'gid' => $arr_gid[$v['adminid']]));
			}
		}
	}
}