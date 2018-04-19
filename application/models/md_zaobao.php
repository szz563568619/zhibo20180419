<?php

class md_zaobao extends CI_Model {
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
	function add_zaobao($data){
        $res = $this->db->insert('zaobao', $data);
        return $res;
    }

    /**
     * @param string $id
     * @return mixed
     * 删除文章
     */
	function del_zaobao($id = '')
	{
        if($id){
            //删除单条数据
            $res = $this->db->simple_query("delete from {$this->db->dbprefix('zaobao')} where id={$id}");
        }
        return $res;
	}

    /**
     * @param $id
     * @param $data
     * @return mixed
     * 修改文章
     */
	function update_zaobao($id, $data)
	{
        $this->db->where('id', $id);
        $res = $this->db->update('zaobao', $data);
		return $res;
	}

    function get_zaobao_list($table,$where = '',$page,$limit)
    {
		$query_search = '';
        $start = ($page - 1)*$limit;
        $zaobao_list['data'] = $this->db->query("select * from {$table}{$query_search} order by time desc limit {$start},{$limit}")->result_array();
        $zaobao_list['count'] = $this->db->query("select count(1) as num from {$table} {$query_search}")->row()->num;
        return $zaobao_list;
    }

    function get_zaobao($id)
    {
        return $this->db->query("select * from {$this->db->dbprefix('zaobao')} where id={$id} limit 1")->row_array();
    }

}