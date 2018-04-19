<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class source extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('admin,seo');
		$this->load->database();
	}

	public function index()
	{
		$this->source_list();
	}

	function source_list()
	{
		$this->out_data['con_page'] = 'source_list';
		$this->out_data['source_list'] = $this->db->query("select id,host,source from {$this->db->dbprefix('source')}")->result_array();
		$this->load->view('default', $this->out_data);
	}

	function edit_source($source_id = 0)
	{
		$info = $this->db->query("select id,host,source from {$this->db->dbprefix('source')} where id='{$source_id}' limit 1");
		if($info->num_rows() > 0)
		{
			$this->out_data['source_info'] = $info->row_array();
		}
		$this->out_data['con_page'] = 'source_edit';
		$this->load->view('default', $this->out_data);
	}

	function del_source()
	{
		$tb_source = $this->db->dbprefix('source');
		$source_id = $this->input->post('id');
		$this->db->delete($tb_source, array('id' => $source_id));
	}

	function update_source()
	{
		$id = $this->input->post('id');
		$info = array('host' => $this->input->post('host'), 'source' => $this->input->post('source'));
		if($id == 0)
		{
			$this->db->insert('source', $info);
		}
		else
		{
			$this->db->update('source', $info, array('id' => $id));
		}
	}
}