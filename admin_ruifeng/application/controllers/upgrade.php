<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class upgrade extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('base');
		$this->load->database();
	}

	public function index()
	{
		$this->upgrade_list();
	}

	function upgrade_list()
	{
		$tb_group = $this->db->dbprefix('group');
		$this->out_data['group_list'] = $this->db->query("select id,name from {$tb_group}")->result_array();
		$this->out_data['upgrade_list'] = $this->db->query("select u.gid, g.name as gname,u.id,u.name from {$this->db->dbprefix('upgrade')} as u inner join {$tb_group} as g on u.gid=g.id")->result_array();

		$this->out_data['con_page'] = 'upgrade';
		$this->load->view('default', $this->out_data);
	}

	function update_upgrade()
	{
		$info = array('name' => $this->input->post('name'), 'gid' => $this->input->post('gid'));
		$id = $this->input->post('id');
		if($id == 0)
		{
			$this->db->insert('upgrade', $info);
		}
		else
		{
			$this->db->update('upgrade', $info, array('id' => $id));
		}
		$this->db->cache_delete_all();
	}

	function del_upgrade()
	{
		$id = $this->input->post('id');
		$this->db->delete('upgrade', array('id' => $id));
		$this->db->cache_delete_all();
	}
}