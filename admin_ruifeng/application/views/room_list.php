<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">房间管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				房间列表 <a class="btn btn-primary" href="room/edit_room">添加房间</a>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>房间ID</th>
								<th>房间名</th>
								<th>密码</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($room_list as $v): ?>
							<tr>
								<td><?php echo $v['id']; ?></td>
								<td><?php echo $v['name']; ?></td>
								<td><?php echo $v['pwd']; ?></td>
								<td>
									<a class="btn btn-primary" href="room/edit_room/<?php echo $v['id']; ?>">编辑</a>
									<button type="button" class="btn btn-danger" onclick="del_room('<?php echo $v['id']; ?>')">删除</button>
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
	
function del_room(id){
	if(confirm('将删除所有与该房间有关数据，请谨慎操作！')){
		$.post(admin.url+'room/del_room',
		{'id': id},
		function (result){
			result = $.parseJSON(result);
			if(result.status){
				alert('删除成功');
				location.reload();
			}else{
				alert(result.msg);
			}
		})
	}
}

</script>