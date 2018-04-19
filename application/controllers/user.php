<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class user extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function index()
	{
		show_404();
	}

	/*会员中心页面*/
	function info()
	{
		$this->_check_member_login();
		$this->out_data['info'] = $this->db->query("select gid,name,re_time,login_time,ip from {$this->db->dbprefix('member')} where id={$this->session->userdata('mid')} limit 1")->row_array();
		$this->out_data['info']['gname'] = $this->db->query("select name from {$this->db->dbprefix('group')} where id={$this->out_data['info']['gid']} limit 1")->row()->name;
		/* if($this->out_data['info']['tid'] != 0)
		{
			$this->out_data['info']['tname'] = $this->db->query("select name from {$this->db->dbprefix('teacher')} where id={$this->out_data['info']['tid']} limit 1")->row()->name;
		}
		else
		{
			$this->out_data['info']['tname'] = '无';
		} */

		$this->out_data['sidebar_current'] = 'info';
		$this->out_data['con_page'] = 'info';

		$tpl = $this->config->item('tpl');
		$this->out_data['tpl'] = 'skin/'.$tpl.'/';
		$this->load->view("{$tpl}/member/default", $this->out_data);
	}

	/*会员中心修改密码页面*/
	function password()
	{
		$this->_check_member_login();

		$this->out_data['sidebar_current'] = 'password';
		$this->out_data['con_page'] = 'password';

		$tpl = $this->config->item('tpl');
		$this->out_data['tpl'] = 'skin/'.$tpl.'/';
		$this->load->view("{$tpl}/member/default", $this->out_data);
	}

	/*修改密码动作*/
	function update_password()
	{
		$this->_check_member_login();

		$old_password = md5($this->input->post('old_password'));
		$password = md5($this->input->post('password'));

		$tb_member = $this->db->dbprefix("member");
		$result = array('status' => false, 'msg' => '');
		$mid = $this->session->userdata('mid');
		if($this->db->query("select 1 from {$tb_member} where id={$mid} and password='{$old_password}' limit 1")->num_rows() == 0)
		{
			$result['msg'] = "原密码输入错误，请确认";
		}
		else
		{
			$this->db->query("update {$tb_member} set password='{$password}' where id={$mid} limit 1");
			$result['status'] = true;
		}
		echo json_encode($result);
	}

	/*会员登录*/
	function login()
	{
		$result = array('status' => false, 'msg' => '', 'kefu' =>false);

		/*先判断验证码是否正确*/
		$captcha = strtolower($this->input->post('captcha'));
		if($captcha != $this->session->userdata('captcha'))
		{
			$result['msg'] = '验证码不正确，请重新输入';
			echo json_encode($result);
			exit;
		}

		/*再来判断账号是否存在*/
		$user = $this->input->post('user');
		$encrypt_user = sha1($user);
		$member = $this->db->query("select id,gid,name,password,is_company,is_verify,cid from {$this->db->dbprefix('member')} where name='{$user}' limit 1");
		if($member->num_rows() == 0)
		{
			$result['msg'] = '您输入的账号不存在，请确认账号';
			echo json_encode($result);
			exit;
		}

		/*账号存在，来判断密码吧*/
		$pwd = $this->input->post('pwd');
		$member = $member->row_array();
		if(md5($pwd) != $member['password'])
		{
			$result['msg'] = '您的密码不正确，请重新输入';
			echo json_encode($result);
			exit;
		}

		//判断用户是否需要审核
		if($member['is_verify'] == 1)
		{
			$cname = '';
			$cid = $member['cid'];
			$cArr = $this->db->query("select nick from {$this->db->dbprefix('admin')} where id={$cid} limit 1")->row_array();
			if($cArr) $cname = $cArr['nick'];
			$result['msg'] = "您的账号正在审核！\r\n请联系客服 ".$cname." ！";
			$result['kefu'] = true;
			echo json_encode($result);
			exit;
		}

		/*好，所有一切都没问题，那就设置登录信息*/
		$this->session->set_userdata('is_login', 1);
		$this->session->set_userdata('name', $member['name']);
		$this->session->set_userdata('gid', $member['gid']);
		$this->session->set_userdata('is_company', $member['is_company']); /*是否是公司的会员*/
		$ip = get_ip();
		$this->session->set_userdata('ip', $ip);
		$this->session->set_userdata('mid', $member['id']);

		/*获取他的小号*/
		//$alias_list = $this->db->query("select name,gid from {$this->db->dbprefix('member_alias')} where mid = {$member['id']}")->result_array();
		//$this->session->set_userdata('alias_list', $alias_list);
		$info = array('login_time' => date("Y-m-d H:i:s"), 'ip' => $ip);


		/*将他的最后登陆时间以及现在IP更新进数据库*/
		$this->db->update('member', $info, array('id' => $member['id']));

		/*再将他的在线情况存入redis(mid,gid,name)，同时设置过期时间70S*/
		// $redis = parent::redis_conn();
		// $redis->setex( "member_list_{$this->session->userdata('rid')}_1_{$member['id']}_{$member['gid']}", 70, json_encode( array('mid' => $member['id'], 'gid' => $member['gid'], 'name' => $member['name'] ) ) );
		// unset($redis);

		$result['status'] = true;
		echo json_encode($result);
	}

	/*会员注册*/
	function register()
	{
		$result = array('status' => false, 'msg' => '');
		//提交的数据
		$phone = $this->input->post('phone');
		$user = $this->input->post('user');
		$pwd = $this->input->post('pwd');
		$publicmsg = $this->input->post('publicmsg');
		//$qq = $this->input->post('qq');

		/*先判断会员名的有合法性比较好*/
		$validate = $this->_validate_name($user);
		if( ! $validate['status'])
		{
			$result['msg'] = $validate['msg'];
			echo json_encode($result);
			exit;
		}
		$numcount = $this->findNum($user);
		if($numcount > 3){
			$result['msg'] = '用户名数字不能超过3个！';
			echo json_encode($result);
			exit;
		}

		//密码不能为空
		if(!trim($pwd))
		{
			$result['msg'] = '密码不能为空';
			echo json_encode($result);
			exit;
		}

		//qq不能为空
		/* if(!trim($qq))
		{
			$result['msg'] = 'QQ不能为空';
			echo json_encode($result);
			exit;
		} */

		/*来判断密码和确认密码*/
		$repwd = $this->input->post('repwd');
		if($pwd != $repwd)
		{
			$result['msg'] = '密码与确认密码不符，请重新输入';
			echo json_encode($result);
			exit;
		}

		/* 判断手机号是否正确 */
		$res_phone = $this->_verify_phone($phone);
		if(!$res_phone){
			$result['msg'] = '不合法的手机号！';
			echo json_encode($result);
			exit;
		}

		/*判断手机号是不是唯一的*/
		$have_phone = $this->db->query("select id from {$this->db->dbprefix('member')} where phone = {$phone} limit 1")->num_rows();
		if($have_phone > 0){
			$result['msg'] = '您的手机号已经注册，请联系客服！';
			echo json_encode($result);
			exit;
		}

		/*先判断验证码是否正确*/
		if($publicmsg != $this->session->userdata('publicmsg') OR $phone != $this->session->userdata('phone'))
		{
			$result['msg'] = '验证码不正确，请重新获取';
			echo json_encode($result);
			exit;
		}

		/*再来判断账号是否存在*/
		$member = $this->db->query("select id from {$this->db->dbprefix('member')} where name='{$user}' limit 1");
		if($member->num_rows() > 0)
		{
			$result['msg'] = '您输入的账号已存在，请换个账号';
			echo json_encode($result);
			exit;
		}


		//随机设置专属老师和客服
		$customer_service_list = $this->db->query("select id from {$this->db->dbprefix('admin')} where find_in_set('customer', permission) and login_status = 1 and id not in('77','108')")->result_array();
		$teacher_service_list = $this->db->query("select id from {$this->db->dbprefix('admin')} where find_in_set('teacher', permission) and login_status = 1")->result_array();
		$cid = empty($customer_service_list) ? null : $customer_service_list[array_rand($customer_service_list)]['id'];
		$tid = empty($teacher_service_list) ? null : $teacher_service_list[array_rand($teacher_service_list)]['id'];
		//都判断好之后
		$info = array('name' => $user,
			'gid' => 2,
			'cid' => $cid,
			'tid' => $tid,
			'is_company' => 0,
			//'qq' => $qq,
			'phone' => $phone,
			'password' => md5($pwd),
			're_time' => date('Y-m-d H:i:s'),
			'is_mobile_reg' => 2,
			'is_verify' => 0
		);

		//向所属客服发送消息说我注册成功了
		send_websocket(array('type' => 'register_success', 'to' => 'admin_'.$cid, 'content' => $user));


		/* 来源关键词 */
		$host = $_SERVER['HTTP_HOST'];
		$source = $this->db->query("select source from {$this->db->dbprefix('source')} where host = '{$host}' limit 1");
		if($source->num_rows() > 0){
			$info['source'] = $source->row()->source;
		}else{
			$info['source'] = $host;
		}
		$info['keyword'] = $this->input->post('querystring');

		$tb_member = $this->db->dbprefix('member');
		$this->db->insert($tb_member, $info);
		$result['status'] = true;
		echo json_encode($result);
	}


	/*退出会员登录*/
	function logout()
	{
		/*先从redis中删除他的信息*/
		// $redis = parent::redis_conn();
		// $mid = $this->session->userdata('mid');
		// $gid = $this->session->userdata('gid');
		// $rid = $this->session->userdata('rid');
		// $redis->del("member_list_{$rid}_1_{$mid}_{$gid}");

		$this->session->sess_destroy();
		header("Location:".base_url());
		// header("Location:".base_url()."room/{$rid}");
	}

	/*名师榜中给专家投票*/
	function specialist_vote()
	{
		$result = array('status' => false, 'msg' => '');
		/*先看有没有登录，必须登录才能投票*/
		if( ! $this->session->userdata('is_login') )
		{
			$result['msg'] = '请登录后再投票';
			echo json_encode($result);
			exit;
		}

		/*再看他今天是否已经投过票了*/
		$mid = $this->session->userdata('mid');
		$tb_vote = $this->db->dbprefix('specialist_vote');
		$is_vote = $this->db->query("select count(1) as num from {$tb_vote} where mid={$mid} and DATE_FORMAT(NOW(),'%Y-%m-%d') = DATE_FORMAT(date,'%Y-%m-%d') limit 1 ")->row()->num;
		if($is_vote > 0)
		{
			$result['msg'] = '您今天已经给老师投过票了，请记得明天继续支持老师哦';
			echo json_encode($result);
			exit;
		}

		/*让他投票*/
		$sid = (int)$this->input->post('id');
		$this->db->insert($tb_vote, array('mid' => $mid, 'sid' => $sid, 'date' => date("Y-m-d")));
		$result['status'] = true;
		echo json_encode($result);
	}

