<?php
class md_tchat extends CI_Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /**
     * @param string $mode
     * @return mixed
     * 获取讲师列表
     */
    function get_tchat_list($table,$page,$limit,$identity)
    {
        $start = ($page - 1)*$limit;
        $art_list['data'] = $this->db->query("select * from {$table} where 1=1 AND identity = '{$identity}' and tid = '{$this->session->userdata('id')}' order by ftime desc limit {$start},{$limit}")->result_array();
        $art_list['count'] = $this->db->query("select count(1) as num from {$table} where 1=1 AND identity = '{$identity}'")->row()->num;
        return $art_list;
    }

    /**
     * @param $id
     * @return mixed
     * 获取制定讲师
     */
    function get_tchat($id){
        return $this->db->query("select * from {$this->db->dbprefix('tchat')} where 1=1 and id = {$id}")->row_array();
    }

    /**
     * @param $data
     * @return mixed
     * 添加讲师
     */
    function add_tchat($data){
        $res = $this->db->insert('tchat', $data);
        return $this->db->insert_id();
    }

    /**
     * @param string $id
     * @return mixed
     * 删除讲师
     */
    function del_tchat($id)
    {
        //删除单条数据
        $res = $this->db->simple_query("delete from {$this->db->dbprefix('tchat')} where id={$id}");
        return $res;
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     * 更新讲师信息
     */
    function update_tchat($id, $data)
    {
        $this->db->where('id', $id);
        $res = $this->db->update('tchat', $data);
        return $res;
    }
}