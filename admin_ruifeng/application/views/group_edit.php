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
				<a href="member/group">会员组列表</a> >> <?php echo ($info['id'] != 0) ? '编辑会员组：'.$info['name'] : '添加会员组'; ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="form-horizontal col-lg-8" role="form">
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">会员组名称</label>
						<div class="col-sm-10">
							<input class="form-control" name="name" id="name" value="<?php echo $info['name'] ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="sort" class="col-sm-2 control-label">排序</label>
						<div class="col-sm-10">
							<input class="form-control" name="sort" id="sort" value="<?php echo $info['sort'] ?>">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" name="id" value="<?php echo $info['id']; ?>">
							<button type="button" class="btn btn-primary" onclick="group_update()">保存</button>
							<button type="reset" class="btn btn-danger">重置</button>
						</div>
					</div>
				</form>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-6 -->
</div>
<!-- /.row -->

<script type="text/javascript">

function group_update(){
	$.post(admin.url+'member/group_update',
	$('form').serialize(),
	function (){
		alert('会员组保存成功');
		location.href = admin.url+'member/group';
	})
}

</script>