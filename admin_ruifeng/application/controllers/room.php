<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class room extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('base');
		$this->load->database();
	}

	public function index()
	{
		$this->room_list();
	}

	function room_list()
	{
		$this->out_data['con_page'] = 'room_list';
		$this->out_data['room_list'] = $this->db->query("select id,name,pwd from {$this->db->dbprefix('room')}")->result_array();
		$this->load->view('default', $this->out_data);
	}

	function edit_room($room_id = 0)
	{
		$info = $this->db->query("select * from {$this->db->dbprefix('room')} where id='{$room_id}' limit 1");
		if($info->num_rows() > 0)
		{
			$this->out_data['room_info'] = $info->row_array();
		}

		$redis = parent::redis_conn();
		$this->out_data['room_info']['del_public_msg'] = (int)$redis->get('is_del_public_msg');

		$this->out_data['con_page'] = 'room_edit';
		$this->load->view('default', $this->out_data);
	}

	function del_room()
	{
		$result = array('status' => false, 'msg' => '');
		$tb_room = $this->db->dbprefix('room');
		$room_id = $this->input->post('id');
		if($this->db->query("select count(id) as num from {$tb_room}")->row()->num == 1)
		{
			/*若当前只有一个房间了，则不允许删除*/
			$result['msg'] = '当前只有一个房间，不允许删除';
		}
		else
		{
			$result['status'] = true;

			/*----------------------删除一切与该房间有关的数据，后续可能会添加----------------------*/
			$this->db->delete($tb_room, array('id' => $room_id));
			$this->db->delete('curriculum', array('rid' => $room_id));
			$this->db->delete('chat_list', array('rid' => $room_id));
			$this->db->delete('strategy', array('rid' => $room_id));
			$this->db->query("delete from {$this->db->dbprefix('specialist_vote')} where sid IN (select id from {$this->db->dbprefix('specialist')} where rid = '{$room_id}')");
			$this->db->delete('specialist', array('rid' => $room_id));
			$redis = parent::redis_conn();
			$redis->del('room_examin_'.$room_id); /*房间是否自动审核*/
			$redis->del($redis->keys("member_list_{$room_id}_*")); /*房间在线用户列表*/
			$redis->zrem("examine_record_{$room_id}", 0, -1); /*房间未审核聊天记录*/
			$redis->zrem("room_{$room_id}", 0, -1); /*房间已审核聊天记录*/
			$redis->del("forbiddenword"); /*删除屏蔽词，forbidden*/
			unset($redis);
			$this->db->cache_delete_all();
			/*----------------------删除一切与该房间有关的数据，后续可能会添加----------------------*/
		}
		echo json_encode($result);
	}

	function update_room()
	{
		$result = array('status' => false, 'msg' => '');
		$room_id = $this->input->post('id');
		$old_id = $this->input->post('old_id');
		$title = $this->input->post('title');

		$info = array('id' => $room_id,
			'name' => $this->input->post('name'),
			'title' => $this->input->post('title'),
			'keywords' => $this->input->post('keywords'),
			'description' => $this->input->post('description'),
			'video' => $this->input->post('video'),
			'obs_video' => $this->input->post('obs_video'),
			'pwd' => $this->input->post('pwd'),
			'qq' => $this->input->post('qq'),
			'phone' => $this->input->post('phone'),
			'qq_code' => $this->input->post('qq_code'),
			'statistics' => $this->input->post('statistics'),
			'shikuang' => $this->input->post('shikuang'),
			'forbidden' => $this->input->post('forbidden'),
			'initpeo' => $this->input->post('initpeo'),
			// 'auto_examine' => $this->input->post('auto_examine'),
			'auto_adminmsg' => $this->input->post('auto_adminmsg')
			);

		$tb_room = $this->db->dbprefix('room');
		/*判断该房间号是否已存在*/
		if($this->db->query("select 1 from {$tb_room} where id='{$room_id}' AND id <> '{$old_id}' limit 1")->num_rows() > 0)
		{
			$result['msg'] = '该房间号已存在，请重新输入';
		}
		else if($room_id == $old_id)
		{
			/*已存在的房间*/
			$this->db->update($tb_room, $info, array('id' => $old_id));
			$result['status'] = true;
			$this->db->cache_delete_all();
		}
		else if($old_id == 0)
		{
			/*新添加的房间*/
			$this->db->insert($tb_room, $info);
			$result['status'] = true;
			$this->db->cache_delete_all();
		}
		
		$redis = parent::redis_conn();

		$redis->set('is_del_public_msg', (int)$this->input->post('del_public_msg'));
		
		$redis->set('video_zhnashi', $info['video']);
		$redis->set('video_obs', $info['obs_video']);
		
		// $redis->set('room_examine_'.$room_id, $info['auto_examine']);
		$redis->set('initpeo', $info['initpeo']);
		$redis->del("forbiddenword"); /*删除屏蔽词，forbidden*/
		echo json_encode($result);
	}
}