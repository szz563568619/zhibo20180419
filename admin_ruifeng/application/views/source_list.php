<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">来源管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				来源列表 <a class="btn btn-primary" href="source/edit_source">添加来源</a>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>域名</th>
								<th>来源</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($source_list as $v): ?>
							<tr>
								<td><?php echo $v['host']; ?></td>
								<td><?php echo $v['source']; ?></td>
								<td>
									<a class="btn btn-primary" href="source/edit_source/<?php echo $v['id']; ?>">编辑</a>
									<button type="button" class="btn btn-danger" onclick="del_source('<?php echo $v['id']; ?>')">删除</button>
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
	
function del_source(id){
	if(confirm('确认删除该来源？请谨慎操作！')){
		$.post(admin.url+'source/del_source',
		{'id': id},
		function (){
			location.reload();
		})
	}
}

</script>