/*-----------------------------------------以下为私有方法---------------------------------------------------*/

	/*验证用户名的合法性，3到15的长度，只能由字母，数字和下划线的组合*/
	private function _validate_name($name)
	{
		$result = array('status' => false, 'msg' => '');
		$len = mb_strlen($name);
		if($len < 3 OR $len > 20)
		{
			$result['msg'] = "用户名的长度为3到20之间";
		}
		elseif( ! preg_match('/^[0-9\x7f-\xff]+$/', $name))
		{
			$result['msg'] = '用户名只能由汉字，数字组成';
		}
		else
		{
			$result['status'] = true;
		}
		return $result;
	}

	/*检查登录情况*/
	private function _check_member_login()
	{
		if( ! $this->session->userdata('is_login') )
		{
			redirect("");
			//show_404();
			exit;
		}
	}

	private function _verify_phone($phone)
	{
		$result = array('status' => false, 'msg' => '');
		if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
			return true;
		}else{
			return false;
		}
	}

	//判断字符串中的数字个数
	private function findNum($str=''){
		$str=trim($str);
		if(empty($str)){return '';}
		$temp=array('1','2','3','4','5','6','7','8','9','0');
		$result='';
		for($i=0;$i<strlen($str);$i++){
			if(in_array($str[$i],$temp)){
				$result.=$str[$i];
			}
		}
		return strlen($result);
	}
}
