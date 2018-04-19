<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class chat extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('admin');
		$this->load->database();
	}

	public function index()
	{
		$this->_set_room_list();
		$this->out_data['con_page'] = 'chat_list';
		$this->load->view('default', $this->out_data);
	}

	function search()
	{
		$this->_set_room_list();
		$send = $this->input->get('send');

		$start = urldecode($this->input->get('start'));
		$end = urldecode($this->input->get('end'));
		$rid = $this->input->get('rid');
		$page = $this->input->get('per_page') ? $this->input->get('per_page') : 1;
		//
		$keyword = trim($this->input->get('keyword'));
		$isnei = trim($this->input->get('isnei'));

		$search = "";
		$count_search = "";
		if( ! empty($send) )
		{
			$sends = $this->_get_member_alias($send);//获取当前会员和小号名称
			$search_send = "'".str_replace(',', "','", $sends)."'";
			$count_search .= " AND name IN ({$search_send}) ";
			$search .= " AND c.name IN ({$search_send}) ";
		}
		if( ! empty($start) ){
			$search .= " AND time >= '{$start}' ";
			$count_search .= " AND time >= '{$start}' ";

		} 
		if( ! empty($end) ){
			$search .= " AND time <= '{$end}' ";
			$count_search .= " AND time <= '{$end}' ";

		} 
		$types = " AND types = 1 ";
		if( $rid !== 0 AND ! empty($rid) ){
			$search .= " AND c.rid = '{$rid}' ";
			$count_search .= " AND rid = '{$rid}' ";
		} 
		//
		if( ! empty($keyword) ){
			$search .= " AND c.content like '%{$keyword}%' ";
			$count_search .= " AND content like '%{$keyword}%' ";

		} 
		if( ! empty($isnei) ){
			$search .= " AND c.types = 1 ";
			$count_search .= " AND types = 1 ";

		} 

		$limit = 30;
		$start_record = ($page - 1)*$limit;
		$tb_chat_list = $this->db->dbprefix('chat_list');
		$tb_admin = $this->db->dbprefix('admin');
		$this->out_data['record_list'] = $this->db->query("select id,name,time,content from {$tb_chat_list} as c where 1= 1 {$search} order by time desc limit {$start_record},{$limit}")->result_array();

		$this->out_data['record_count'] = $this->db->query("select count(1) as num from {$tb_chat_list} where 1 = 1 {$count_search}")->row()->num;
		$this->out_data['ben_count'] = $this->db->query("select count(1) as num from {$tb_chat_list} where 1 = 1 {$count_search}{$types}")->row()->num;

		$this->out_data['pagin'] = parent::get_pagin(base_url()."chat/search?send={$send}&start={$start}&end={$end}&rid={$rid}&keyword={$keyword}&isnei={$isnei}", $this->out_data['record_count'], $limit, 3,  true);

		$this->out_data['send'] = $send;
		$this->out_data['start'] = $start;
		$this->out_data['end'] = $end;
		$this->out_data['rid'] = $rid;
		//
		$this->out_data['keyword'] = $keyword;
		$this->out_data['isnei'] = $isnei;
		$this->out_data['con_page'] = 'chat_list';
		$this->load->view('default', $this->out_data);
	}
	
	private function _get_member_alias($send)
	{
		$mem_ids = array();
		$member_alias = array();
		$where_send = "'".str_replace(',', "','", $send)."'";
		$res = $this->db->query("select id from {$this->db->dbprefix('member')} where name in({$where_send})")->result_array();
		if(!empty($res))
		{
			$mem_ids = array_column($res, 'id');
		}
		
		if(!empty($mem_ids))
		{
			$mem_id = implode(',', $mem_ids);
			$member_alias = $this->db->query("select name from {$this->db->dbprefix('member_alias')} where mid in({$mem_id})")->result_array();
			$result = $send . ',' . implode(',', array_column($member_alias, 'name'));
		}
		else
		{
			$result = $send;
		}
		return $result;
	}

	function clear_data()
	{
		$redis = parent::redis_conn();
		$this->_set_room_list();
		foreach($this->out_data['room_list'] as $v)
		{
			$redis->zremrangebyrank("room_{$v['id']}", 0, -1);
		}


		$beforeyesterday = date("Y-m-d",strtotime('-2 day'));
		$this->db->query("delete from {$this->db->dbprefix('chat_list')} where date_format(time,'%Y-%m-%d')<='{$beforeyesterday}'");
		// $this->db->query("TRUNCATE TABLE {$this->db->dbprefix('chat_list')}");
		//$this->db->query("TRUNCATE TABLE {$this->db->dbprefix('visitor')}");
	}

	private function _set_room_list()
	{
		$this->out_data['room_list'] = $this->db->query("select id,name from {$this->db->dbprefix('room')}")->result_array();
	}
	//根据查询时间要求导出成excel
	function for_excel()
	{
		$start_time = $this->input->post('start_time');	
		$end_time = $this->input->post('end_time');
		
		//将时间后面的分秒归0
		$substr_st = substr($start_time,-5);
		$new_start_time = str_replace($substr_st,'00:00',$start_time);

		$substr_end = substr($end_time,-5);
		$new_end_time = str_replace($substr_end,'00:00',$end_time);

		$as_date = strtotime($new_start_time);

		//统计多少个小时
		$count_time = (strtotime($new_end_time) - strtotime($new_start_time))/3600;

		$tb_chat_list = $this->db->dbprefix('chat_list');
		// $this->out_data['record_list'] = $this->db->query("select count(id),date_format(time,'%Y%m%d%H') from {$tb_chat_list} where time between {$start_time} and {$end_time} order by time desc group by date_format(time,'%Y%m%d%H') ")->result_array();
		// $outside = $this->db->query("select types as tt ,(select count(id) from {$tb_chat_list} where tt = 1 ) as inner,date_format(time,'%Y%m%d%H') as time  from {$tb_chat_list} where time between '{$start_time}' and '{$end_time}' and types = 0 group by date_format(time,'%Y%m%d%H') ")->result_array();
		
		//内网
		$inner = $this->db->query("select count(id) as inn,date_format(time,'%Y-%m-%d %H') as tim  from {$tb_chat_list} where time between '{$start_time}' and '{$end_time}' and types = 1 group by date_format(time,'%Y%m%d%H') ")->result_array();
		// 外网
		$outside = $this->db->query("select count(id) as outside,date_format(time,'%Y-%m-%d %H') as tim  from {$tb_chat_list} where time between '{$start_time}' and '{$end_time}' and types = 0 group by date_format(time,'%Y%m%d%H') ")->result_array();
		
		//合并数组
		$last_array = array();
		for($i = 0; $i < $count_time; $i++)
		{
			$ddd_date = $as_date + 3600 * $i;
			$ff_date = $as_date + 3600 * ($i+1);
			$last_array[$i]['time'] = date('Y-m-d H:s',$ddd_date) .' -- '. date('H:s',$ff_date);

			foreach ($inner as $val) 
			{
				$val_time = strtotime($val['tim'] . ':00:00');

				if($ddd_date == $val_time)
				{
					$last_array[$i]['inn'] = $val['inn'];
				}

			}
			foreach ($outside as $vv) 
			{
				$vv_time = strtotime($vv['tim'] . ':00:00');

				if($ddd_date == $vv_time)
				{
					$last_array[$i]['outside'] = $vv['outside'];
				}

			}
		}

		//将合并后的数组，每小时key不存在的，改为为空
		foreach($last_array as $key => $v)
		{
			if(empty($v['inn']))
			{
				$last_array[$key]['inn'] = '';
			} 
			if(empty($v['outside']))
			{
				$last_array[$key]['outside'] = '';
			}
			
		}

		//调用很少，生成excel表格
		$this->put_for_excel($last_array);

	}

	//将数组中的数据生成excel表
	public function put_for_excel($arr)
	{
		
		require_once 'PHPExcel.php';  
		include "PHPExcel/Writer/IWriter.php";   
		include "PHPExcel/Writer/Excel5.php";   
		include 'PHPExcel/IOFactory.php';   
		//上面的四句代码是引入所需要的库， 

		$objPHPExcel = new PHPExcel();
	
		$a1 = '时间';  //这是两个标头  就是列名，最上面的那个  
		$a2 = '内部总条数';  
		$a3 = '外部总条数';  
		

		//$a1=iconv("utf-8","gb2312",$a1);  //如果是乱码的话，则需要转换下  
		//$a2=iconv("utf-8","gb2312",$a2);  
		$objPHPExcel->getActiveSheet()->setCellValue('a1', $a1);//设置列的值  
		$objPHPExcel->getActiveSheet()->setCellValue('b1', $a2);  
		$objPHPExcel->getActiveSheet()->setCellValue('c1', $a3);  

		
		/* 设置实盘账号格式为纯数字，解决科学计数法问题，参考PHPExcel/Style/NumberFormat.php */
		$objPHPExcel->getActiveSheet()->getStyle('b')->getNumberFormat()->setFormatCode();
		$objPHPExcel->getActiveSheet()->getStyle('c')->getNumberFormat()->setFormatCode();
		
		// $result = $this->db->query("select id,name,nick from {$this->db->dbprefix('member')}")->result_array();//连接数据库的就不用多解释了
		// $result = $this->db->query("select m.id,m.name,m.nick,i.integral,i.datetime from {$this->db->dbprefix('member')} m , {$this->db->dbprefix('integral')} i  where m.id = i.mem_id order by m.id ")->result_array();//连接数据库的就不用多解释了
		// print_r($result);
		// exit;
		$i = 2; //自增变量，用来控制行，因为标头占的第一行，所以这里从第二行开始  
		foreach($arr as $v)
		{
			$time = $v['time']; 
			
			$inner = $v['inn'];  
			$outside = $v['outside'];  
			// $reason = $v['reason'];  
			// $another = $v['another'];  
			// $touzi_reason = $v['touzi_reason'];  
			// $create_date = $v['create_date'];  
			// $flag = $v['flag'];  
			//$i++;   
			//$id=iconv("utf8","gb2312",$id);  
			//$cname = iconv("utf8","gb2312",$cname);  
			$objPHPExcel->getActiveSheet()->setCellValue('a'.$i, $time);  
			$objPHPExcel->getActiveSheet()->setCellValue('b'.$i, $inner);  
			$objPHPExcel->getActiveSheet()->setCellValue('c'.$i, $outside);  
			// $objPHPExcel->getActiveSheet()->setCellValue('f'.$i, $reason);  
			// $objPHPExcel->getActiveSheet()->setCellValue('g'.$i, $another);  
			// $objPHPExcel->getActiveSheet()->setCellValue('h'.$i, $touzi_reason);  
			// $objPHPExcel->getActiveSheet()->setCellValue('i'.$i, $create_date);  
			// $objPHPExcel->getActiveSheet()->setCellValue('j'.$i, $flag);//这些跟上面的一样，开始一行一行的赋值。  
			$i++;     
		}  
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);//设置宽度  
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);  
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);  
		// $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);  
		// $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);  
		// $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);  
		// $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);  
		// $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);  
		// $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);  
		// $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);  
		
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
		$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray1);
		$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray1);
		// $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray1);
		// $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray1);
		// $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray1);
		// $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray1);
		// $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray1);
		// $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray1);
		// $objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray1);
		  
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  //创建表格类型，目前支持老版的excel5,和excel2007,也支持生成html,pdf,csv格式  
		
		$jifen_name = 'one__' . date('Y-m-d',time()) . '-' . date('H-i',time()). '.xls';
		$jifen_path = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']).'file/excel/' . $jifen_name;
		
		/* echo $wen_path;
		exit; */
		$objWriter->save($jifen_path);//保存生成  
		
		if(file_exists($jifen_path))
		{
			
			//ajax返回参数
			echo base_url().'chat/put_excel_download/?file=' . $jifen_name;
			//echo "成功生成excel，点击获取";
		}
	}
	//生成的excel下载
	public function put_excel_download(){
		$file = $this->input->get('file');
		$file = base_url().'file/excel/'.$file;
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