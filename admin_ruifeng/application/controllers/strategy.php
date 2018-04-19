<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class strategy extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('base');
		$this->load->database();
	}

	public function index()
	{
		$this->strategy_list();
	}

	function strategy_list($room_id = '')
	{
		$this->out_data['room_list'] = $this->db->query("select id,name from {$this->db->dbprefix('room')}")->result_array();
		if($room_id == '') $room_id = $this->out_data['room_list'][0]['id'];

		$tb_group = $this->db->dbprefix('group');
		$this->out_data['group_list'] = $this->db->query("select id,name from {$tb_group}")->result_array();

		$tb_teacher = $this->db->dbprefix('teacher');
		$this->out_data['teacher_list'] = $this->db->query("select id,name from {$tb_teacher}")->result_array();

		// $this->out_data['strategy_list'] = $this->db->query("select s.*,g.name as gname,t.name as tname from {$this->db->dbprefix('strategy')} as s inner join {$tb_group} as g on s.gid=g.id left join {$tb_teacher} as t on s.tid=t.id where s.rid='{$room_id}'")->result_array();
		$this->out_data['strategy_list'] = $this->db->query("select s.*,g.name as gname,t.name as tname from {$this->db->dbprefix('strategy')} as s inner join {$tb_group} as g on s.gid=g.id left join {$tb_teacher} as t on s.tid=t.id where s.rid='{$room_id}' order by s.sort desc ")->result_array();

		$this->out_data['room_id'] = $room_id;
		$this->out_data['con_page'] = 'strategy';
		$this->load->view('default', $this->out_data);
	}

	function update_strategy()
	{
		$strategy_id = $this->input->post('strategy_id');
		$name = $this->input->post('name');
		$title = $this->input->post('title');
		$gid = $this->input->post('gid');
		$tid = $this->input->post('tid');
		$position = $this->input->post('position');
		$profit = $this->input->post('profit');
		$stop = $this->input->post('stop');
		$reason = $this->input->post('reason');
		$time = $this->input->post('time');
		$room_id = $this->input->post('room_id');
		foreach($strategy_id as $k => $v)
		{
			if($name[$k] != '')
			{
				$info = array('name' => $name[$k],
					'title' => $title[$k],
					'gid' => $gid[$k],
					'tid' => $tid[$k],
					'position' => $position[$k],
					'profit' => $profit[$k],
					'stop' => $stop[$k],
					'reason' => $reason[$k],
					'time' => $time[$k],
					'rid' => $room_id);
				if($strategy_id[$k] != 0)
				{
					$this->db->update('strategy', $info, array('id' => $strategy_id[$k]));
				}
				else
				{
					$this->db->insert('strategy', $info);
				}
			}
		}
		$this->db->cache_delete_all();
	}

	function del_strategy()
	{
		$id = $this->input->post('id');
		$this->db->delete('strategy', array('id' => $id));
		$this->db->cache_delete_all();
	}
}