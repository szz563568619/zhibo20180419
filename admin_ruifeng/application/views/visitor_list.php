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
		<h3 class="page-header">游客管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<form action="visitor/search" method="get">
					<p>选择时间范围：
						<input type="text" name="start" class="form-control date w200" id="start" value="<?php echo my_echo($start); ?>" readonly > 至 <input type="text" class="form-control date w200" id="end" name="end" value="<?php echo my_echo($end); ?>" readonly >
						是否对话：<input type="checkbox" name="is_talk" class="" id="is_talk" value="1" <?php if(my_echo($is_talk) == 1): ?>checked="checked"<?php endif; ?>>
					</p>
					<p><button class="btn btn-success">搜索</button> <!-- <button type="button" class="btn btn-danger" onclick="l_reload()">查看当天内容</button> --> <a  class="btn btn-danger" onclick="for_excel()">导出Excel表格</a></p>
				</form>
				
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>IP</th>
								<th>来源</th>
								<th>游客名</th>
								<th>关键字</th>
								<th>时间</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($visitor_list as $v): ?>
							<tr class="visitor_<?php echo $v['id']; ?>">
								<td><?php echo $v['ip']; ?></td>
								<td><?php echo $v['source']; ?></td>
								<td><?php echo $v['name']; ?></td>
								<td><?php echo $v['keyword']; ?></td>
								<td><?php echo $v['time']; ?></td>
								<td>
									<a href="visitor/chat_list/<?php echo $v['id']; ?>/1" class="btn btn-primary">查看对话</a>
									<!-- <button type="button" class="btn btn-danger" onclick="del_visitor(<?php echo $v['id']; ?>)">删除</button> -->
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

<script>
$('#start, #end').datetimepicker({ format:'yyyy-mm-dd', language:'zh-CN', autoclose:true, minView:2, todayBtn:true, todayHighlight:true, minuteStep:1 });
// function set_remark(id, remark){
// 	var new_remark = prompt('请输入该游客的备注', remark);
// 	$.post(admin.url+'visitor/set_remark',
// 	{id:id, remark:new_remark},
// 	function (){$('.visitor_'+id+' .remark').html(new_remark)})
// }
function for_excel(){
	var start_time = $('#start').val();
	var end_time = $('#end').val();
	var is_talk = $('#is_talk').is(':checked');
	if(is_talk){is_talk=1;}else{is_talk=0;}
	$.post(admin.url + 'visitor/for_excel',
	{start_time:start_time,end_time:end_time,is_talk:is_talk},
	function(res){
		res = $.parseJSON(res);
		window.location.href = res.msg;
	})

}
function del_visitor(id){
	if(confirm('确认删除该游客？该操作不可恢复，请谨慎操作！')){
		$.post(admin.url+'visitor/del_visitor_istalk',
		{'id': id},
		function (){
			location.reload();
		})
	}
}
function l_reload(){
	location.href = admin.url+'visitor';
}
</script>