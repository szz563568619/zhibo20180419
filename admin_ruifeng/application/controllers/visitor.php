<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class visitor extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('customer,seo');
	}

	public function index()
	{
		$this->visitor_list();
	}

	/*游客管理页面*/
	function visitor_list()
	{
		$this->load->database();
		$dtime = date("Y-m-d");
		// $this->out_data['visitor_list'] = $this->_get_visitor_list();
		$page = $this->input->get('per_page') ? $this->input->get('per_page') : 1;
		$limit = 20;
		$start = ($page - 1)*$limit;
		$count = $this->db->query("select count(1) as num from {$this->db->dbprefix('visitor')} where DATE_FORMAT(time,'%Y-%m-%d') = '{$dtime}'")->row()->num;
		$this->out_data['visitor_list'] = $this->db->query("select id,name,keyword,ip,source,is_talk,time from {$this->db->dbprefix('visitor')} where DATE_FORMAT(time,'%Y-%m-%d') = '{$dtime}' order by time desc limit {$start},{$limit}")->result_array();
		$base_url = base_url().'visitor/?';
		$this->out_data['pagin'] = parent::get_pagin($base_url, $count, $limit, 3,  true);
		$this->out_data['con_page'] = 'visitor_list';
		$this->load->view('default', $this->out_data);
	}
	
	// function del_visitor_istalk(){
	// 	$id = (int)$this->input->post('id');
	// 	$this->load->database();
	// 	$this->db->delete('visitor_istalk', array('id' => $id));
	// }
	
	/* 用户信息标记成有用或无用 */
	// function set_valuable(){
	// 	$id = (int)$this->input->post('id');
	// 	$is_valuable = (int)$this->input->post('is_valuable');
	// 	$is_valuable = $is_valuable?0:1;
	// 	$this->load->database();
	// 	$this->db->update('visitor_istalk', array('is_valuable' => $is_valuable), array('id' => $id));
	// }

	/*给游客设置备注*/
	// function set_remark()
	// {
	// 	$this->load->database();
	// 	$id = $this->input->post('id');
	// 	$remark = $this->input->post('remark');
	// 	$this->db->update('visitor', array('remark' => $remark), array('id' => $id));
	// }

	/*某游客的对话记录*/
	function chat_list($id, $gid)
	{
		$this->load->database();
		//$this->out_data['chat_list'] = $this->db->query("select is_visitor,send_name,time,content from {$this->db->dbprefix('visitor_chat')} where mid={$id} and gid={$gid} and aid={$this->session->userdata('id')} order by time desc")->result_array();
		$this->out_data['chat_list'] = $this->db->query("select is_visitor,send_name,time,content from {$this->db->dbprefix('visitor_chat')} where mid={$id} and gid={$gid} order by time desc")->result_array();
		$this->out_data['visitor'] = $this->db->query("select name from {$this->db->dbprefix('visitor')} where id = '{$id}'")->row_array();
		$this->out_data['con_page'] = 'visitor_chat_list';
		$this->load->view('default', $this->out_data);
	}
	
	/*某游客的对话记录*/
	function minichat_list($id, $gid)
	{
		$this->load->database();
		$this->out_data['chat_list'] = $this->db->query("select is_visitor,send_name,time,content from {$this->db->dbprefix('visitor_chat')} where mid={$id} and gid={$gid} and aid={$this->session->userdata('id')} order by time desc")->result_array();
		$this->load->view('minivisitor_chat_list', $this->out_data);
	}
	
	/*某游客的对话记录*/
	function minichat_msg($id, $gid)
	{
		$this->load->database();
		$chat_list = $this->db->query("select is_visitor,send_name,time,content,click_resource from {$this->db->dbprefix('visitor_chat')} where mid={$id} and gid={$gid} and aid={$this->session->userdata('id')}  order by time asc")->result_array();
		echo json_encode($chat_list);
	}

	function im()
	{
		$this->out_data['con_page'] = 'visitor_im';
		$this->load->view('default', $this->out_data);
	}
	
	/*用于前台显示的会话页面*/
	function front_im()
	{
		$this->load->view('visitor_im_front', $this->out_data);
	}

	function im_page()
	{
		$socket = $this->config->item('socket');
		$this->out_data['socket_port'] = $socket['receive_port'];
		$this->out_data['socket_url'] = $socket['url'];
		$this->load->view('im/index', $this->out_data);
	}

	/*游客会话页面获取在线游客列表*/
	function get_online_visitor()
	{
		// $redis = parent::redis_conn();
		// $user_list = $redis->keys('member_list_*');

		$user_list = explode(',', $this->input->post('user_list'));

		$member_list = array(); /*用户保存在线用户*/
		$visitor_list = array(); /*保存在线游客*/
		foreach($user_list as $k => $v)
		{
			if($v != '')
			{	
				$id = explode('_', $v);
				if(isset($id[1]))
				{
					$gid = $id[1];
					if($gid == 1) $visitor_list[$k] = "'".$id[0]."'";
					else $member_list[$k] = "'".$id[0]."'";
				}else{
					file_put_contents('user_list.txt', $v."\r\n", FILE_APPEND);
				}
			}
		}
		if(empty($visitor_list) AND empty($member_list))
		{
			echo json_encode(array());
		}
		else
		{
			$this->load->database();
			if(!empty($visitor_list)) $visitor_list = $this->db->query("select id,name,source,remark,keyword from {$this->db->dbprefix('visitor')} where id IN(".join(',', $visitor_list).")")->result_array();
			if(!empty($member_list)) $member_list = $this->db->query("select id,name,gid,source,keyword from {$this->db->dbprefix('member')} where id IN(".join(',', $member_list).")")->result_array();
			foreach($visitor_list as $k => $v)
			{
				$visitor_list[$k]['gid'] = 1;
			}
			$visitor_list = array_merge($member_list, $visitor_list);
			echo json_encode($visitor_list);
		}
	}

	/*获取某个游客的信息*/
	function get_visitor_info($id, $gid)
	{
		$this->load->database();
		if($gid == 1)
		{
			$info = $this->db->query("select id,name,source,remark from {$this->db->dbprefix('visitor')} where id = {$id} limit 1")->row_array();
		}
		else
		{
			$info = $this->db->query("select id,name from {$this->db->dbprefix('member')} where id = {$id} limit 1")->row_array();
		}
		$info['gid'] = $gid;
		echo json_encode($info);
	}

	/*发送聊天信息*/
	function send_msg()
	{
		/*下面准备开始发送信息了*/
		$id = $this->session->userdata('id');
		$this->load->database();
		$sex = $this->db->query("select sex from {$this->db->dbprefix('admin')} where id = {$id} limit 1")->row()->sex;
		$mid = $this->input->post('visitor_id'); /*发送的用户ID*/
		$gid = $this->input->post('gid'); /*发送的用户组ID*/
		$info = array('mid' => $mid, 'aid' => $id, 'gid' => $gid, 'is_visitor' => 0, 'time' => date('Y-m-d H:i:s'), 'content' => $this->input->post('content'), 'send_name' => $this->session->userdata('nick'), 'sex' => $sex);
		send_websocket(array('type' => 'private_msg', 'to' => $mid.'_'.$gid.'|admin_'.$id, 'content' => json_encode($info)));
		$this->load->database();
		unset($info['sex']);
		$this->db->insert('visitor_chat', $info);
	}
	
	/* 搜索功能 */
	function search(){
		$start_time = urldecode($this->input->get('start'));
		$end_time = urldecode($this->input->get('end'));
		$start_time = empty($start_time)?date("Y-m-d").' 00:00:00':$start_time.' 00:00:00';
		$end_time = empty($end_time)?date("Y-m-d H:i:s"):$end_time.' 23:59:59';
		$is_talk = (int)$this->input->get('is_talk');
		$is_talk_sql = $is_talk ? " AND is_talk = 1 " : '';
		if((strtotime($end_time) < strtotime($start_time)))
		{
			echo "<script>alert('结束时间必须大于起始时间！');location.href = '".base_url()."visitor';</script>";
			//redirect("phone",'最终时间必须大于起始时间!!!');
			exit;
		}
		$this->load->database();
		$page = $this->input->get('per_page') ? $this->input->get('per_page') : 1;
		$limit = 20;
		$start = ($page - 1)*$limit;
		$count = $this->db->query("select count(1) as num from {$this->db->dbprefix('visitor')} where time between '{$start_time}' and '{$end_time}' {$is_talk_sql}")->row()->num;
		$this->out_data['visitor_list'] = $this->db->query("select id,name,keyword,ip,source,is_talk,time from {$this->db->dbprefix('visitor')} where time between '{$start_time}' and '{$end_time}' {$is_talk_sql}  order by time desc limit {$start},{$limit}")->result_array();
		$base_url = base_url().'visitor/search/?&start=' . date("Y-m-d",strtotime($start_time)) . '&end=' . date("Y-m-d",strtotime($end_time)) . '&is_talk=' . $is_talk;
		$this->out_data['pagin'] = parent::get_pagin($base_url, $count, $limit, 3,  true);
		$this->out_data['start'] = date("Y-m-d",strtotime($start_time));
		$this->out_data['end'] = date("Y-m-d",strtotime($end_time));
		$this->out_data['is_talk'] = $is_talk;
		$this->out_data['con_page'] = 'visitor_list';
		$this->load->view('default', $this->out_data);
	}
	
	//根据查询时间要求导出成excel
	function for_excel()
	{
		$start_time = $this->input->post('start_time');	
		$end_time = $this->input->post('end_time');
		$is_talk = (int)$this->input->post('is_talk');
		$result = array('status' => false, 'msg' => '');
		$start_time = empty($start_time)?date("Y-m-d").' 00:00:00':$start_time.' 00:00:00';
		$end_time = empty($end_time)?date("Y-m-d H:i:s"):$end_time.' 23:59:59';
		$is_talk_sql = $is_talk ? " AND is_talk = {$is_talk}" : '';
		
		if((strtotime($end_time) < strtotime($start_time)))
		{
			echo "<script>alert('结束时间必须大于起始时间！');location.href = '".base_url()."visitor';</script>";
			//redirect("phone",'最终时间必须大于起始时间!!!');
			exit;
		}
		$this->load->database();
		$inner = $this->db->query("select id,name,keyword,ip,source,is_talk,time from {$this->db->dbprefix('visitor')} where time between '{$start_time}' and '{$end_time}' {$is_talk_sql}")->result_array();
		//调用很少，生成excel表格
		$this->put_for_excel($inner,$result,$start_time,$end_time);

	}

	//将数组中的数据生成excel表
	public function put_for_excel($arr,$result,$start_time,$end_time)
	{
		require_once 'PHPExcel.php';  
		include "PHPExcel/Writer/IWriter.php";   
		include "PHPExcel/Writer/Excel5.php";   
		include 'PHPExcel/IOFactory.php';   
		//上面的四句代码是引入所需要的库， 
		$objPHPExcel = new PHPExcel();
		$a1 = '域名';  //这是两个标头  就是列名，最上面的那个  
		$a2 = '来源';  //这是两个标头  就是列名，最上面的那个  
		$a3 = '游客名';
		$a4 = '关键字';
		$a5 = '时间';
		$a6 = '是否对话';
		$time = '查询起始时间: ' . $start_time . '--------- 截止时间: ' . $end_time;

		//$a1=iconv("utf-8","gb2312",$a1);  //如果是乱码的话，则需要转换下  
		//$a2=iconv("utf-8","gb2312",$a2);  
		$objPHPExcel->getActiveSheet()->setCellValue('a1', $time);//设置列的值  
		$objPHPExcel->getActiveSheet()->setCellValue('a4', $a1);//设置列的值  
		$objPHPExcel->getActiveSheet()->setCellValue('b4', $a2);  
		$objPHPExcel->getActiveSheet()->setCellValue('c4', $a3);
		$objPHPExcel->getActiveSheet()->setCellValue('d4', $a4);
		$objPHPExcel->getActiveSheet()->setCellValue('e4', $a5);
		$objPHPExcel->getActiveSheet()->setCellValue('f4', $a6);
		$i = 5; //自增变量，用来控制行，因为标头占的第一行，所以这里从第二行开始  
		foreach($arr as $v)
		{
			$ip = $v['ip']; 
			$source = $v['source']; 
			$name = $v['name']; 
			$keyword = $v['keyword'];
			$time = $v['time']; 
			$is_talk = $v['is_talk'] ? '是' : '否';
			//$i++;   
			//$id=iconv("utf8","gb2312",$id);  
			//$cname = iconv("utf8","gb2312",$cname);  
			$objPHPExcel->getActiveSheet()->setCellValue('a'.$i, $ip);  
			$objPHPExcel->getActiveSheet()->setCellValue('b'.$i, $source);  
			$objPHPExcel->getActiveSheet()->setCellValue('c'.$i, $name);  
			$objPHPExcel->getActiveSheet()->setCellValue('d'.$i, $keyword);  
			$objPHPExcel->getActiveSheet()->setCellValue('e'.$i, $time);  
			$objPHPExcel->getActiveSheet()->setCellValue('f'.$i, $is_talk);
			$i++;     
		}  
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);//设置宽度  
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);  
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);  
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);  
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);  
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);  
		
		//设置字体，颜色等
		$styleArray1 = array(
		  'font' => array(
			'bold' => true,
			'size'=>12,
			'color'=>array(
			  'argb' => '0gfd000d',
			),
		  ),
		  'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		  ),
		);
		// 将A1单元格设置为加粗，居中
		$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('B4')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('C4')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('D4')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('E4')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('F4')->applyFromArray($styleArray1);
		  
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  //创建表格类型，目前支持老版的excel5,和excel2007,也支持生成html,pdf,csv格式  
		
		$jifen_name = 'keywords__' . date('Y-m-d',time()) . '-' . date('H-i',time()). '.xls';
		$jifen_path = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']).'file/keywords/' . $jifen_name;
		
		$objWriter->save($jifen_path);//保存生成  
		
		if(file_exists($jifen_path))
		{
			//ajax返回参数
			$result['status'] = true;
			$result['msg'] = base_url().'visitor/put_excel_download/?file=' . $jifen_name;
			echo json_encode($result);
		}
	}
	//生成的excel下载
	public function put_excel_download(){
		$file = $this->input->get('file');
		$file = base_url().'file/keywords/'.$file;
		header('Content-Description: File Download');
		header('Content-type: application.octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		//header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
	}

}