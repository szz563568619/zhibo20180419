<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class desktop extends MY_Controller {

	private $redis;	
	function __construct()
	{
		parent::__construct();
		parent::check_permission('base');
		$this->redis = parent::redis_conn();
	}

	public function index()
	{
		$this->out_data['con_page'] = 'desktop_list';
		$this->load->view('default', $this->out_data);
	}

	function desktop_data($desk)
	{
		$this->load->database();
		$start_time = $this->input->get('start_time');	
		$end_time = $this->input->get('end_time');
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
		$redis_start_time = strtotime($start_time." 00:00:00");
		$redis_end_time = strtotime($end_time." 23:59:59");

		//选择查询数据库
		if($desk == 1)
		{

			$tb_chat_list = 'desktop';
			$desk_title = '保存桌面统计查询';
		}
		elseif($desk == 2)
		{
			$tb_chat_list = 'from_desktop';
			$desk_title = '从桌面进入网站统计查询';

		}
		
		//根据时间范围查询结果
		$arr_redis = $this->redis->zRangeByScore($tb_chat_list, $redis_start_time, $redis_end_time);

		$result_data = array();
		foreach($arr_redis as $k => $v)
		{
			$date = date("Y-m-d", $v);
			if(isset($result_data[$date])) $result_data[$date]++;
			else $result_data[$date] = 1;
		}

		$data = array('date' => array(), 'count' => array());
		foreach($result_data as $k => $v)
		{
			$data['date'][] = $k;
			$data['count'][] = $v;
		}
		$data['date'] = json_encode($data['date']);
		$data['count'] = json_encode($data['count']);
		$this->load->view('desktop_data', array('data' => $data));
	}

	//根据查询时间要求导出成excel
	function for_excel()
	{
		$start_time = $this->input->post('start_time');	
		$end_time = $this->input->post('end_time');
		$desk = $this->input->post('desk');
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
		$redis_start_time = strtotime($start_time);
		$redis_end_time = strtotime($end_time);

		//选择查询数据库
		if($desk == 1)
		{

			$tb_chat_list = 'desktop';
			$desk_title = '保存桌面统计查询';
		}
		elseif($desk == 2)
		{
			$tb_chat_list = 'from_desktop';
			$desk_title = '从桌面进入网站统计查询';

		}
		
		//根据时间范围查询结果
		$arr_redis = $this->redis->zRangeByScore($tb_chat_list, $redis_start_time, $redis_end_time);
		
		//统计总条数
		$count_redis = $this->redis->zCount($tb_chat_list, $redis_start_time, $redis_end_time);
		
		//调用很少，生成excel表格
		$this->put_for_excel($arr_redis,$result,$start_time,$end_time,$desk_title,$count_redis);

	}

	//将数组中的数据生成excel表
	public function put_for_excel($arr,$result,$start_time,$end_time,$desk_title,$count)
	{
		
		require_once 'PHPExcel.php';  
		include "PHPExcel/Writer/IWriter.php";   
		include "PHPExcel/Writer/Excel5.php";   
		include 'PHPExcel/IOFactory.php';   
		//上面的四句代码是引入所需要的库， 

		$objPHPExcel = new PHPExcel();
	
		$a1 = '点击时间';  //这是两个标头  就是列名，最上面的那个  
		// $a2 = '时间';  //这是两个标头  就是列名，最上面的那个  
		$laiyuan =$desk_title . '---------总条数:' . $count;
		$select_time = '查询起始时间: ' . $start_time . '--------- 截止时间: ' . $end_time;

		//$a1=iconv("utf-8","gb2312",$a1);  //如果是乱码的话，则需要转换下  
		//$a2=iconv("utf-8","gb2312",$a2);  
		$objPHPExcel->getActiveSheet()->setCellValue('a1', $select_time);//设置列的值  
		$objPHPExcel->getActiveSheet()->setCellValue('a2', $laiyuan);//设置列的值  
		$objPHPExcel->getActiveSheet()->setCellValue('a4', $a1);//设置列的值  
		// $objPHPExcel->getActiveSheet()->setCellValue('b4', $a2);  
		

		
		/* 设置实盘账号格式为纯数字，解决科学计数法问题，参考PHPExcel/Style/NumberFormat.php */
		// $objPHPExcel->getActiveSheet()->getStyle('b')->getNumberFormat()->setFormatCode();
		// $objPHPExcel->getActiveSheet()->getStyle('c')->getNumberFormat()->setFormatCode();
		
		// $result = $this->db->query("select id,name,nick from {$this->db->dbprefix('member')}")->result_array();//连接数据库的就不用多解释了
		// $result = $this->db->query("select m.id,m.name,m.nick,i.integral,i.datetime from {$this->db->dbprefix('member')} m , {$this->db->dbprefix('integral')} i  where m.id = i.mem_id order by m.id ")->result_array();//连接数据库的就不用多解释了
		// print_r($result);
		// exit;
		$i = 5; //自增变量，用来控制行，因为标头占的第一行，所以这里从第二行开始  
		for($rr = 0; $rr < $count; $rr++)
		{
			$time = date('Y-m-d H:i:s',$arr[$rr]); 
			// $name = $v['name'];  
			//$i++;   
			//$id=iconv("utf8","gb2312",$id);  
			//$cname = iconv("utf8","gb2312",$cname);  
			$objPHPExcel->getActiveSheet()->setCellValue('a'.$i, $time);  
			// $objPHPExcel->getActiveSheet()->setCellValue('b'.$i, $rr);  
			$i++;     
		}  
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(80);//设置宽度  
		// $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);  
		
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
		// $objPHPExcel->getActiveSheet()->getStyle('B4')->applyFromArray($styleArray1);
		  
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  //创建表格类型，目前支持老版的excel5,和excel2007,也支持生成html,pdf,csv格式  
		
		$jifen_name = 'desktop__' . date('Y-m-d',time()) . '-' . date('H-i',time()). '.xls';
		$jifen_path = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']).'file/desktop/' . $jifen_name;
		
		$objWriter->save($jifen_path);//保存生成  
		
		if(file_exists($jifen_path))
		{
			//ajax返回参数
			$result['status'] = true;
			$result['msg'] = base_url().'desktop/put_excel_download/?file=' . $jifen_name;
			echo json_encode($result);
		}
	}
	//生成的excel下载
	public function put_excel_download(){
		$file = $this->input->get('file');
		$file = base_url().'file/desktop/'.$file;
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
	public function desktop_del()
	{
		$this->load->database();
		// 清除一周之前的数据
		$stime = date('Y-m-d H:i:s',time() - 604800);
		$this->redis->zRemRangeByScore('desktop', 0, $stime);
		$this->redis->zRemRangeByScore('from_desktop', 0, $stime);
	}
}