<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class keywords extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('base');
	}

	public function index()
	{
		$this->out_data['con_page'] = 'keywords_list';
		$this->load->view('default', $this->out_data);
	}

	//分页查看
	public function keywords_list()
	{
		$this->load->database();
		$start_time = $this->input->get('start');	
		$end_time = $this->input->get('end');
		
		$result = array('status' => false, 'msg' => '', 'val' => '');
		
		if(empty($start_time) || empty($end_time))
		{
			redirect("/keywords",'选择时间范围不能为空!!!');
			exit;
		}

		if(!(strtotime($end_time) >= strtotime($start_time)))
		{
			redirect("/keywords",'最终时间必须大于起始时间!!!');
			exit;
		}
		$page = $this->input->get('per_page') ? $this->input->get('per_page') : 1;
		$search = $this->input->get('search');
		$query_search = " where time between '{$start_time}' and '{$end_time}' ";

		$tb_chat_list = $this->db->dbprefix('qq_click');
		$limit = 50;
		$start = ($page - 1)*$limit;
		$this->out_data['keywords_list'] = $this->db->query("select id,time,keywords,state from {$tb_chat_list}{$query_search} order by time desc limit {$start},{$limit}")->result_array();

		$query_count = "select count(1) as num from {$tb_chat_list} {$query_search}";
		$count = $this->db->query($query_count)->row()->num;
		$base_url = base_url().'keywords/keywords_list/?&start=' . $start_time . '&end=' . $end_time;
		$this->out_data['start'] = $start_time;
		$this->out_data['end'] = $end_time;
		$this->out_data['pagin'] = parent::get_pagin($base_url, $count, $limit, 3,  true);
		$this->out_data['count'] = $count;
		$this->out_data['search'] = $search;
		$this->out_data['con_page'] = 'keywords_list';
		$this->load->view('default', $this->out_data);
	}


	//根据查询时间要求导出成excel
	function for_excel()
	{
		$start_time = $this->input->post('start_time');	
		$end_time = $this->input->post('end_time');
		$result = array('status' => false, 'msg' => '');
		
		if(empty($start_time) || empty($end_time))
		{
			$result['msg'] = '选择时间范围不能为空!!!';
			echo json_encode($result);
			exit;
		}

		if(!(strtotime($end_time) >= strtotime($start_time)))
		{
			$result['msg'] = '最终时间必须大于起始时间!!!';
			echo json_encode($result);
			exit;
		}

		$tb_chat_list = $this->db->dbprefix('qq_click');
		$inner = $this->db->query("select id,time,keywords,state from {$tb_chat_list} where time between '{$start_time}' and '{$end_time}' order by time")->result_array();
		
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
	
		$a1 = '关键词';  //这是两个标头  就是列名，最上面的那个  
		$a2 = '来源';  //这是两个标头  就是列名，最上面的那个  
		$a3 = '时间';
		$laiyuan ='1为其他QQ点击过来数据,2为10分钟弹窗点击过来数据!';
		$time = '查询起始时间: ' . $start_time . '--------- 截止时间: ' . $end_time;

		//$a1=iconv("utf-8","gb2312",$a1);  //如果是乱码的话，则需要转换下  
		//$a2=iconv("utf-8","gb2312",$a2);  
		$objPHPExcel->getActiveSheet()->setCellValue('a1', $time);//设置列的值  
		$objPHPExcel->getActiveSheet()->setCellValue('a2', $laiyuan);//设置列的值  
		$objPHPExcel->getActiveSheet()->setCellValue('a4', $a1);//设置列的值  
		$objPHPExcel->getActiveSheet()->setCellValue('b4', $a2);  
		$objPHPExcel->getActiveSheet()->setCellValue('c4', $a3);  
		

		
		/* 设置实盘账号格式为纯数字，解决科学计数法问题，参考PHPExcel/Style/NumberFormat.php */
		// $objPHPExcel->getActiveSheet()->getStyle('b')->getNumberFormat()->setFormatCode();
		// $objPHPExcel->getActiveSheet()->getStyle('c')->getNumberFormat()->setFormatCode();
		
		// $result = $this->db->query("select id,name,nick from {$this->db->dbprefix('member')}")->result_array();//连接数据库的就不用多解释了
		// $result = $this->db->query("select m.id,m.name,m.nick,i.integral,i.datetime from {$this->db->dbprefix('member')} m , {$this->db->dbprefix('integral')} i  where m.id = i.mem_id order by m.id ")->result_array();//连接数据库的就不用多解释了
		// print_r($result);
		// exit;
		$i = 5; //自增变量，用来控制行，因为标头占的第一行，所以这里从第二行开始  
		foreach($arr as $v)
		{
			$time = $v['time']; 
			$keywords = $v['keywords'];  
			$state = $v['state'];  
			//$i++;   
			//$id=iconv("utf8","gb2312",$id);  
			//$cname = iconv("utf8","gb2312",$cname);  
			$objPHPExcel->getActiveSheet()->setCellValue('a'.$i, $keywords);  
			$objPHPExcel->getActiveSheet()->setCellValue('b'.$i, $state);  
			$objPHPExcel->getActiveSheet()->setCellValue('c'.$i, $time);  
			$i++;     
		}  
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(100);//设置宽度  
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);  
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);  
		
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
		$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('B4')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('C4')->applyFromArray($styleArray1);
		  
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  //创建表格类型，目前支持老版的excel5,和excel2007,也支持生成html,pdf,csv格式  
		
		$jifen_name = 'keywords__' . date('Y-m-d',time()) . '-' . date('H-i',time()). '.xls';
		$jifen_path = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']).'file/keywords/' . $jifen_name;
		
		$objWriter->save($jifen_path);//保存生成  
		
		if(file_exists($jifen_path))
		{
			//ajax返回参数
			$result['status'] = true;
			$result['msg'] = base_url().'keywords/put_excel_download/?file=' . $jifen_name;
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

	//清空数据表
	public function keywords_del()
	{
		$this->load->database();
		// 清除一周之前的数据
		$stime = date('Y-m-d H:i:s',time() - 604800);
		$this->db->where('time <', $stime)->delete('qq_click'); 
	}
}