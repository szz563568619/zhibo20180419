<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class chat extends MY_Controller {

	protected $redis;
	private $rid;
	function __construct()
	{
		parent::__construct();
		$this->redis = parent::redis_conn();
		$this->rid = $this->input->post('rid');
		if(!$this->rid) $this->rid = $this->session->userdata('rid');
		if(!$this->rid) exit;
	}

	public function index()
	{
		show_404();
	}

	/*发送我的心跳，说明我还活着，有效时间70秒*/
	function send_heart()
	{
		$mid = $this->session->userdata('mid');
		$gid = $this->session->userdata('gid');
		$this->redis->setex( "member_list_{$this->rid}_1_{$mid}_{$gid}", 70, '');
		//$this->redis->setex( "member_list_{$this->rid}_1_{$mid}", 70, '');
	}

	/*发送聊天信息*/
	function send_msg()
	{
		$this->load->database();
		parent::is_forbidden();
		
		$result = array("code"=>200);
		$is_nospeaking = parent::is_nospeaking();//禁言
		if($is_nospeaking){
			$result = array("code"=>403);
			echo json_encode($result);
			exit;
		}
		
		//如果当前会员被删了就重新设置成游客
		if($this->session->userdata('is_login')){
			$mArr = $this->db->query("select id,name,gid from {$this->db->dbprefix('member')} where name = '{$this->session->userdata('name')}' limit 1")->row_array();
			if(empty($mArr)){
				$this->session->unset_userdata('is_login');
				$this->session->unset_userdata('rid');
				$this->session->unset_userdata('name');
				$this->session->unset_userdata('gid');
				$this->session->unset_userdata('ip');
			}
		}
		
		$type = 'examine_public_msg'; /*这是将要发送到socket中的type*/
		$is_auto_examine = false; /*是否是自动审核的信息*/
		$ip = get_ip(); /*IP*/

		/*下面准备开始发送信息了*/
		$mid = $this->session->userdata('mid');
		$gid = $this->session->userdata('gid');
		$gid1 = $this->session->userdata('gid');
		if($gid === false) parent::set_visitor_info($this->rid);
		$gid = isset($_POST['gid']) ? $_POST['gid'] : $this->session->userdata('gid');
		$name = isset($_POST['name']) ? $_POST['name'] : $this->session->userdata('name');
		
		if($this->session->userdata('gid') == 1){
			$gid = $this->session->userdata('gid');
			$name = $this->session->userdata('name');
		}
		
		$name = strip_tags($name);
		// $forbidden = explode('|', $this->session->userdata('forbidden') );
		$forbidden = explode('|', parent::forbiddenword());
		$content = str_replace($forbidden, '**', strip_tags($this->input->post('content'), '<img>'));
		if(!$this->session->userdata('is_company')){
			$content = is_shouji(is_qq($content));
			$content = content_nofollow($content);
		}
		$content = preg_replace("/@([\S]*)/", "<label style='color:#0000ff;font-size: 14px;font-weight: bold;'>@$1</label>", $content);
		
		//如果是管理员，后面添加qq信息
		if($gid == 0){
			$uqq = $this->redis->get('uqq_'.$mid);
			if(!$uqq){//如果redis里面没有，就查数据库
				$this->load->database();
				$tmp_qq = $this->db->query("select qq from {$this->db->dbprefix('member')} where id = '{$mid}' limit 1")->row_array();
				$this->redis->setex( 'uqq_'.$mid, 3600*24, $tmp_qq['qq']);
				$uqq = $tmp_qq['qq'];
			}
			if($uqq){
				$content .= '<a href="javascript:;" onclick="open_qq('.$uqq.')" style="margin-left:10px;text-decoration:none;color:red;"><img src="'.base_url().'skin/'.$this->config->item('tpl').'/images/webchat/qq.png" align="absmiddle"></a>';
			}
		}
		
		$info = array('rid' => $this->rid, 'gid' => $gid, 'time' => date('Y-m-d H:i:s'), 'content' => $content, 'name' => $name);

		if($this->redis->get('room_examine_'.$this->rid) )
		{
			/*如果该房间是自动审核房间*/
			$type = 'public_msg';
			$is_auto_examine =  true;
		}

		if($this->session->userdata('is_company'))
		{
			/*如果公司内部发言，添加标记字段，便于后台统计*/
			$info['types'] = '1';
			if($this->session->userdata('gid') !== 1)
			{
				/*如果既是内部，又是非游客发言，则自动审核*/
				$type = 'public_msg';
				$is_auto_examine =  true;
			}
		}

		// 看是从pc端发过来的还是从wap端发过来的
		$is_mobile = (int)$this->input->post('is_mobile');
		$info['is_mobile'] = $is_mobile;
		
		//弹幕
		$is_dan = $this->input->post('is_dan');

		$score = str_pad(str_replace('.', '', microtime(true)),14,0);
		//设置时间串唯一标识插入数据库
		$info['score'] = $score;
		send_websocket(array('type' => $type, 'content' => json_encode($info+array('is_handan' => $is_dan))));

		if($is_auto_examine)
		{
			/*如果是自动审核的记录，还需同时记入数据库*/
			$this->load->database();
			$this->db->insert('chat_list', $info);
		}
		else
        {
            $this->load->database();
            $this->db->insert('chat_list_examine', $info);
        }
		echo json_encode($result);
	}
	
	/* 巡管删除聊天信息 */
	function del_msg(){
		$score = $this->input->post('score');
		$result = array("code"=>200);
		//先判断是不是巡官
		if($this->session->userdata("gid") == 0){
			$is_del_public_msg = $this->redis->get('is_del_public_msg');
			if(!$is_del_public_msg){
				$result['code'] = 404;
			}else{
				//在这里把要删除的信息存入redis
				send_websocket(array('type' => 'del_public_msg', 'content' => $score));
				//然后删除数据库中的这条信息
				$this->load->database();
				$this->db->delete('chat_list', array('score' => $score)); 
			}
		}else{
			$result['code'] = 403;
		}
		echo json_encode($result);
	}
	
	//巡管屏蔽ip
	function ip_ban()
	{
		$result = array('status' => false, 'msg' => '');
		$forbidden = $this->input->post('forbidden');
		$key = 'ipban_'.$forbidden;
		
		$admin_name = $this->session->userdata('name'); //管理员,屏蔽人

		//公司ip
		$company_ip = explode(',', $this->config->item('company_ip'));

		if(in_array($forbidden, $company_ip))
		{
			$result['msg'] = '该IP是公司IP，不能屏蔽！';
		}
		else
		{
			$this->redis->setex($key, 3600*24*15, $admin_name); /*立马屏蔽掉*/
			/*如果上面被屏蔽的是用户名，那同时再把他的IP也屏蔽掉，如果能找到他的IP的话*/
			if(!filter_var($forbidden, FILTER_VALIDATE_IP))
			{
				$this->load->database();
				$ip = '';
				if(strpos($forbidden, '游客') == 0)
				{
					/*该用户名是游客，去游客表中找*/
					$ip_info = $this->db->query("select ip from {$this->db->dbprefix('visitor')} where name = '{$forbidden}' limit 1");
				}
				else
				{
					$ip_info = $this->db->query("select ip from {$this->db->dbprefix('member')} where name = '{$forbidden}' limit 1");
				}
				if($ip_info->num_rows() > 0) $ip = $ip_info->row()->ip;

				if($ip != '' AND !in_array($forbidden, $company_ip))
				{
					$this->redis->setex('ipban_'.$ip, 3600*24*15, $admin_name);
				}
			}
			$result['status'] = true;
		}
        echo json_encode($result);

	}
	
	//用户禁言
	function nospeaking_ban()
	{
		$result = array('status' => false, 'msg' => '');
		$forbidden = $this->input->post('forbidden');
		$key = 'nospeaking_'.$forbidden;
		
		$admin_name = $this->session->userdata('name'); //管理员,屏蔽人

		//公司ip
		$company_ip = explode(',', $this->config->item('company_ip'));

		if(in_array($forbidden, $company_ip))
		{
			$result['msg'] = '该IP是公司IP，不能屏蔽！';
		}
		else
		{
			$this->redis->setex($key, 3600*24*15, $admin_name); /*立马屏蔽掉*/
			/*如果上面被屏蔽的是用户名，那同时再把他的IP也屏蔽掉，如果能找到他的IP的话*/
			if(!filter_var($forbidden, FILTER_VALIDATE_IP))
			{
				$this->load->database();
				$ip = '';
				if(strpos($forbidden, '游客') == 0)
				{
					/*该用户名是游客，去游客表中找*/
					$ip_info = $this->db->query("select ip from {$this->db->dbprefix('visitor')} where name = '{$forbidden}' limit 1");
				}
				else
				{
					$ip_info = $this->db->query("select ip from {$this->db->dbprefix('member')} where name = '{$forbidden}' limit 1");
				}
				if($ip_info->num_rows() > 0) $ip = $ip_info->row()->ip;

				if($ip != '' AND !in_array($forbidden, $company_ip))
				{
					$this->redis->setex('nospeaking_'.$ip, 3600*24*15, $admin_name);
				}
			}
			$result['status'] = true;
		}
        echo json_encode($result);

	}

}