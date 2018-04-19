<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class groupchat extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('customer,teacher');
	}
	
	/*群聊对话记录*/
	function chat_list()
	{
		$id = $this->session->userdata('id');
		$this->load->database();
		$chat_list = $this->db->query("select * from {$this->db->dbprefix('groupchat_list')} where ((tid={$id} OR cid={$id}) and is_member=1) or is_member = 0 order by time desc limit 20")->result_array();
		echo json_encode(array_reverse($chat_list));
	}

	function im()
	{
		$this->out_data['con_page'] = 'groupchat_im';
		$this->load->view('default', $this->out_data);
	}
	

	function im_page()
	{
		$socket = $this->config->item('socket');
		$this->out_data['socket_port'] = $socket['receive_port'];
		$this->out_data['socket_url'] = $socket['url'];
		$this->load->view('im/groupchat', $this->out_data);
	}

	/*发送群组聊天信息*/
	function send_msg()
	{
		$toyou = 0;
		/*下面准备开始发送信息了*/
		$id = $this->session->userdata('id');
		$to = $this->input->post('to'); /*发送给谁*/
		$this->load->database();
		$headimg = $this->db->query("select headimg from {$this->db->dbprefix('admin_extra')} where aid = {$id} limit 1")->row()->headimg;
		$info = array('uid' => $id, 'is_member' => 0, 'time' => date('Y-m-d H:i:s'), 'content' => $this->input->post('content'), 'send_name' => $this->session->userdata('nick'), 'headimg' => $headimg);
		if($to == '')
		{
			/*发送给所有属于我的客户以及所有管理员*/
			$to = 'group_'.$id.'|admin';
		}
		else
		{
			/*发送给指定uid的用户以及所有管理员*/
			$to .= '|admin';
            $toarr = explode('_', $to);
			if(my_echo($toarr[0])) $toyou = $toarr[0];
		}
		$info['toyou'] = $toyou;
		send_websocket(array('type' => 'groupchat_msg', 'to' => $to, 'content' => json_encode($info)));

		$this->load->database();
		$this->db->insert('groupchat_list', $info);
		
	}

}