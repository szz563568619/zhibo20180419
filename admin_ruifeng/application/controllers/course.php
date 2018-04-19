<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class course extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('base');
		$this->out_data['current_function'] = 'course';
		$this->load->database();
	}

	public function index()
	{
		$this->curriculum();
	}

	function teacher()
	{
		$this->out_data['teacher'] = $this->db->query("select * from {$this->db->dbprefix('teacher')}")->result_array();
		$this->out_data['con_page'] = 'teacher';
		$this->load->view('default', $this->out_data);
	}

	function del_teacher()
	{
		$id = $this->input->post('id');
		$this->db->delete('teacher', array('id' => $id));
		$this->db->cache_delete_all();
	}

	function set_teacher()
	{
		$id = $this->input->post('id');
		$name = $this->input->post('name');
		if($id == 0)
		{
			$this->db->insert('teacher', array('name' => $name));
		}
		else
		{
			$this->db->update('teacher', array('name' => $name), array('id' => $id));
		}
		$this->db->cache_delete_all();
	}

	function curriculum($room_id = '')
	{
		$this->out_data['room_list'] = $this->db->query("select id,name from {$this->db->dbprefix('room')}")->result_array();
		if($room_id == '') $room_id = $this->out_data['room_list'][0]['id'];

		$this->out_data['curriculum_list'] = $this->db->query("select * from {$this->db->dbprefix('curriculum')} where rid = '{$room_id}' order by id")->result_array();

		$this->out_data['teacher'] = $this->db->query("select * from {$this->db->dbprefix('teacher')}")->result_array();

		$this->out_data['room_id'] = $room_id;

		$this->out_data['con_page'] = 'curriculum';
		$this->load->view('default', $this->out_data);
	}

	function del_curriculum()
	{
		$id = $this->input->post('id');
		$this->db->delete('curriculum', array('id' => $id));
		$this->db->cache_delete_all();
	}

	function update_curriculum()
	{
		$start_time = $this->input->post('start_time');
		$end_time = $this->input->post('end_time');
		$curr_name = $this->input->post('curr_name');
		$monday = $this->input->post('monday');
		$tuesday = $this->input->post('tuesday');
		$wednesday = $this->input->post('wednesday');
		$thursday = $this->input->post('thursday');
		$friday = $this->input->post('friday');
		$curriculum_id = $this->input->post('curriculum_id');
		$room_id = $this->input->post('room_id');
		foreach($start_time as $k => $v)
		{
			if($start_time[$k] != '' AND $end_time[$k] != '')
			{
				$info = array('start_time' => str_pad(str_replace('：', ':', $start_time[$k]), 5, '0', STR_PAD_LEFT),
					'end_time' => str_pad(str_replace('：', ':', $end_time[$k]), 5, '0', STR_PAD_LEFT),
					'curr_name' => trim($curr_name[$k]),
					'monday' => $monday[$k],
					'tuesday' => $tuesday[$k],
					'wednesday' => $wednesday[$k],
					'thursday' => $thursday[$k],
					'friday' => $friday[$k],
					'rid' => $room_id);
				if($curriculum_id[$k] != 0)
				{
					$this->db->update('curriculum', $info, array('id' => $curriculum_id[$k]));
				}
				else
				{
					$this->db->insert('curriculum', $info);
				}
			}
		}
		$this->db->cache_delete_all();
	}

	public function data_list()
	{
		$this->out_data['data_list'] = $this->db->query("select * from {$this->db->dbprefix('course_data')} order by week asc")->result_array();
		$this->out_data['con_page'] = 'data_list';
		$this->load->view('default', $this->out_data);
	}

	function del_data()
	{
		$id = $this->input->post('id');
		$this->db->delete('course_data', array('id' => $id));
		$this->db->cache_delete_all();
	}

	function update_data()
	{
		$info = $this->input->post('info');
		$week = $this->input->post('week');
		$ids = $this->input->post('id');
		foreach($info as $k => $v)
		{
				$res = array('info' => $info[$k],
					'week' => $week[$k]);
				if($ids[$k] != 0)
				{
					$this->db->update('course_data', $res, array('id' => $ids[$k]));
				}
				else
				{
					$this->db->insert('course_data', $res);
				}
		}
		$this->db->cache_delete_all();
	}
}