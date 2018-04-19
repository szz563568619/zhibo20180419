<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">老师管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">老师信息</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form id="teacher_form" class="form-horizontal col-lg-8" role="form">
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">老师名称</label>
						<div class="col-sm-10">
							<input class="form-control form_teacher_name" name="name" value="">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" class="form_teacher_id" name="id" value="0">
							<button type="button" class="btn btn-primary" onclick="set_teacher()">保存</button>
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

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">老师列表</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>老师名称</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($teacher as $v): ?>
							<tr class="teacher_<?php echo $v['id']; ?>">
								<td class="name"><?php echo $v['name']; ?></td>
								<td>
									<button class="btn btn-primary" onclick="update_teacher(<?php echo $v['id']; ?>)">编辑</button>
									<button type="button" class="btn btn-danger" onclick="del_teacher(<?php echo $v['id']; ?>)">删除</button>
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

function update_teacher(id){
	var name = $('.teacher_'+id+' .name').html();
	$('.form_teacher_name').val(name);
	$('.form_teacher_id').val(id);
}

function set_teacher(){
	if( $('.form_teacher_name').val() == '' ){
		alert('请输入老师名称');
	}else{
		$.post(admin.url+'course/set_teacher',
		$('#teacher_form').serialize(),
		function (){
			location.reload();
		})
	}
}

function del_teacher(id){
	if(confirm('确认删除该老师？该操作不可恢复，请谨慎操作！')){
		$.post(admin.url+'course/del_teacher',
		{'id': id},
		function (){
			location.reload();
		})
	}
}

</script>