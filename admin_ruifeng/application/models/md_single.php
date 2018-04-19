<?php

class md_single extends CI_Model {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	function get_page_list()
	{
		return $this->db->query("select id,title,url,flag from {$this->db->dbprefix('single_page')}")->result_array();
	}

	function del_page($id)
	{
		$this->db->simple_query("delete from {$this->db->dbprefix('single_page')} where id={$id}");
	}

	function get_page($page_id)
	{
		return $this->db->query("select * from {$this->db->dbprefix('single_page')} where id={$page_id} limit 1")->row_array();
	}

	function save_page($id, $info)
	{
		$result = array('status' => false, 'msg' => '', 'id' => $id);
		if($id === 0)
		{
			$this->db->insert('single_page', $info);
			$result['status'] = true;
			$result['id'] = $this->db->insert_id();
		}
		else
		{
			$this->db->where('id', $id);
			$result['status'] = true;
			$this->db->update('single_page', $info);
		}
		return $result;
	}

}