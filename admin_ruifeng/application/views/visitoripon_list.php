<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">游客ip在线时间列表</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-body" id="">
					<div class="col-lg-6">
					
					</div>
					
				
				</div>
				<form class="form-horizontal" role="form">
				  <div class="form-group">
						<label for="limittime_part" class="col-sm-2 control-label">进来后不能看视频聊天时长</label>
						<div class="col-sm-10">
							<div class="input-group">
								<input class="form-control" id="limittime_part" name="limittime_part" type="number" placeholder="设置最大在线时长，进来后不能看视频聊天" value="<?php echo (int)$visitoripon_limittime_part; ?>">
								<span class="input-group-addon">分钟</span>
							</div>
						</div>
				  </div>
				  <div class="form-group">
						<label for="limittime_all" class="col-sm-2 control-label">进来后什么都不能看</label>
						<div class="col-sm-10">
							<div class="input-group">
								<input class="form-control" id="limittime_all" name="limittime_all" type="number" placeholder="设置最大在线时长，进来后什么都不能看" value="<?php echo (int)$visitoripon_limittime_all; ?>">
								<span class="input-group-addon">分钟</span>
							</div>
						</div>				  
					</div>
				  <div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
					  <button type="button" class="btn btn-success" onclick="save_limittime()">保存</button>
					</div>
				  </div>
				</form>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>ip</th>
								<th>在线时长/分钟</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($visitoripons as $v): ?>
							<tr>
								<td><?php echo $v['ip']; ?></td>
								<td><?php echo round($v['totaltime']/60); ?></td>
								<td>
									<button type="button" class="btn btn-danger" onclick="del(<?php echo $v['id']; ?>)">删除</button>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
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
function del(id){
	$.post(admin.url+'visitoripon/del_visitoripon', {id:id}, function (){
		location.reload();
	})
}

function save_limittime(){
	var limittime_part = $('input[name=limittime_part]').val();
	var limittime_all = $('input[name=limittime_all]').val();
	$.post(admin.url+'visitoripon/set_limittime', {limittime_part:limittime_part,limittime_all:limittime_all}, function (){
		location.reload();
	})
}
</script>