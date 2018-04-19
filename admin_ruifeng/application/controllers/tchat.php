<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class tchat extends MY_Controller{
    function __construct(){
        parent::__construct();
		$this->out_data['current_function'] = 'laoshi';
		$this->out_data['var'] = array();
		$this->out_data['var']['permission'] = $this->session->userdata('permission');
		$this->out_data['var']['permission'] = explode(',', $this->out_data['var']['permission']);
		if(in_array('teacher', $this->out_data['var']['permission'])){
			$this->out_data['var']['name'] = '老师';
			$this->out_data['var']['sendtype'] = 'tchat';
			$this->out_data['var']['deltype'] = 'del_tchat';
		}else{
			$this->out_data['var']['name'] = '打字员';
			$this->out_data['var']['sendtype'] = 'dazichat';
			$this->out_data['var']['deltype'] = 'del_dazichat';
		}
    }

    /**
     * 默认显示前20条数据
     */
    function index(){
        $this->load->database();
        $this->out_data['con_page'] = 'tchat_page';
        //刷新页面，显示数据库中的20条直播数据
        $this->load->model('md_tchat');
        $tb_art1 = $this->db->dbprefix('tchat');
		$identity = $this->out_data['var']['sendtype'];
        $data1 = $this->md_tchat->get_tchat_list($tb_art1,1,20,$identity);
        $this->out_data['tchat'] = $data1['data'];
        $this->load->view('default', $this->out_data);
    }

    /**
     * 老师发送文字直播
     */
    function send_msg(){
        $res = array('msg'=>'','status'=>false,'data'=>array());
        $content = trim($this->input->post('content'));
        $tid = $this->session->userdata('id');
        $handan = (int)$this->input->post('handan');
        if(!$content){
            $res['msg'] = '发送内容不能为空';
            echo json_encode($res);
            exit;
        }
        $info = array(
            'content' => $content,
            'tid' => $tid,
            'ftime' => date("Y-m-d H:i:s"),
            'mtime' => str_pad(str_replace('.', '', microtime(true)),14,0),
			'handan' => $handan
        );
        $info['tname'] = $this->session->userdata('nick');
        $info['identity'] = $this->out_data['var']['sendtype'];
        /**
         * 已经获取到直播内容，下面开始发送直播消息
         * 1.存入数据库2.调用soket
         */
        $this->load->database();
        $this->load->model('md_tchat');
        $resu = $this->md_tchat->add_tchat($info);

        $info['id'] = $resu;
        //通过soket广播出去
        $this->soket_send(json_encode($info),$this->out_data['var']['sendtype']);
        $res['status'] = true;
        $res['data'] = $info;
        echo json_encode($res);
    }

    /**
     * 删除老师发送的聊天信息
     */
    function del(){
        $id = (int)$this->input->post('id');
        /*soket发送删除信息*/
        $data['type'] = $this->out_data['var']['deltype'];
        $data['to'] = '';
        $data['content'] = $id;
        send_websocket($data);
        $this->load->database();
        $this->load->model('md_tchat');
        $resu = $this->md_tchat->del_tchat($id);
        echo $resu;
    }

    /**
     * soket发送信息
     * @param $content string
     */
    protected function soket_send($content = '',$type = 'tchat'){
        $data = array('type'=>$type,'content'=>$content);
        send_websocket($data);
    }
}