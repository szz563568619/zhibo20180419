<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class visitoripon extends MY_Controller {

	protected $redis;
	function __construct()
	{
		parent::__construct();
		$this->redis = parent::redis_conn();
	}

	public function index()
	{
		show_404();
	}
	
	/* 设置游客ip在线时间，只要在线就会访问这个，2分钟访问1次*/
	function set_visitoripon()
	{
		$result = array('status' => true, 'msg' => 'ok', 'flag' => '');
		$gid = $this->session->userdata('gid');
		if($gid == 1)//只统计游客
		{
			$this->load->database();
			$ip = get_ip();//当前用户的ip
			$visitoripon_limittime_part = $this->redis->get('visitoripon_limittime_part');//限制时长，不能看视频聊天，分钟
			$visitoripon_limittime_all = $this->redis->get('visitoripon_limittime_all');//限制时长,什么都不能看，分钟
			//$visitoripon_totaltime = (int)$this->session->userdata('visitoripon_totaltime');//当前ip时长，分钟
			$visitoripon_sql = $this->db->query("select ip,updatetime,totaltime from {$this->db->dbprefix('visitoripon')} where ip = '{$ip}'");//查询当前ip信息的sql
			$visitoripon = $visitoripon_sql->row_array();
			$visitoripon_totaltime = isset($visitoripon['totaltime']) ? round($visitoripon['totaltime']/60) : 0;//当前ip时长，分钟
			if($visitoripon_totaltime > $visitoripon_limittime_all)
			{
				$result = array('status' => false, 'msg' => '超时', 'flag' => 'all');//如果超过最大时长，就返回false，不下行了
			}
			else
			{
				if($visitoripon_totaltime > $visitoripon_limittime_part)
				{
					$result = array('status' => false, 'msg' => '超时', 'flag' => 'part');//如果是大于不能看视频聊天的时间
				}
				$curtime = time();//当前时间，也是要放入visitoripon表中的最新一次的时间，单位秒
				$addtime = 0;//要加到总时长totaltime里面的时间，单位分钟
				$totaltime = 0;//累计时长，秒
				if($visitoripon_sql->num_rows() == 0)
				{
					//如果没有，就插入到这个表里面，最新更新时间为当前时间
					$this->db->insert('visitoripon', array('ip' => $ip, 'updatetime' => $curtime));
				}
				else
				{
					//到这一步，visitoripon表里肯定有对应$ip的这条数据，下面就是更新时长的问题了
					if($curtime - $visitoripon['updatetime'] <= (2*60+60))
					{
						$addtime = $curtime - $visitoripon['updatetime'];//如果要现在和上次更新时间的间隔小于3分钟，考虑是在线
					}
					$totaltime = $addtime + $visitoripon['totaltime'];
					//然后就把新增加的时间和最新更新时间，更新到这条记录里面去
					$this->db->update('visitoripon', array('totaltime' => $totaltime, 'updatetime' => $curtime), array('ip' => $ip));
				}
				//$this->session->set_userdata('visitoripon_totaltime', round($totaltime/60));
			}
		}
		//$result['havetime'] = $visitoripon_limittime_all - $visitoripon_totaltime;
		echo json_encode($result);
	}
	
	function havetime()
	{
		$ip = get_ip();//当前用户的ip
		$this->load->database();
		$visitoripon_limittime_all = $this->redis->get('visitoripon_limittime_all');//限制时长,什么都不能看，分钟
		//$visitoripon_totaltime = (int)$this->session->userdata('visitoripon_totaltime');//当前ip时长，分钟
		$visitoripon = $this->db->query("select ip,updatetime,totaltime from {$this->db->dbprefix('visitoripon')} where ip = '{$ip}'")->row_array();//查询当前ip信息的sql
		$visitoripon_totaltime = isset($visitoripon['totaltime']) ? round($visitoripon['totaltime']/60) : 0;
		echo $visitoripon_limittime_all - $visitoripon_totaltime;
	}
}