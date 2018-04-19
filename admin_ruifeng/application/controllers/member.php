<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class member extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		parent::check_permission('member');
		$this->load->database();
		self::check_login_admin();
		$this->out_data['current_function'] = 'member';
	}

	public function index()
	{
		$this->group();
	}

	private function check_login_admin()
	{
		$admin_login = $this->db->where('id',$this->session->userdata('id'))->limit(1)->get('admin')->num_rows();
		if(!$admin_login)
		{
			header("Location:".base_url()."login");
		}
	}

	function group()
	{
		$this->out_data['group'] = $this->db->query("select id,name,sort from {$this->db->dbprefix('group')} order by sort desc")->result_array();
		$this->out_data['con_page'] = 'group';
		$this->load->view('default', $this->out_data);
	}

	function group_edit($id = -1)
	{
		$id = (int)$id;
		$info = $this->db->query("select id,name,sort from {$this->db->dbprefix('group')} where id={$id} limit 1");
		if($info->num_rows() > 0)
		{
			$info = $info->row_array();
		}
		else
		{
			$info = array('id' => -1, 'name' => '', 'sort' => 0);
		}
		// $this->load->library('lib_elements');
		// $info['icon'] = $this->lib_elements->get_file_element(array('title' => '会员组勋章', 'name' => 'icon', 'img' => '../images/level/level'.$info['id'].'.png'));

		$this->out_data['info'] = $info;
		$this->out_data['con_page'] = 'group_edit';
		$this->load->view('default', $this->out_data);
	}

	function group_update()
	{
		$id = (int)$this->input->post('id');
		$name = $this->input->post('name');
		$icon = $this->input->post('icon');
		$sort = $this->input->post('sort');

		if($id == -1)
		{
			$this->db->insert('group', array('name' => $name, 'sort' => $sort));
			$id = $this->db->insert_id();
		}
		else
		{
			$this->db->update('group', array('name' => $name, 'sort' => $sort), array('id' => $id));
		}
		// $this->load->library('lib_elements');
		// $this->lib_elements->move_img($icon, '../images/level/level'.$id.'.png');
	}

	function member_list()
	{
		$page = $this->input->get('per_page') ? $this->input->get('per_page') : 1;
		$search = $this->input->get('search');
		//$encrypt_search = sha1($search);
		$startt = $this->input->get('start');
		$end = $this->input->get('end');
		$query_search = '';
		if($search) $query_search = " AND ((m.name LIKE '%{$search}%' OR m.phone = '{$search}') OR m.source LIKE '%{$search}%') ";
		if($startt) $query_search .= " AND (re_time >= '{$startt} 00:00:00') ";
		if($end) $query_search .= " AND (re_time <= '{$end} 23:59:59') ";
		
		//如果管理员是客服，且不是超级管理员，只显示对应客服的会员
		$permission = $this->out_data['permission']['permission'];
		$permission = explode(',', $permission);
		if(!in_array('admin', $permission)) $query_search .= " AND cid = {$this->session->userdata('id')} ";

		$tb_member = $this->db->dbprefix('member');
		$tb_group = $this->db->dbprefix('group');
		$tb_teacher = $this->db->dbprefix('teacher');
		$limit = 20;
		$start = ($page - 1)*$limit;
		$query_memeber_list = "select m.id,m.name,m.re_time,m.login_time,m.is_open,m.is_company,m.source,m.keyword,m.is_mobile_reg,m.is_verify,g.name as gname from {$tb_member} as m left join {$tb_group} as g on m.gid=g.id where 1 = 1 {$query_search} order by re_time desc limit {$start},{$limit}";
		$this->out_data['member_list'] = $this->db->query($query_memeber_list)->result_array();

		$query_count = "select count(1) as num from {$tb_member} as m where 1 = 1 {$query_search}";
		$count = $this->db->query($query_count)->row()->num;
		$base_url = base_url().'member/member_list/?';
		if($query_search) $base_url .= "search=".$search."&start={$startt}&end={$end}";
		$this->out_data['pagin'] = parent::get_pagin($base_url, $count, $limit, 3,  true);

		$this->out_data['start'] = $startt;
		$this->out_data['end'] = $end;
		$this->out_data['search'] = $search;
		$this->out_data['con_page'] = 'member_list';
		$this->load->view('default', $this->out_data);
	}
	
	function member_verify($id)
	{
		$this->db->update('member', array('is_verify' => 0), array('id' => $id));
	}

	function member_del($id)
	{
		//推送删除事件
		$gid = $this->db->query("select gid from {$this->db->dbprefix('member')} where id = '{$id}'")->row()->gid;
		$data = array('type'=>'delete_member','content'=>$id.'_'.$gid);
        send_websocket($data);
		
		$this->db->delete('member', array('id' => $id));
		//删除redis中的小号
		$redis = parent::redis_conn();
		$redis->del("alias_list_".$id);
		$redis->del("uqq_".$id);
		$redis->del('auth_code_'.$id.'_'.$gid);
	}

	function member_edit($id = 0)
	{
		$this->out_data['group_list'] = $this->db->query("select * from {$this->db->dbprefix('group')}")->result_array();
		$this->out_data['member_info'] = $this->db->query("select id,gid,name,phone,is_open,cid,is_company,qq,account,say,tid,is_mobile_reg,keyword,source from {$this->db->dbprefix('member')} where id={$id} limit 1")->row_array();
		
		/* 来源表 */
		$this->out_data['source'] = $this->db->query("select * from {$this->db->dbprefix('source')}")->result_array();
		
		if( ! $this->out_data['member_info'])
		{
			$this->out_data['member_info']['id'] = 0;
			$this->out_data['member_info']['is_mobile_reg'] = 0;
		}
		$this->out_data['customer_service_list'] = $this->db->query("select id, nick from {$this->db->dbprefix('admin')} where find_in_set('customer', permission)")->result_array();
		/* 设置专属老师列表 */
		$this->out_data['teacher_service_list'] = $this->db->query("select id, nick from {$this->db->dbprefix('admin')} where find_in_set('teacher', permission)")->result_array();

		$this->out_data['con_page'] = 'member_edit';
		$this->load->view('default', $this->out_data);
	}

	function member_update()
	{
		$phone = $this->input->post('phone');
		//if(strlen($phone) < 15) $phone = sha1($phone); /*加密手机号*/
		$info = array('name' => $this->input->post('name'),
			'gid' => $this->input->post('gid'),
			'cid' => $this->input->post('cid'),
			'tid' => $this->input->post('tid'),
			'is_company' => $this->input->post('is_company'),
			'phone' => $phone,
			'qq' => trim($this->input->post('qq')),
			// 'account' => trim($this->input->post('account')),
			'is_mobile_reg' => $this->input->post('is_mobile_reg'),
			'source' => $this->input->post('source'),
			'keyword' => $this->input->post('keyword'),
			'say' => trim($this->input->post('say'))
			);
		$password = $this->input->post('password');
		if($password) $info['password'] = md5($password);
		$id = $this->input->post('id');

		$result = array('status' => false, 'msg' => '');

		//判断开户
		$is_open = $this->input->post('is_open');
		if($is_open !== false){
			//如果有传过来的开户信息，就存数据库
			$info['is_open'] = $is_open;
			if($is_open) $info['open_time'] = date("Y-m-d H:i:s");
		}

		/*先判断会员名的有合法性比较好*/
		$validate = $this->_validate_name($info['name']);
		if( ! $validate['status'])
		{
			$result['msg'] = $validate['msg'];
			echo json_encode($result);
			exit;
		}
		
		/* 判断来源 */
		if(!$info['source'])
		{
			$result['msg'] = '请选择来源！';
			echo json_encode($result);
			exit;
		}
		
		/* 判断关键词 */
		if(!$info['keyword'])
		{
			$result['msg'] = '请输入关键词！';
			echo json_encode($result);
			exit;
		}

		$tb_member = $this->db->dbprefix('member');
		
		/* 判断qq唯一性 */
		if(!empty($info['qq']) AND $this->db->query("select count(1) as num from {$tb_member} where qq='".$info['qq']."' and id <> {$id} limit 1")->row()->num > 0)
		{
			$result['msg'] = 'QQ号已存在，请重新输入';
			echo json_encode($result);
			exit;
		}

		if($this->db->query("select count(1) as num from {$tb_member} where name='".$info['name']."' and id <> {$id} limit 1")->row()->num > 0 OR $this->db->query("select count(1) as num from {$this->db->dbprefix('member_alias')} where name='".$info['name']."' limit 1")->row()->num > 0)
		{
			$result['msg'] = '该会员名称已存在，请重新输入';
		}
		else
		{
			if($id == 0)
			{
				$info['re_time'] = date('Y-m-d H:i:s');
				$this->db->insert($tb_member, $info);
			}
			else
			{
				/* 如果不是admin就提示不能操作 */
				$permission = $this->out_data['permission']['permission'];
				$permission = explode(',', $permission);
				if(!in_array('admin', $permission)){
					$resArr = $this->db->query("select cid,phone,qq from {$tb_member} where id = {$id} limit 1")->row_array();
					if($resArr){
						if($resArr['phone'] != $info['phone'] OR $resArr['qq'] != $info['qq'] OR $resArr['cid'] != $info['cid']){
							$result['msg'] = '您无权更改手机号，qq号和专属客服！';
							echo json_encode($result);
							exit;
						}
					}
				}
				
				$this->db->update($tb_member, $info, array('id' => $id));
			}
			$result['status'] = true;
		}
		$redis = parent::redis_conn();
		$redis->del("uqq_".$id);
		$redis->del('auth_code_'.$id.'_'.$info['gid']);
		echo json_encode($result);
	}

	function member_alias($mid)
	{
		$this->out_data['alias_list'] = $this->db->query("select id,name from {$this->db->dbprefix('member_alias')} where mid = {$mid}")->result_array();
		$this->out_data['member_info'] = $this->db->query("select name from {$this->db->dbprefix('member')} where id={$mid} limit 1")->row_array();
		$this->out_data['mid'] = $mid;
		$this->out_data['con_page'] = 'member_alias';
		$this->load->view('default', $this->out_data);
	}

	function del_alias()
	{
		$id = $this->input->post('id');
		//删除redis中的小号
		$mid = $this->db->query("select mid from {$this->db->dbprefix('member_alias')} where id={$id} limit 1")->row()->mid;
		$redis = parent::redis_conn();
		$redis->del("alias_list_".$mid);
		$this->db->query("delete from {$this->db->dbprefix('member_alias')} where id={$id} limit 1");
	}

	function edit_alias($mid, $alias_id = 0)
	{
		$info = array('gid' => '', 'name' => '');
		if($alias_id != 0) $info = $this->db->query("select gid,name from {$this->db->dbprefix('member_alias')} where id={$alias_id} limit 1")->row_array();

		$this->out_data['info'] = $info;
		$this->out_data['group_list'] = $this->db->query("select * from {$this->db->dbprefix('group')}")->result_array();
		$this->out_data['member_name'] = $this->db->query("select name from {$this->db->dbprefix('member')} where id={$mid} limit 1")->row()->name;
		$this->out_data['mid'] = $mid;
		$this->out_data['alias_id'] = $alias_id;
		$this->out_data['con_page'] = 'member_alias_edit';
		$this->load->view('default', $this->out_data);
	}

	function alias_update()
	{
		$info = array('mid' => $this->input->post('mid'), 'name' => $this->input->post('name'), 'gid' => $this->input->post('gid'));
		$result = array('status' => false, 'msg' => '');
		/*先判断会员名的有合法性比较好*/
		$validate = $this->_validate_name($info['name']);
		if( ! $validate['status'])
		{
			$result['msg'] = $validate['msg'];
			echo json_encode($result);
			exit;
		}

		$tb_member = $this->db->dbprefix('member');
		$tb_member_alias = $this->db->dbprefix('member_alias');
		$alias_id = $this->input->post('alias_id');

		if($this->db->query("select count(1) as num from {$tb_member} where name='".$info['name']."' limit 1")->row()->num > 0 OR $this->db->query("select count(1) as num from {$tb_member_alias} where name='".$info['name']."' AND id <> {$alias_id} limit 1")->row()->num > 0)
		{
			$result['msg'] = '该会员名称已存在，请重新输入';
		}
		else
		{
			if($alias_id == 0) $this->db->insert($tb_member_alias, $info);
			else $this->db->update($tb_member_alias, $info, array('id' => $alias_id));
			$result['status'] = true;
			//删除redis中的小号
			$redis = parent::redis_conn();
			$redis->del("alias_list_".$info['mid']);
		}
		echo json_encode($result);
	}

	private function _validate_name($name)
	{
		$result = array('status' => false, 'msg' => '');
		if(mb_strlen($name) < 3 OR mb_strlen($name) > 20)
		{
			$result['msg'] = '用户名的长度为3到20之间';
		}
		elseif( ! preg_match('/^[A-Za-z0-9_\x7f-\xff]+$/', $name))
		{
			$result['msg'] = '用户名只能由汉字，大小写字母，数字和下划线组成';
		}
		else
		{
			$result['status'] = true;
		}
		return $result;
	}


	//根据查询时间要求导出成excel
	function for_excel()
	{
		$start_time = $this->input->post('start_time');	
		$end_time = $this->input->post('end_time');

		$tb_chat_list = $this->db->dbprefix('member');
		
		$outside = $this->db->query("select name,re_time,open_time from {$tb_chat_list} where is_open = 1 order by open_time desc ")->result_array();

		//调用很少，生成excel表格
		$this->put_for_excel($outside);

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
	
		$a1 = '会员名';  //这是两个标头  就是列名，最上面的那个  
		$a2 = '注册时间';  
		$a3 = '开户时间';  
		

		//$a1=iconv("utf-8","gb2312",$a1);  //如果是乱码的话，则需要转换下  
		//$a2=iconv("utf-8","gb2312",$a2);  
		$objPHPExcel->getActiveSheet()->setCellValue('a1', $a1);//设置列的值  
		$objPHPExcel->getActiveSheet()->setCellValue('b1', $a2);  
		$objPHPExcel->getActiveSheet()->setCellValue('c1', $a3);  

		$i = 2; //自增变量，用来控制行，因为标头占的第一行，所以这里从第二行开始  
		foreach($arr as $v)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('a'.$i, $v['name']);  
			$objPHPExcel->getActiveSheet()->setCellValue('b'.$i, $v['re_time']);  
			$objPHPExcel->getActiveSheet()->setCellValue('c'.$i, $v['open_time']);   
			$i++;     
		}  
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);//设置宽度  
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);  
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);  
		
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
		  
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  //创建表格类型，目前支持老版的excel5,和excel2007,也支持生成html,pdf,csv格式  
		
		$jifen_name = 'one__' . date('Y-m-d',time()) . '-' . date('H-i',time()). '.xls';
		$jifen_path = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']).'file/excel/' . $jifen_name;
		
		/* echo $wen_path;
		exit; */
		$objWriter->save($jifen_path);//保存生成  
		
		if(file_exists($jifen_path))
		{
			
			//ajax返回参数
			echo base_url().'member/put_excel_download/?file=' . $jifen_name;
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