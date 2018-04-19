<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class admin extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('admin');
		$this->load->database();
	}

	public function index()
	{
		$this->admin_list();
	}

	function admin_list()
	{
		$this->out_data['admin_list'] = $this->db->select('id,name,nick')->from('admin')->get()->result_array();
		$this->out_data['con_page'] = 'admin_list';
		$this->load->view('default', $this->out_data);
	}

	function del_admin()
	{
		if(!parent::is_post()) show_404();
		$id = (int)$this->input->post('id');
		$this->db->delete('admin', array('id' => $id));
		$this->db->delete('admin_extra', array('aid' => $id));//添加扩展表
	}

	function edit_admin($id = 0)
	{
		$id = (int)$id;

		if($id == 0)
		{
			$this->out_data['admin'] = array('id' => 0, 'name' => '', 'nick' => '', 'permission' => '', 'rid' => '', 'wellcome' => '', 'qq' => '', 'phone' => '', 'intro' => '', 'sex' => '', 'headimg' => '', 'is_hot' => 0, 'solve' => '', 'jietao' => '');//添加扩展表
		}
		else
		{
			//添加扩展表
			$admin = $this->db->select('id,name,nick,permission,wellcome,rid,sex')->from('admin')->where('id', $id)->get()->row_array();
			$admin_extra = $this->db->select('qq,phone,intro,headimg,is_hot,solve,jietao')->from('admin_extra')->where('aid', $id)->get()->row_array();
			if(empty($admin_extra)){
				//如果扩展表中没有这条数据，就插入一条
				$admin_extra = array('aid'=>$id,'qq'=>'','phone'=>'','intro'=>'','headimg' => '', 'is_hot' => 0);
				$this->db->insert('admin_extra',$admin_extra);
			}
			$this->out_data['admin'] = array_merge($admin,$admin_extra);
		}
		
		//头像处理
		$this->load->library('lib_elements');
		$this->out_data['admin']['headimg'] = $this->lib_elements->get_file_element(array('title' => '头像', 'name' => 'headimg', 'img' => '../upload/headimg/'.$this->out_data['admin']['headimg']));
		
		$this->out_data['rid_list'] = $this->db->select('id,name')->from('room')->get()->result_array();
		$this->out_data['con_page'] = 'admin_edit';
		$this->load->view('default', $this->out_data);
	}

	function save_admin()
	{
		if(!parent::is_post()) show_404();
		$result = array('status' => false, 'msg' => '');
		$id = $this->input->post('id');
		$info = array('name' => $this->input->post('name'),
			'wellcome' => $this->input->post('wellcome'),
			'sex' => $this->input->post('sex'),
			'nick' => $this->input->post('nick'));
		/* 扩展表 */
		$info_extra = array('qq' => $this->input->post('qq'),
			'phone' => $this->input->post('phone'),
			'is_hot' => $this->input->post('is_hot'),
			'solve' => $this->input->post('solve'),
			'jietao' => $this->input->post('jietao'),
			'intro' => $this->input->post('intro'));
		
		$is_exist = $this->db->select('id')->from('admin')->where(array('name' => $info['name'], 'id <> '=> $id))->get()->num_rows();
		if($is_exist)
		{
			$result['msg'] = '该账号已存在，请重新输入';
			echo json_encode($result);
			exit;
		}

		$permission = $this->input->post('permission');
		if($permission) $permission = join(',', $permission);
		$info['permission'] = $permission;
		$rid = $this->input->post('rid');
		if($rid) $rid = join(',', $rid);
		$info['rid'] = $rid;
		$password = $this->input->post('password');
		if($password) $info['password'] = md5($password);
		
		//头像处理
		$headimg = $this->input->post('headimg');
		if($headimg){
			$this->load->library('lib_elements');
			$is_upload_avatar = $this->lib_elements->move_img($headimg, '../upload/headimg/'.$headimg);
			if($is_upload_avatar)
			{
				$info_extra['headimg'] = $headimg;
				if($id)
				{
					$old_avatar = $this->db->query("select headimg from {$this->db->dbprefix('admin_extra')} where aid={$id} limit 1")->row()->headimg;
					@unlink('../upload/headimg/'.$old_avatar);
				}
			}
		}

		if($id == 0)
		{
			$this->db->insert('admin', $info);
			//添加扩展表
			$insert_id = $this->db->insert_id();
			$info_extra['aid'] = $insert_id;
			$this->db->insert('admin_extra', $info_extra);
		}
		else
		{
			$this->db->update('admin', $info, array('id' => $id));
			$this->db->update('admin_extra', $info_extra, array('aid' => $id));//添加扩展表
		}
		$result['status'] = true;
		echo json_encode($result);
	}
}