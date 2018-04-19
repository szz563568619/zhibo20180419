<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">管理员管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				管理员列表 <a href="admin/edit_admin/" class="btn btn-primary">添加管理员</a>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>账号</th>
								<th>昵称</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($admin_list as $v): ?>
							<tr>
								<td><?php echo $v['id']; ?></td>
								<td><?php echo $v['name']; ?></td>
								<td><?php echo $v['nick']; ?></td>
								<td>
									<a href="admin/edit_admin/<?php echo $v['id']; ?>" class="btn btn-primary">编辑</a>
									<?php if($this->session->userdata('id') == 1 OR $this->session->userdata('id') == 39){ ?><button type="button" class="btn btn-danger" onclick="del_admin(<?php echo $v['id']; ?>)">删除</button><?php } ?>
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
	
function del_admin(id){
	if(confirm('确认删除该管理员？该操作不可恢复，请谨慎操作！')){
		$.post(admin.url+'admin/del_admin',
		{'id': id},
		function (){
			location.reload();
		})
	}
}

</script>