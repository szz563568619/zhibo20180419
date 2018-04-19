<script src="js/plugins/datepicker/bootstrap-datetimepicker.min.js"></script>
<script src="js/plugins/datepicker/bootstrap-datetimepicker.zh-CN.js"></script>
<link rel="stylesheet" href="css/plugins/datepicker/bootstrap-datetimepicker.min.css">
<style>
.w200{display: inline; width: 200px;}
.w400{display: inline; width: 400px;}
</style>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">会员管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<form class="search_form" method="get" action="member/member_list">			
					<p>选择注册时间范围：
						<input type="text" name="start" class="form-control date w200" id="start" value="<?php echo my_echo($start); ?>" readonly > 至 <input type="text" class="form-control date w200" id="end" name="end" value="<?php echo my_echo($end); ?>" readonly >
						<button class="btn btn-success" type="button" onclick="refresh_time()">清空时间</button>
					</p>
					<p>
					<input type="text" name="search" class="form-control w400" value="<?php echo $search; ?>">
					<button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-search"></i>搜索</button>
					</p>
					<p><a  class="btn btn-success" onclick="for_excel()">导出开户数据</a></p>		
				</form>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>名称</th>
								<th>会员组</th>
								<th>注册时间</th>
								<th>最近登陆</th>
								<th>是否开户</th>
								<th>注册来源</th>
								<th>来源</th>
								<th>关键词</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($member_list as $v): ?>
							<tr>
								<td><?php echo $v['name']; ?></td>
								<td><?php echo $v['gname']; ?></td>
								<td><?php echo $v['re_time']; ?></td>
								<td><?php echo $v['login_time']; ?></td>
								<td><?php echo $v['is_open'] ? '是' : ''; ?></td>
								<td><?php if($v['is_mobile_reg']==1){echo 'APP';}else if($v['is_mobile_reg']==2){echo '<b style="color:red;">前台注册</b>';}else{echo '后台添加';} ?></td>
								<td><?php echo $v['source']; ?></td>
								<td><?php echo $v['keyword']; ?></td>
								<td>
									<?php if($v['is_verify'] ==1){ ?><button type="button" class="btn btn-primary" onclick="change_verify(<?php echo $v['id'];?>)">点击审核</button><?php } ?>
									<?php if($v['is_company']): ?><a href="member/member_alias/<?php echo $v['id']; ?>" class="btn btn-primary">小号管理</a><?php endif; ?>
									
									<a href="member/member_edit/<?php echo $v['id']; ?>" class="btn btn-primary">编辑</a>
									<?php if(in_array('admin', $permission)){ ?><button type="button" class="btn btn-danger" onclick="member_del(<?php echo $v['id'];?>)">删除</button><?php } ?>
								</td>
							</tr>
						<?php endforeach ?>
						</tbody>
					</table>
					<?php echo $pagin; ?>
				</div>
				<!-- /.table-responsive -->
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-6 -->
</div>
<!-- /.row -->

<script type="text/javascript">
$('#start, #end').datetimepicker({ format:'yyyy-mm-dd', language:'zh-CN', autoclose:true, minView:2, todayBtn:true, todayHighlight:true, minuteStep:1 });
function member_del(id){
	if(confirm('确认删除该会员?')){
		$.post(admin.url+'member/member_del/'+id,'',function (){
			location.reload();
		})
	}
}
function refresh_time(){
	$('input[name=start]').val('');
	$('input[name=end]').val('');
}

function change_verify(id){
	$.post(admin.url+'member/member_verify/'+id,'',function (){
		location.reload();
	})
}

function for_excel(){
	var start_time = $('#start').val();
	var end_time = $('#end').val();
	$.post(admin.url + 'member/for_excel',
	{start_time:start_time,end_time:end_time},
	function(res){
		window.location.href = res;
	})

}
</script>