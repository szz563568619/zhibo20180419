<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">会员组管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				会员组列表 <a href="member/group_edit/" class="btn btn-primary">添加会员组</a>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>组等级ID</th>
								<th>会员组名称</th>
								<th>勋章图标</th>
								<th>排序</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($group as $v):?>
							<tr>
								<td><?php echo $v['id']; ?></td>
								<td><?php echo $v['name']; ?></td>
								<td><img src="../skin/gupiao0306/images/level/level<?php echo $v['id']; ?>.png"></td>
								<td><?php echo $v['sort']; ?></td>
								<td>
									<a href="member/group_edit/<?php echo $v['id']; ?>" class="btn btn-primary">编辑</a>
								</td>
							</tr>
						<?php endforeach;?>
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