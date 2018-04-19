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
		<h3 class="page-header">聊天记录 <!--<button class="btn btn-danger" onclick="clear_data();">清除过期数据</button>--></h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<form action="chat/search" method="get">
					<p>发送人：<input type="text" name="send" class="form-control w400" id="send" value="<?php echo my_echo($send); ?>" placeholder="
					若要查询多人记录，请用,隔开" ></p>
					<p>
						选择房间：
						<select name="rid" class="form-control w200">
						<option value="0">全部</option>
						<?php foreach($room_list as $v): ?>
						<option value="<?php echo $v['id']; ?>" <?php if($v['id'] == my_echo($rid)): ?>selected<?php endif; ?>><?php echo $v['name']; ?></option>
						<?php endforeach; ?>
						</select>
					<p>选择时间范围：
						<input type="text" name="start" class="form-control date w200" id="start" value="<?php echo my_echo($start); ?>" readonly > 至 <input type="text" class="form-control date w200" id="end" name="end" value="<?php echo my_echo($end); ?>" readonly >
					</p>
					</p>
					<p>关键词：<input type="text" name="keyword" class="form-control w400" id="keyword" value="<?php echo my_echo($keyword); ?>" placeholder="请输入关键词" ></p>
					<p>是否内部人员：<input type="checkbox" name="isnei" class="" id="isnei" value="isnei" <?php if(my_echo($isnei)): ?>checked="checked"<?php endif; ?>></p>
					<p><button class="btn btn-success">搜索</button><a  class="btn btn-success" onclick="for_excel()">导出成Excel表</a></p>
				</form>
				
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th width="160px">发送人</th>
								<th width="160px">时间</th>
								<th>内容</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$record_list = isset($record_list) ? $record_list : array();
							foreach($record_list as $v): ?>
							<tr>
								<td><?php echo $v['name']; ?></td>
								<td><?php echo $v['time']; ?></td>
								<td><?php echo $v['content']; ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php if(isset($record_count)): ?><div>统计：搜索到记录共<?php echo $record_count; ?>条。其中公司内部发言总数为<?php echo $ben_count; ?> 条，客户发言总数为<?php echo $record_count - $ben_count; ?>条</div><?php endif; ?>
					<?php echo my_echo($pagin); ?>
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
	
$('#start, #end').datetimepicker({ format:'yyyy-mm-dd hh:ii:ss', language:'zh-CN', autoclose:true, minView:0, todayBtn:true, todayHighlight:true, minuteStep:1 });

function clear_data(){
	$.get(admin.url+'chat/clear_data','',function (){alert("删除成功");});
}

function for_excel(){
	var start_time = $('#start').val();
	var end_time = $('#end').val();
	$.post(admin.url + 'chat/for_excel',
	{start_time:start_time,end_time:end_time},
	function(res){
		window.location.href = res;
	})

}

</script>