<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">升级通告</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">添加/编辑 通告</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form id="upgrade_form" class="form-horizontal col-lg-6" role="form">
					<div class="form-group">
						<label class="col-sm-2 control-label">用户名</label>
						<div class="col-sm-10">
							<input class="form-control form_name" name="name" value="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">所升等级</label>
						<div class="col-sm-10">
							<select class="form-control form_gid" name="gid">
								<?php foreach($group_list as $v): ?>
								<option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" class="form_upgrade_id" name="id" value="0">
							<button type="button" class="btn btn-primary" onclick="update_upgrade()">保存</button>
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
			<div class="panel-heading">通告列表</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>用户名</th>
								<th>所升等级</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($upgrade_list as $v): ?>
							<tr class="upgrade_<?php echo $v['id']; ?>">
								<td class="name"><?php echo $v['name']; ?></td>
								<td class="gname"><?php echo $v['gname']; ?></td>
								<td>
									<input type="hidden" class="gid" value="<?php echo $v['gid']; ?>">
									<button class="btn btn-primary" onclick="edit_upgrade(<?php echo $v['id']; ?>)">编辑</button>
									<button type="button" class="btn btn-danger" onclick="del_upgrade(<?php echo $v['id']; ?>)">删除</button>
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

function edit_upgrade(id){
	var dom = $('.upgrade_'+id);
	$('.form_name').val(dom.find('.name').html());
	$('.form_upgrade_id').val(id);
	$(".form_gid option[value='"+dom.find('.gid').val()+"']").attr('selected', true);
}

function update_upgrade(){
	if( $('.form_name').val() == '' ){
		alert('请输入用户名');
	}else{
		$.post(admin.url+'upgrade/update_upgrade',
		$('#upgrade_form').serialize(),
		function (){
			location.reload();
		})
	}
}

function del_upgrade(id){
	if(confirm('确认删除该通告？该操作不可恢复，请谨慎操作！')){
		$.post(admin.url+'upgrade/del_upgrade',
		{'id': id},
		function (){
			location.reload();
		})
	}
}

</script>