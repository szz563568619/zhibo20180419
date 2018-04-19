<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">专家管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				 房间
				<select class="form-control" name="room_id" onchange="select_room(this)" style="display:inline;width:150px;">
					<?php foreach($room_list as $v): ?>
					<option value="<?php echo $v['id']; ?>" <?php if($v['id'] == $room_id): ?>selected<?php endif; ?>><?php echo $v['name']; ?></option>
					<?php endforeach; ?>
				</select>
				<a href="specialist/specialist_edit/" class="btn btn-primary">添加专家</a>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>专家名称</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($specialist_list as $v): ?>
							<tr>
								<td><?php echo $v['name']; ?></td>
								<td>
									<a href="specialist/specialist_edit/<?php echo $v['id']; ?>" class="btn btn-primary">编辑</a>
									<button class="btn btn-danger" onclick="specialist_del(<?php echo $v['id']; ?>)">删除</button>
								</td>
							</tr>
						<?php endforeach ?>
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

function select_room(obj){
	var room_id = $(obj).find('option:selected').val();
	location.href = admin.url+'specialist/specialist_list/'+room_id;
}

function specialist_del (id) {
	if(confirm('删除该专家？')){
		$.post(admin.url+'specialist/specialist_del/'+id,'',function (){location.reload()})
	}
}

</script>