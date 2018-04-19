<?php

class md_tactics extends CI_Model {
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
    /**
     * @param $data
     * @return mixed
     * 添加文章
     */
	function add_tactics($data){
        $res = $this->db->insert('tactics', $data);
        return $res;
    }

    /**
     * @param string $id
     * @return mixed
     * 删除文章
     */
	function del_tactics($id = '')
	{
        if($id){
            //删除单条数据
            $res = $this->db->simple_query("delete from {$this->db->dbprefix('tactics')} where id={$id}");
        }
        return $res;
	}

    /**
     * @param $id
     * @param $data
     * @return mixed
     * 修改文章
     */
	function update_tactics($id, $data)
	{
        $this->db->where('id', $id);
        $res = $this->db->update('tactics', $data);
		return $res;
	}

    function get_tactics_list($table,$where = '',$page,$limit)
    {
        $start = ($page - 1)*$limit;
        $tactics_list['data'] = $this->db->query("select id,tid,title,intro,create_time,fname from {$table}{$where} order by create_time desc limit {$start},{$limit}")->result_array();
        $tactics_list['count'] = $this->db->query("select count(1) as num from {$table} {$where}")->row()->num;
        return $tactics_list;
    }

    function get_tactics($id)
    {
        return $this->db->query("select id,tid,title,intro,create_time,fname from {$this->db->dbprefix('tactics')} where id={$id} limit 1")->row_array();
    }

}