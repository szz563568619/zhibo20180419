<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class specialist extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('base');
		$this->out_data['current_function'] = 'specialist';
		$this->load->database();
	}

	public function index()
	{
		$this->specialist_list();
	}

	function specialist_list($room_id = '')
	{
		$this->out_data['room_list'] = $this->db->query("select id,name from {$this->db->dbprefix('room')}")->result_array();
		if($room_id == '') $room_id = $this->out_data['room_list'][0]['id'];

		$tb_specialist = $this->db->dbprefix('specialist');
		$this->out_data['specialist_list'] = $this->db->query("select id,name from {$tb_specialist} where rid='{$room_id}' order by sort desc ")->result_array();

		$this->out_data['room_id'] = $room_id;
		$this->out_data['con_page'] = 'specialist_list';
		$this->load->view('default', $this->out_data);
	}

	function specialist_del($id)
	{
		$id = (int)$id;
		$avatar = $this->db->query("select avatar from {$this->db->dbprefix('specialist')} where id={$id} limit 1")->row()->avatar;
		@unlink('../upload/specialist/'.$avatar);
		$this->db->delete('specialist', array('id' => $id));
		$this->db->cache_delete_all();
	}

	function specialist_edit($id = -1)
	{
		$info = $this->db->query("select id,name,rid,content,avatar,sort from {$this->db->dbprefix('specialist')} where id={$id} limit 1");
		if($info->num_rows() > 0)
		{
			$info = $info->row_array();
		}
		else
		{
			$info = array('id' => -1, 'avatar' => '');
		}
		$this->load->library('lib_elements');
		$info['avatar'] = $this->lib_elements->get_file_element(array('title' => '专家头像', 'name' => 'avatar', 'img' => '../upload/specialist/'.$info['avatar']));
		$this->out_data['room_list'] = $this->db->query("select id,name from {$this->db->dbprefix('room')}")->result_array();
		$this->out_data['info'] = $info;
		$this->out_data['con_page'] = 'specialist_edit';
		$this->load->view('default', $this->out_data);
	}

	function specialist_update()
	{
		$id = (int)$this->input->post('id');
		$info = array('name' => $this->input->post('name'),
			'rid' => $this->input->post('rid'),
			'sort' => (int)trim($this->input->post('sort')),
			'content' => $this->input->post('content'));
		$avatar = $this->input->post('avatar');
		$this->load->library('lib_elements');
		$is_upload_avatar = $this->lib_elements->move_img($avatar, '../upload/specialist/'.$avatar);
		if($is_upload_avatar)
		{
			/*上传了新头像*/
			$info['avatar'] = $avatar;
			if($id != -1)
			{
				$old_avatar = $this->db->query("select avatar from {$this->db->dbprefix('specialist')} where id={$id} limit 1")->row()->avatar;
				@unlink('../upload/specialist/'.$old_avatar);
			}
		}
		if($id == -1)
		{
			$this->db->insert('specialist', $info);
		}
		else
		{
			$this->db->update('specialist', $info, array('id' => $id));
		}
		$this->db->cache_delete_all();
	}
}