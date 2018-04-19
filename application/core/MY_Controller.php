<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	/**
	 * this is the base controller
	 * all the controller must extends base controller
	 */
	protected $out_data;
	protected $redis = null;
	function __construct()
	{
		parent::__construct();
		//$this->check_member();
	}
	
	/**
	*判断会员是否存在
	*
	*/
	protected function check_member()
	{
		$gid = $this->session->userdata('gid');
		if($gid !== false AND $gid != 1){//如果是会员
			$mid = $this->session->userdata('mid');
			$this->load->database();
			$memArr = $this->db->query("select id from {$this->db->dbprefix('member')} where id = {$mid} limit 1")->row_array();
			if(empty($memArr)){
				$this->session->sess_destroy();
				header("Location:".base_url());
			}
		}
	}

	/**
	 * 连接redis
	 * @return resource 返回连接redis的资源
	 */
	protected function redis_conn()
	{
		if($this->redis != null) return $this->redis;

		$this->redis = new Redis();
		$redis_conf = $this->config->item('redis');
		$this->redis->connect($redis_conf['host'], $redis_conf['port']);
		if($redis_conf['auth'] != '') $this->redis->auth($redis_conf['auth']);
		$this->redis->select($redis_conf['db']);
		return $this->redis;

	}

	/**
	 * 获取数据库查询数据，先看是否有redis缓存，没有则查数据库放入缓存
	 * @param  [String] $key [存在redis中的key]
	 * @param  [String] $sql [该数据在mysql中的查询语句，表前缀用dbprefix代替]
	 * @return [Array]      [数据结果]
	 */
	// protected function get_select($key, $sql)
	// {
	// 	$re = self::redis_conn();
	// 	$result = $re->get($key);
	// 	if($result === false)
	// 	{
	// 		$this->load->database();
	// 		$result = $this->db->query(str_replace('dbprefix_', $this->db->dbprefix, $sql))->result_array();
	// 		$result = json_encode($result);
	// 		$re->set($key, $result);
	// 	}
	// 	return json_decode($result, true);
	// }

	function is_forbidden()
	{
		$ip = get_ip();
		$name = $this->session->userdata('name');
		$redis = $this->redis_conn();
		if($redis->exists('ipban_'.$ip) OR $redis->exists('ipban_'.$name))
		{
			$this->load->database();
			$this->db->cache_on();
			$qq = $this->db->query("select qq from {$this->db->dbprefix('room')} limit 1")->row()->qq;
			$this->db->cache_off();
			ob_start();
			$this->load->view("ban_page", array('qq' => explode(',', $qq)));
			ob_end_flush();
			exit();
		}
	}
	
	/* 用户禁言 */
	function is_nospeaking()
	{
		$ip = get_ip();
		$name = $this->session->userdata('name');
		$redis = $this->redis_conn();
		if($redis->exists('nospeaking_'.$ip) OR $redis->exists('nospeaking_'.$name)){
			return true;
		}
		return false;
	}

	/*是否是公司内部IP*/
	protected function is_company()
	{
		$ip = get_ip();
		$company_ip = $this->config->item('company_ip');
		if(in_array($ip, explode(',', $company_ip))) return true;
		else return false;
		
	}

	/**
	 * 在session中设置当前访问者的各项信息[is_login,name,gid,ip,mid,tpl]
	 * @param [string] $rid [房间ID]
	 */
	protected function set_visitor_info($rid)
	{
		$ip = get_ip();

		//判断是不是内部ip,写入session
		$is_company = self::is_company() ? 1 : 0;
		if($this->session->userdata('is_company') === false) $this->session->set_userdata('is_company', $is_company);

		if( ! $this->session->userdata('name'))
		{
			/*只有当session不存在的时候，才会设置信息*/
			$this->load->helper('string');
			$this->session->set_userdata('is_login', 0);
			$this->session->set_userdata('rid', $rid);
			$this->session->set_userdata('name', '游客'.random_string('alnum', 6));
			$this->session->set_userdata('gid', 1);
			$this->session->set_userdata('ip', $ip);
		}

		/*无论是会员还是游客，都要向数据库中更新数据*/
		if($this->session->userdata('is_login'))
		{
			$this->db->update('member', array('login_time' => date("Y-m-d H:i:s"), 'ip' => $ip), array('id' => $this->session->userdata('mid')));
		}
		else
		{
			$tb_visitor = $this->db->dbprefix('visitor');
			$name = $this->session->userdata('name');
			$visitor_id = $this->db->query("select id,keyword from {$tb_visitor} where name = '{$name}' limit 1");
			if($visitor_id->num_rows() == 0)
			{
				$info = array('name' => $name, 'ip' => $ip, 'keyword' => $_SERVER['QUERY_STRING']);
				if($info['keyword'] === 0) $info['keyword'] = '';
				$host = $_SERVER['HTTP_HOST'];
				$source = $this->db->query("select source from {$this->db->dbprefix('source')} where host = '{$host}' limit 1");
				if($source->num_rows() > 0) $source = $source->row()->source;
				else $source = $_SERVER['HTTP_HOST'];
				$info['source'] = $source;
				$info['time'] = date("Y-m-d H:i:s");
				
				$this->db->insert($tb_visitor, $info);
				$mid = $this->db->insert_id();
				$this->session->set_userdata('mid', $mid);
			}
			else
			{
				//如果已经有关键字
				$keyword = $visitor_id->row()->keyword;
				$keywordArr = explode(',', $keyword);
				$querykeyword = $_SERVER['QUERY_STRING'];
				if(!in_array($querykeyword, $keywordArr)){
					$keyword .= ','.$_SERVER['QUERY_STRING'];
				}
				$keyword = ltrim($keyword, ',');
				$info = array('ip' => $ip, 'keyword' => $keyword);
				$visitor_id = $visitor_id->row()->id;
				$this->db->update($tb_visitor, $info, array('id' => $visitor_id));
			}
		}
	}
	
	/* 查看这个会员在数据库中是否有变更，或者删除 */
	protected function is_change_member($rid)
	{
		if($this->session->userdata('is_login')){
			$mArr = $this->db->query("select id,name,gid from {$this->db->dbprefix('member')} where name = '{$this->session->userdata('name')}' limit 1")->row_array();
			if(empty($mArr)){
				header("Location:".base_url()."user/logout");
			}else{
				$this->session->set_userdata('gid', $mArr['gid']);
				$this->session->set_userdata('mid', $mArr['id']);
			}
		}
	}


	/**
	 * 获取在线列表
	 * @param  integer $is_member [是获取真实访客还是假人呢？]
	 * @param  string $rid [房间ID]
	 * @return [array]     [以group_id为索引分类的二维列表数组]
	 */
	protected function get_online_list($rid, $is_member = 1)
	{
		$redis = self::redis_conn();
		$online_list = $redis->keys("member_list_{$rid}_{$is_member}_*");
		$online_list = $redis->mget($online_list);
		$result = array();
		if(is_array($online_list))
		{
			foreach($online_list as $v)
			{
				$v = json_decode($v, true);
				if( ! isset($result[$v['gid']]) ) $result[$v['gid']] = array();

				$result[$v['gid']][] = $v;
			}
		}
		return $result;
	}
	
	/*刚进来的用户进来就加到redis*/
	protected function add_innerpeo($rid = '001')
	{
		$name = '';
		$this->redis = $this->redis_conn();
		
		//先判断是不是巡官,不是就开始添加
		if($this->session->userdata("gid") !== 0 AND $this->session->userdata("gid") !== '0'){
			$name = $this->session->userdata('name');
			if($this->session->userdata('send_name')){
				$name = $this->session->userdata('send_name');
			}
			$this->redis->lpush("g_innnerpeo_list",$name);
			send_websocket(array('type' => 'g_innnerpeo_list', 'content' => $name));
		}
	}
	
	/* 获取会员小号 */
	protected function get_alias_list(){
		$res = array();
		if($this->session->userdata('is_company')){
			$mid = $this->session->userdata('mid');
			$this->redis = $this->redis_conn();
			if($this->redis->exists('alias_list_'.$mid)){
				$res = json_decode($this->redis->get('alias_list_'.$mid),true);
			}else{
				$this->load->database();
				$res = $this->db->query("select name,gid from {$this->db->dbprefix('member_alias')} where mid = {$mid}")->result_array();
				$this->redis->set('alias_list_'.$mid,json_encode($res));
			}	
		}
		return $res;
	}
	
	/* 屏蔽关键词 */
	protected function forbiddenword(){
		$res = array();
		$this->redis = $this->redis_conn();
		if($this->redis->exists('forbiddenword')){
			$res = $this->redis->get('forbiddenword');
		}else{
			$this->load->database();
			$res = $this->db->query("select forbidden from {$this->db->dbprefix('room')} limit 1")->row()->forbidden;
			$this->redis->set('forbiddenword',$res);
		}	
		
		return $res;
	}
	
	/* 是否发送给专属客服，或者发送给所有客服 */
	protected function is_send_my_kefu()
	{
		//如果是会员，开户的或者未开户的注册时间在3天之内都分配给对应客服
		$login_status = $this->session->userdata('is_login');
		if($login_status)
		{
			/*$member = $this->db->query("select re_time,is_open from {$this->db->dbprefix('member')} where name = '{$this->session->userdata('name')}' limit 1")->row_array();
			if($member['is_open'] OR $member['re_time'] > date("Y-m-d H:i:s",strtotime("-3 day")))
			{
				//符合条件就返回true
				return 1;
			}*/
			return 1;
		}
		return 0;
	}
}