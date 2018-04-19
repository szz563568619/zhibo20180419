<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class room extends MY_Controller {

	private $is_mobile = false;
	function __construct()
	{
		parent::__construct();
		parent::is_forbidden();
		$this->is_mobile = is_mobile();
	}

	public function index($rid = '001')
	{

		$this->load->database();

		/*先确定好直播室模板先*/
		$tpl = $this->config->item('tpl');
		if($this->is_mobile)
		{
			$pwd_parm = '';
			if($this->input->get('pwd')) $pwd_parm = '&pwd='.$this->input->get('pwd');
			/*移动端访问，若没有tpl参数，加上，以区分链接*/
			if(!$this->input->get('tpl')) header("Location:".base_url().'room/'.$rid.'?tpl=wap'.$pwd_parm);
			$tpl = 'wap';
		}
		else
		{
			/*电脑端访问，若有tpl参数，去掉，以区分链接*/
			if($this->input->get('tpl')) header("Location:".base_url().'room/'.$rid);
		}
		$this->out_data['tpl'] = 'skin/'.$tpl.'/';
		$this->session->set_userdata('tpl', $this->out_data['tpl']);

		$this->db->cache_on();
		$room_list = $this->db->query("select id,pwd from {$this->db->dbprefix('room')}")->result_array();
		$this->db->cache_off();
		if(!$rid) $rid = $room_list[0]['id'];

		/*查看输入的房间ID是否存在 ，并且是否需要密码进入房间*/
		$is_room_exist = false;
		foreach($room_list as $v)
		{
			if($rid == $v['id'])
			{
				$is_room_exist = true; /*房间存在*/

				/*判断密码*/
				if($v['pwd'] != '' AND $this->input->get('pwd') != $v['pwd'])
				{
					$this->_get_me_pwd($rid);
					return;
				}
				break;
			}
		}
		if( ! $is_room_exist) show_404();

		/*----------------------------------------------能进入页面了，下面正式开始-----------------------------------------------------*/

		parent::set_visitor_info($rid);
		parent::is_change_member($rid);//检查会员是否变卦

		/*----------------------取各部分的数据了------------------*/

		/* 判断是只发给专属客服还是发给所有客服 */
		$this->out_data['is_send_my_kefu'] = parent::is_send_my_kefu();

		/*默认显示的一部分聊天记录从mysql数据库中读取*/
		$this->out_data['chat_list'] = $this->db->query("select name,time,content,gid,score,is_mobile,types from {$this->db->dbprefix('chat_list')} where rid='{$rid}' order by time desc limit 20")->result_array();
		krsort($this->out_data['chat_list']);

		/*最新消息列表，实际也就是后台的升级通告*/
		$this->db->cache_on();
		$this->out_data['upgrade_list'] = $this->db->query("select u.name,u.gid,g.name as gname from {$this->db->dbprefix('upgrade')} as u inner join {$this->db->dbprefix('group')} as g on g.id=u.gid")->result_array();
		$this->db->cache_off();

		/*房间相关信息*/
		$this->db->cache_on();
		$this->out_data['room'] = $this->db->query("select title,keywords,description,video,obs_video,is_obs_video,statistics,qq,qq_code,phone,shikuang,forbidden from {$this->db->dbprefix('room')} where id='{$rid}' limit 1")->row_array();
		$this->db->cache_off();
		//$this->session->set_userdata('forbidden', $this->out_data['room']['forbidden']);

		/* 获取新加入用户信息 */
		parent::add_innerpeo($rid);

		//来源
		$host = $_SERVER['HTTP_HOST'];
		$source = '';
		$source_info = $this->db->query("select source from {$this->db->dbprefix('source')} where host = '{$host}' limit 1");
		if($source_info->num_rows() > 0) $source = '_'.$source_info->row()->source;
		$this->out_data['source'] = $source;

		/* 获取当前客服信息 */
		$this->out_data['kefu_extra'] = $this->db->select('qq,phone,intro')->from('admin_extra')->where('aid', $this->session->userdata('cid'))->get()->row_array();//客服扩展信息

		//当前域名下的信息
		$this->out_data['domain_info'] = get_domain_info();

		$this->out_data['rid'] = $rid;
		//$this->out_data['alias_list'] = $this->session->userdata('alias_list');
		$this->out_data['alias_list'] = parent::get_alias_list();
		$socket = $this->config->item('socket');
		$this->out_data['socket_port'] = $socket['receive_port'];
		$this->out_data['socket_url'] = $socket['url'];
		$this->load->view("{$tpl}/room", $this->out_data);
	}

/*-----------------------------------------以下为私有方法---------------------------------------------------*/

	private function _get_me_pwd($rid)
	{
		$this->load->database();
		$this->db->cache_on();
		$qq = $this->db->query("select qq from {$this->db->dbprefix('room')} limit 1")->row()->qq;
		$this->db->cache_off();
		$res = '';
		if($this->input->get('pwd') !== false){
			$res = '您输入的密码不正确，请重新输入';
		}
		$this->load->view('get_pwd', array('rid' => $rid, 'qq' => explode(',', $qq), 'res' => $res));
	}

}
