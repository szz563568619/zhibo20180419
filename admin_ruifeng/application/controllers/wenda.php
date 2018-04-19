<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class wenda extends MY_Controller{
    function __construct(){
        parent::__construct();
		$this->out_data['current_function'] = 'laoshi';
    }

    /**
     * 默认显示所有数据
     */
    function index(){
		$tname = $this->session->userdata['nick'];
        $this->load->database();
        $this->out_data['wenda'] = $this->db->query("select id,uname,content,uid,tname,gid,is_huida,time,is_show from {$this->db->dbprefix('wenda')} where did = 0 AND tname = '{$tname}' order by time desc")->result_array();
        $this->out_data['con_page'] = 'wenda_list';
        $this->load->view('default', $this->out_data);
    }
	
	function edit_wenda($id){
		$id = (int)$id;
		//先把问题显示出来
		$this->load->database();
		$this->out_data['wenti'] = $this->db->query("select id,uname,content,tid,tname,uid,gid from {$this->db->dbprefix('wenda')} where id = {$id}")->row_array();
		//查询答案
		$this->out_data['daan'] = $this->db->query("select id,uname,content,tid,uid,tname,gid,time from {$this->db->dbprefix('wenda')} where did = {$id}")->result_array();
		$this->out_data['con_page'] = 'wenda_edit';
        $this->load->view('default', $this->out_data);
	}
	
	function set_show($id, $is_show)
	{
		$this->load->database();
		$this->db->update('wenda',array('is_show'=>$is_show),array('id'=>$id));
	}
	
	function save_daan(){
		$res = array('msg'=>'','status'=>false,'data'=>array());
		$info = array(
			'did' => (int)$this->input->post('did'),
			'uid' => (int)$this->input->post('uid'),
			'gid' => (int)$this->input->post('gid'),
			'tid' => (int)$this->session->userdata('id'),
			'tname' => $this->session->userdata('nick'),
			'uname' => $this->input->post('uname'),
			'content' => trim($this->input->post('content')),
			'time' => date("Y-m-d H:i:s")
		);
		if(!$info['content']){
			$res['msg'] = '回答内容不能为空！';
			echo json_encode($res);
			exit;
		}
		$this->load->database();
		//把用户提的问题标记为1，说明这是一个已经有回答的问题
		$this->db->update('wenda',array('is_huida'=>1),array('id'=>$info['did']));
		//把回答插入数据库
		$this->db->insert('wenda',$info);
		$info['id'] = $this->db->insert_id();
		$res['status'] = true;
		$res['data'] = $info;
		echo json_encode($res);
	}
	//删除回答
	function del_da(){
		$id = (int)$this->input->post('id');
		$did = (int)$this->input->post('did');
        $this->load->database();
        $this->db->simple_query("delete from {$this->db->dbprefix('wenda')} where id={$id}");
		$is_empty = $this->db->query("select count(1) as num from {$this->db->dbprefix('wenda')} where did = {$did}")->row()->num;
		if(!$is_empty){
			//如果答案条数是空的，就把问题中的is_huida归零
			$this->db->update('wenda',array('is_huida'=>0),array('id'=>$did));
		}
	}
	//删除问题，和问题下面的回答
	function del_wen($id){
		$id = (int)$id;
        $this->load->database();
        $this->db->simple_query("delete from {$this->db->dbprefix('wenda')} where id={$id} OR did={$id}");
	}
}