<?php
class md_roomextra extends CI_Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
    }

    /**
     * @param string $mode
     * @return mixed
     * 获取直播列表
     */
    function get_roomextra_list($table,$page,$limit,$where = array('where' => ''))
    {
        $start = ($page - 1)*$limit;
        $art_list['data'] = $this->db->query("select * from {$table} where 1=1 {$where['where']} order by id desc limit {$start},{$limit}")->result_array();
        $art_list['count'] = $this->db->query("select count(1) as num from {$table} where 1=1 {$where['where']}")->row()->num;
        return $art_list;
    }

    /**
     * @param $id
     * @return mixed
     * 获取单个直播
     */
    function get_roomextra($id){
        return $this->db->query("select * from {$this->db->dbprefix('room_extra')} where 1=1 and id = {$id}")->row_array();
    }

    /**
     * @param $data
     * @return mixed
     * 添加直播
     */
    function add_roomextra($data){
        $res = $this->db->insert('room_extra', $data);
        return $res;
    }

    /**
     * @param string $id
     * @return mixed
     * 删除直播
     */
    function del_roomextra($id)
    {
        //删除单条数据
        $res = $this->db->simple_query("delete from {$this->db->dbprefix('room_extra')} where id={$id}");
        return $res;
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     * 更新直播
     */
    function update_roomextra($id, $data)
    {
        $this->db->where('id', $id);
        $res = $this->db->update('room_extra', $data);
        return $res;
    }
}