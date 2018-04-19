<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class im extends MY_Controller {

	protected $redis;
	private $rid;
	function __construct()
	{
		parent::__construct();
		$this->redis = parent::redis_conn();
		$this->rid = $this->input->post('rid');
		if(!$this->rid) $this->rid = $this->session->userdata('rid');
		if(!$this->rid) exit;
		$this->load->database();
	}

	public function index()
	{
		$this->out_data['tpl'] = $this->session->userdata('tpl');
		/*是会员聊天还是游客聊天*/
		if($this->session->userdata('is_login'))
		{
			/*会员*/
			$table = $this->db->dbprefix('member');
		}
		else
		{
			/*游客*/
			$table = $this->db->dbprefix('visitor');
		}
		$gid = $this->session->userdata('gid');
		$cid = '';
		/*看当前访客是否设置了客服或者客服是否已经被删除*/
		$customer_service = $this->db->query("select a.id,a.wellcome,a.nick,a.sex,b.headimg from {$table} as t inner join {$this->db->dbprefix('admin')} as a inner join {$this->db->dbprefix('admin_extra')} as b on t.cid=a.id where t.id={$this->session->userdata('mid')} and find_in_set('customer', a.permission) and (({$gid} = 1 and a.login_status = 1) or {$gid} <> 1) limit 1");
		if($customer_service->num_rows() > 0)
		{
			$customer_service = $customer_service->row_array();
			$cid = $customer_service['id'];
		}
		else
		{
			$cname = '';
			$customer_service_online = array();
			/*重新给他设置一个客服*/
			$customer_service = $this->db->query("select a.id,a.wellcome,a.nick,a.login_status,a.sex,b.headimg from {$this->db->dbprefix('admin')} as a  inner join {$this->db->dbprefix('admin_extra')} as b where find_in_set('customer', permission)")->result_array();
			foreach($customer_service as $k => $v){
				if($v['login_status']){
					$customer_service_online[] = $v;
				}
				if($this->session->userdata('cid') == $v['id']){
					$cname = $v['nick'];
				}
			}
			if(!$customer_service_online AND $this->session->userdata('cid')){//如果没在线的并且是已经进来的
				$cid = $this->session->userdata('cid');
				$customer_service['nick'] = $cname;
			}else{
				if($customer_service_online){//如果有在线人数，遍历在线的
					$customer_service = $customer_service_online;
				}
				if($customer_service){
					$key = array_rand($customer_service);
					$customer_service = $customer_service[$key];
					$cid = $customer_service['id'];
					$this->db->update($table, array('cid' => $cid), array('id' => $this->session->userdata('mid')));
				}
			}
		}

		if($customer_service){
			$customer_name = $customer_service['nick'];
			$this->out_data['customer_name'] = $customer_name;
			$mid = $this->session->userdata('mid');
			$gid = $this->session->userdata('gid');
			$this->session->set_userdata('cid', $cid);
		}

		/*我的客服列表*/
		$is_send_my_kefu = $this->is_send_my_kefu();
		$kefu_sql = "select a.id,a.name,a.nick,a.sex,e.qq,e.headimg from {$this->db->dbprefix('admin')} as a inner join {$this->db->dbprefix('admin_extra')} as e on a.id=e.aid where find_in_set('customer', a.permission) ";
		if($is_send_my_kefu)
		{
			/*只有专属客服能联系*/
			$kefu_sql .= " and a.id = {$cid} limit 1 ";
		}
		else
		{
			/* 都可以被扫的情况下，只显示在线的客服 */
			$kefu_sql .= " and a.login_status = 1 ";
		}
		$service_list = $this->db->query($kefu_sql)->result_array();
		if(empty($service_list))
		{
			$service_list = $this->db->query("select a.id,a.name,a.nick,a.sex,e.qq,e.headimg from {$this->db->dbprefix('admin')} as a inner join {$this->db->dbprefix('admin_extra')} as e on a.id=e.aid where find_in_set('customer', a.permission) and a.id = '{$cid}' limit 1 ")->result_array();
		}
		$this->out_data['service_list'] = array();
		foreach($service_list as $v)
		{
			$this->out_data['service_list'][$v['id']] = $v;
		}

		$this->out_data['cid'] = $cid;
		$this->out_data['is_send_my_kefu'] = $is_send_my_kefu;
		$this->load->view("im/index", $this->out_data);
	}

	/*发送聊天信息*/
	function send_msg()
	{
		parent::is_forbidden();

		/*下面准备开始发送信息了*/
		$mid = $this->session->userdata('mid');
		$gid = $this->session->userdata('gid');
		$cid = $this->input->post('service_id');
		$click_resource = $this->input->post('click_resource');
		if($gid == 1)
		{
			$table = $this->db->dbprefix('visitor');/*游客*/
		}
		else
		{
			$table = $this->db->dbprefix('member');/*会员*/
		}
		// $cid = $this->db->query("select cid from {$table} where id={$mid} limit 1")->row()->cid;
		
		$info = array('mid' => $mid, 'gid' => $gid, 'aid' => $cid, 'time' => date('Y-m-d H:i:s'), 'content' => $this->security->xss_clean(strip_tags($this->input->post('content'), '<img>')), 'send_name' => $this->session->userdata('name'), 'click_resource' => $click_resource);
		$this->db->insert('visitor_chat', $info);
		if(!$this->session->userdata('is_talk') AND $gid == 1)
		{
			$count = $this->db->query("select count(1) as num from {$this->db->dbprefix('visitor_istalk')} where sid = {$mid}")->row()->num;
			if($count <= 0){
				$res = $this->db->query("select id as sid,name,ip,keyword,source,is_talk,remark,cid,time from {$this->db->dbprefix('visitor')} where id = {$mid}")->row_array();
				$res['is_talk'] = 1;
				$this->db->insert('visitor_istalk', $res);
				$this->db->update('visitor', array('is_talk' => $res['is_talk']), array('id' => $mid));
			}
			$this->session->set_userdata('is_talk', 1);
		}
		send_websocket(array('type' => 'private_msg', 'to' => 'admin_'.$cid.'|'.$mid.'_'.$gid, 'content' => json_encode($info)));
	}
	
	function get_qq($cid)
	{
		$admin = $this->db->query("select qq from {$this->db->dbprefix('admin_extra')} where aid = {$cid} limit 1")->row_array();
		if(isset($admin['qq']))
		{
			echo $admin['qq'];
		}
	}
	
	function get_kefu($id)
	{
		$data = $this->db->query("select a.id,a.name,a.nick,a.sex,e.qq,e.headimg from {$this->db->dbprefix('admin')} as a inner join {$this->db->dbprefix('admin_extra')} as e on a.id=e.aid where find_in_set('customer', a.permission) and a.id = {$id} limit 1 ")->row_array();
		echo json_encode($data);
	}

}