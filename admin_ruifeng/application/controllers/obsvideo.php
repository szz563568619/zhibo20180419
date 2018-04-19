<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class obsvideo extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('admin');
		$this->load->database();
	}

	public function index()
	{
		$info = $this->db->query("select id,is_obs_video from {$this->db->dbprefix('room')} where id='001' limit 1");
		if($info->num_rows() > 0)
		{
			$this->out_data['room_info'] = $info->row_array();
		}
		$this->out_data['con_page'] = 'obsvideo_edit';
		$this->load->view('default', $this->out_data);
	}


	function update_room()
	{
		$result = array('status' => false, 'msg' => '');

		$info = array('id' => '001',
			'is_obs_video' => $this->input->post('is_obs_video')
			);

		$tb_room = $this->db->dbprefix('room');
		
		/* 发送使用哪种直播代码 */
		send_websocket(array('type' => 'is_obs_video', 'content' => $info['is_obs_video']));
		echo json_encode($result);
	}
}