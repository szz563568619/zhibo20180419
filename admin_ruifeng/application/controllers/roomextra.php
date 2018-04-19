<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class roomextra extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        parent::check_permission('admin');
        $this->load->database();
        $this->load->model('md_roomextra');
    }

    public function index()
    {
        //ALTER TABLE `zhibo_room_extra` ADD `title` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '优化标题' , ADD `keywords` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '优化关键词' , ADD `description` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '优化描述' ;
        $this->roomextra_list();
    }

    function roomextra_list()
    {
        $page = $this->input->get('per_page') ? $this->input->get('per_page') : 1;
        $tb_art = $this->db->dbprefix('room_extra');
        $limit = 20;
        $data = $this->md_roomextra->get_roomextra_list($tb_art,$page,$limit);
        $base_url = base_url().'roomextra/roomextra_list/?per_page=' . $page;
        $this->out_data['pagin'] = parent::get_pagin($base_url, $data['count'], $limit, 3,  true);
        $this->out_data['data'] = $data['data'];
        $this->out_data['con_page'] = 'roomextra_list';
        $this->load->view('default', $this->out_data);
    }

    function del_roomextra()
    {
        if(!parent::is_post()) show_404();
        $id = (int)$this->input->post('id');
        $this->md_roomextra->del_roomextra($id);
		$this->db->cache_delete_all();
    }

    function edit_roomextra($id = 0)
    {
        $id = (int)$id;
        if($id != 0)
        {
            $this->out_data['roomextra'] = $this->md_roomextra->get_roomextra($id);
        }
        $this->out_data['con_page'] = 'roomextra_edit';
        $this->load->view('default', $this->out_data);
    }

    function save_roomextra()
    {
        if(!parent::is_post()) show_404();
        $result = array('status' => false, 'msg' => '');
        $id = $this->input->post('id');
        $info = array(
            'domain' => trim($this->input->post('domain')),
            'title' => trim($this->input->post('title')),
            'keywords' => trim($this->input->post('keywords')),
            'description' => trim($this->input->post('description')),
            'info' => trim($this->input->post('info'))
        );
        if(empty($info['domain'])){
            $result['msg'] = '域名不能为空！';
            echo json_encode($result);
            exit;
        }
		if(empty($info['info'])){
            $result['msg'] = '底部信息不能为空！';
            echo json_encode($result);
            exit;
        }

        if(empty($info['title'])){
            $result['msg'] = '优化标题不能为空！';
            echo json_encode($result);
            exit;
        }

        if(empty($info['keywords'])){
            $result['msg'] = '优化关键词不能为空！';
            echo json_encode($result);
            exit;
        }

        if(empty($info['description'])){
            $result['msg'] = '优化描述不能为空！';
            echo json_encode($result);
            exit;
        }

        if($id == 0)
        {
            $this->md_roomextra->add_roomextra($info);
        }
        else
        {
            $this->md_roomextra->update_roomextra($id, $info);
        }
		$this->db->cache_delete_all();
        $result['status'] = true;
        echo json_encode($result);
    }
}