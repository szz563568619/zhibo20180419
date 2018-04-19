<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class examine extends MY_Controller {

    // private $redis;
    function __construct()
    {
        parent::__construct();
        parent::check_permission('examine');
        $this->out_data['current_function'] = '';
        // $this->redis = parent::redis_conn();
    }

    private function _get_rid_list()
    {
        return explode(',', $this->session->userdata('rid'));
    }

    public function index()
    {
        $this->load->database();

        //是否审核
        $this->out_data['room_info'] = $this->db->query("select auto_examine from {$this->db->dbprefix('room')} where id = '001'")->row_array();

        // $this->out_data['rid_list'] = $this->_get_rid_list();
        $this->out_data['chat_list'] = $this->db->query("select id,rid,name,time,content,score,gid from {$this->db->dbprefix('chat_list_examine')}")->result_array(); /*未审核的聊天记录*/


        $socket = $this->config->item('socket');
        $this->out_data['socket_port'] = $socket['receive_port'];
        $this->out_data['socket_url'] = $socket['url'];

        $this->out_data['con_page'] = 'examine';
        $this->load->view('default', $this->out_data);
    }

    function update_room_examine()
    {
        $roome_examine = $this->input->post('auto_examine');
        $this->db->update('room', array('auto_examine' => $roome_examine), array('id' => '001'));
        $redis = parent::redis_conn();
        $redis->set('room_examine_001', $roome_examine);
    }

    /**
     * 删除聊天记录
     */
    function del()
    {
        $id = $this->input->post('id');
        if( ! is_array($id) ) $ids[] = $id;
        else $ids = $id;

        $this->load->database();
        $this->db->query("delete from {$this->db->dbprefix('chat_list_examine')} where score IN (".join(',', $ids).")");

    }

    /*发布聊天记录*/
    function release()
    {
        $this->load->database();
        $id = $this->input->post('id');
        if( ! is_array($id) ) $ids[] = $id;
        else $ids = $id;

        $tb_examine = $this->db->dbprefix('chat_list_examine');
        $tb_chat_list = $this->db->dbprefix('chat_list');
        $chat_list = $this->db->query("select rid,gid,name,time,content,types,score from {$tb_examine} where score IN(".join(',', $ids).")")->result_array();
        foreach($chat_list as $v)
        {
            $v['time'] = date('Y-m-d H:i:s');
            send_websocket(array('type' => 'public_msg', 'content' => json_encode($v)));
            $this->db->insert($tb_chat_list, $v);
        }
        $this->db->query("delete from {$tb_examine} where score in(".join(',', $ids).")");

    }

}