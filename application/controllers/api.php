<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class api extends MY_Controller {

	private $rid;
	function __construct()
	{
		parent::__construct();
		$this->rid = $this->input->post('rid');
		if(!$this->rid) $this->rid = $this->session->userdata('rid');
		if(!$this->rid) $this->rid = '001';
	}

	function get_course_list()
	{
		$this->load->database();
		$this->db->cache_on();
		$curriculum_list = $this->db->query("select curr_name,start_time,end_time,monday,tuesday,wednesday,thursday,friday from {$this->db->dbprefix('curriculum')} where rid='{$this->rid}' order by id")->result_array();
		$teacher_list = $this->db->query("select id,name from {$this->db->dbprefix('teacher')}")->result_array();
		$this->db->cache_off();
		$key_teacher_list = array();
		foreach($teacher_list as $k => $v)
		{
			$key_teacher_list[$v['id']] = $v['name'];
		}
		foreach($curriculum_list as $k => $v)
		{
			if(isset($key_teacher_list[$v['monday']])) $curriculum_list[$k]['monday'] = $key_teacher_list[$v['monday']];
			if(isset($key_teacher_list[$v['tuesday']])) $curriculum_list[$k]['tuesday'] = $key_teacher_list[$v['tuesday']];
			if(isset($key_teacher_list[$v['wednesday']])) $curriculum_list[$k]['wednesday'] = $key_teacher_list[$v['wednesday']];
			if(isset($key_teacher_list[$v['thursday']])) $curriculum_list[$k]['thursday'] = $key_teacher_list[$v['thursday']];
			if(isset($key_teacher_list[$v['friday']])) $curriculum_list[$k]['friday'] = $key_teacher_list[$v['friday']];
		}
		echo json_encode($curriculum_list);
	}

	function get_kecheng_list()
	{
		$this->load->database();
		$this->db->cache_on();
		$curriculum_list = $this->db->query("select curr_name,start_time,end_time,monday,tuesday,wednesday,thursday,friday from {$this->db->dbprefix('curriculum')} where rid='{$this->rid}' order by id")->result_array();
		$teacher_list = $this->db->query("select id,name from {$this->db->dbprefix('teacher')}")->result_array();

		$data_list = $this->db->query("select info,week from {$this->db->dbprefix('course_data')} order by week asc")->result_array();
		$this->db->cache_off();
		foreach($teacher_list as $k => $v)
		{
			$key_teacher_list[$v['id']] = $v['name'];
		}
		foreach($curriculum_list as $k => $v)
		{
			if(isset($key_teacher_list[$v['monday']])) $curriculum_list[$k]['monday'] = $key_teacher_list[$v['monday']];
			if(isset($key_teacher_list[$v['tuesday']])) $curriculum_list[$k]['tuesday'] = $key_teacher_list[$v['tuesday']];
			if(isset($key_teacher_list[$v['wednesday']])) $curriculum_list[$k]['wednesday'] = $key_teacher_list[$v['wednesday']];
			if(isset($key_teacher_list[$v['thursday']])) $curriculum_list[$k]['thursday'] = $key_teacher_list[$v['thursday']];
			if(isset($key_teacher_list[$v['friday']])) $curriculum_list[$k]['friday'] = $key_teacher_list[$v['friday']];
		}
		$datalist = array();
		foreach ($data_list as $k => $v) {
			$datalist[$v['week']][] = $v['info'];
		}

		$res['kecheng'] = $curriculum_list;
		$res['data'] = $datalist;

		echo json_encode($res);

	}

	/*专家团队和名师榜*/
	function get_special_list()
	{
		$result = array();
		$this->load->database();
		$this->db->cache_on();
		$specialist = $this->db->query("select id,name,content,avatar from {$this->db->dbprefix('specialist')} where rid='{$this->rid}'")->result_array();
		$this->db->cache_off();
		$specialist_vote = $this->db->query("select sid,date from {$this->db->dbprefix('specialist_vote')} where DATE_FORMAT(NOW(), '%Y-%m') = DATE_FORMAT(date,'%Y-%m')")->result_array();
		$today = date("Y-m-d");
		$specialist_vote_sum = 0;
		$result['specialist'] = array();
		foreach($specialist as $v)
		{
			$v['month'] = 0;
			$v['today'] = 0;
			$result['specialist'][$v['id']] = $v;
		}
		foreach($specialist_vote as $v)
		{
			if(isset($result['specialist'][$v['sid']]))
			{
				$result['specialist'][$v['sid']]['month']++;
				if($v['date'] == $today)
				{
					$result['specialist'][$v['sid']]['today']++;
					$specialist_vote_sum++;
				}
			}
		}
		$specialist_vote_sum = $specialist_vote_sum == 0 ? 1 : $specialist_vote_sum; /*计算时分母不能为0*/
		$result['specialist_vote_sum'] = $specialist_vote_sum;
		echo json_encode($result);
	}
	
	/* 获取当前讲课老师 */
	function get_cur_teacher()
	{
		/* $this->load->database();
		$tid = '';//老师id
		$cur_time = date("H:i");//获取当前时间
		$week = strtolower(date("l"));//获取当前星期数
		$curriculum = $this->db->query("select * from {$this->db->dbprefix('curriculum')}")->result_array();
		foreach($curriculum as $v){
			if($cur_time >= $v['start_time'] AND $cur_time < $v['end_time']){
				$tid = $v[$week];
			}
		}
		$teacher = $this->db->query("select name from {$this->db->dbprefix('teacher')} where id = '{$tid}' limit 1")->row_array();
		if($teacher){
			echo $teacher['name'].'正在直播';
		}else{
			echo '当前无直播';
		} */
		$this->load->database();
		$curriculum = $this->db->query("select * from {$this->db->dbprefix('curriculum')}")->result_array();//获取课程
		//获取老师
		$teacher = $this->db->query("select id,name from {$this->db->dbprefix('teacher')}")->result_array();
		$tids = array_column($teacher, 'id');
		$names = array_column($teacher, 'name');
		$teacher = array_combine($tids, $names);
		//返回结果
		$result = array('curriculum' => $curriculum, 'teacher' => $teacher);
		echo json_encode($result);
		
	}
	
	/* 显示在线人数 */
	function get_online()
	{
		$redis = parent::redis_conn();
		$initpeo = $redis->get('initpeo');
		if(!$initpeo){
			$this->load->database();
			$initpeo = $this->db->query("select initpeo from {$this->db->dbprefix('room')} where id = '{$this->rid}' limit 1")->row()->initpeo;
			$redis->set('initpeo', $initpeo);
		}
		
		echo $initpeo;
	}
	
	function is_kefu_on()
	{
		$res = 0;//默认不跳转qq
		$redis = parent::redis_conn();
		$gid = $this->session->userdata('gid');
		$cid = $this->session->userdata('cid');
		if($gid != 1){//如果是会员
			if(!$redis->exists('kefu_on_'.$cid)){
				//如果没有，就从数据库中取值，存到redis并打印
				$this->load->database();
				$kefu = $this->db->query("select login_status from {$this->db->dbprefix('admin')}  where id = {$cid} limit 1")->row_array();
				$redis->set('kefu_on_'.$cid, $kefu['login_status']);
			}
			$status = $redis->get('kefu_on_'.$cid);
			if(!$status){//如果不在线
				$res = 1;
			}
		}
		echo $res;	
	}
	
	/* 获取抽奖结果 */
	function get_gift()
	{
		$this->load->database();
		$this->db->cache_on();
		$res = $this->db->query("select a.*,b.smallimg,b.bigimg,b.name as jname from {$this->db->dbprefix('jiangpin_peo')} as a left join {$this->db->dbprefix('jiangpin')} as b on a.jid = b.id order by orders asc limit 0,10")->result_array();
		$this->db->cache_off();
		echo json_encode($res);
	}
}