<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class user extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function index()
	{
		$this->edit();
	}

	function edit()
	{
		$this->out_data['info'] = $this->db->select('name,nick,wellcome')->from('admin')->where('id', $this->session->userdata('id'))->get()->row_array();
		$this->out_data['con_page'] = 'edit_my';
		$this->load->view('default', $this->out_data);
	}

	function save_user()
	{
		$result = array('status' => false, 'msg' => '');
		$name = $this->input->post('name');
		$nick = $this->input->post('nick');
		$wellcome = $this->input->post('wellcome');
		$password = $this->input->post('password');
		$id = $this->session->userdata('id');
		$is_exist = $this->db->select('id')->from('admin')->where("id <> {$id} and name='{$name}'")->get()->num_rows();
		if($is_exist > 0)
		{
			$result['msg'] = '该用户名已存在，请重新输入';
		}
		else
		{
			$update = array('name' => $name, 'nick' => $nick);
			if( !empty($password) )
			{
				$update['password'] = md5($password);
			}
			if($wellcome){
				$update['wellcome'] = $wellcome;
			}

			$this->db->update('admin', $update, "id = {$id}");
			$result['status'] = true;
			$result['msg'] = '保存成功';
			$this->session->set_userdata('nick', $nick);
		}
		echo json_encode($result);
	}
}