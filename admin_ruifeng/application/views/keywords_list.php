<script src="js/plugins/datepicker/bootstrap-datetimepicker.min.js"></script>
<script src="js/plugins/datepicker/bootstrap-datetimepicker.zh-CN.js"></script>
<link rel="stylesheet" href="css/plugins/datepicker/bootstrap-datetimepicker.min.css">
<style>
.table>tbody>tr>td{vertical-align: middle;}
.w200{display: inline; width: 200px;}
.w400{display: inline; width: 400px;}
</style>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">关键词统计导出</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<form method="get" action="keywords/keywords_list">
					<p>选择时间范围：
						<input type="text" name="start" class="form-control date w200" id="start" value="<?php echo my_echo($start); ?>" readonly > 至 <input type="text" class="form-control date w200" id="end" name="end" value="<?php echo my_echo($end); ?>" readonly >
					</p>
					<!-- <p><button class="btn btn-success">搜索</button><button type="button" class="btn btn-success" onclick="for_excel()">导出成Excel表</button></p> -->
					<p>
						<a  class="btn btn-success" onclick="for_excel()">导出成Excel表</a>
						<button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-search"></i>查看</button>
						<button class="btn btn-danger" onclick="keywords_del()" type="button">清空一周之前数据</button>
					</p>
				</form>
			</div>
			<div class="panel-body">
					<div class="panel-heading">
						<span class="btn btn-primary">总条数:<?php echo my_echo($count); ?></span>
					</div>
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>关键词</th>
									<th>来源</th>
									<th>时间</th>
								</tr>
							</thead>
							<tbody>
							<?php if(my_echo($keywords_list)):foreach($keywords_list as $v): ?>
								<tr>
									<td><?php echo my_echo($v['keywords']); ?></td>
									<td><?php echo my_echo($v['state']); ?></td>
									<td><?php echo my_echo($v['time']); ?></td>
								</tr>
							<?php endforeach; endif; ?>
							</tbody>
						</table>
						<?php echo my_echo($pagin); ?>
					</div>
					<!-- /.table-responsive -->
				</div>
			</div>
		</div>
	<!-- /.col-lg-6 -->
</div>
<!-- /.row -->
<script>
	
$('#start, #end').datetimepicker({ format:'yyyy-mm-dd hh:ii:ss', language:'zh-CN', autoclose:true, minView:0, todayBtn:true, todayHighlight:true, minuteStep:1 });

function for_excel(){
	var start_time = $('#start').val();
	var end_time = $('#end').val();
	$.post(admin.url + 'keywords/for_excel',
	{start_time:start_time,end_time:end_time},
	function(result){
		// console.log(res);
		result = $.parseJSON(result);
		if(result.status)
		{
			window.location.href = result.msg;
		}
		else
		{
			alert(result.msg);
		}
	})

}
function keywords_del(){
	if(confirm('确认清空数据,请谨慎操作?')){
		$.post(admin.url+'keywords/keywords_del','',function (){
			location.reload();
		})
	}
}


</script>