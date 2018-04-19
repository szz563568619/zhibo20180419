<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">"<?php echo $member_info['name']; ?>"小号管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				小号列表 <a class="btn btn-primary" href="member/edit_alias/<?php echo $mid; ?>">添加小号</a>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>小号</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($alias_list as $v): ?>
							<tr>
								<td><?php echo $v['name']; ?></td>
								<td>
									<a class="btn btn-info" href="member/edit_alias/<?php echo $mid; ?>/<?php echo $v['id']; ?>">编辑</a>
									<button type="button" class="btn btn-danger" onclick="del_alias(<?php echo $v['id']; ?>)">删除</button>
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
	
function del_alias(id){
	if(confirm('确认删除该小号？该操作不可恢复，请谨慎操作！')){
		$.post(admin.url+'member/del_alias',
		{'id': id},
		function (){
			location.reload();
		})
	}
}

</script>