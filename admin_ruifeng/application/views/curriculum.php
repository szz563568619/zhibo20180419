<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">课程管理</h3>
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
				<button class="btn btn-primary" onclick="add_curriculum();">添加课程</button>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="table-responsive form_curriculum">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>时间</th>
								<th>栏目名称</th>
								<th>周一</th>
								<th>周二</th>
								<th>周三</th>
								<th>周四</th>
								<th>周五</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody class="curriculum_list">
						<?php foreach($curriculum_list as $v): ?>
							<tr class="curriculum_list_<?php echo $v['id']; ?>">
								<td><input class="form-control" style="display:inline;width:85px;" name="start_time[]" value="<?php echo $v['start_time']; ?>">---<input class="form-control" style="display:inline;width:85px;" name="end_time[]" value="<?php echo $v['end_time']; ?>"></td>
								<td><input class="form-control" style="display:inline;" name="curr_name[]" value="<?php echo $v['curr_name']; ?>"></td>
								<td>
									<select class="form-control" name="monday[]" style="width:100%;">
										<?php foreach($teacher as $tv): ?>
										<option value="<?php echo $tv['id']; ?>" <?php if($v['monday'] == $tv['id']): ?>selected<?php endif; ?>><?php echo $tv['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<select class="form-control" name="tuesday[]" style="width:100%;">
										<?php foreach($teacher as $tv): ?>
										<option value="<?php echo $tv['id']; ?>" <?php if($v['tuesday'] == $tv['id']): ?>selected<?php endif; ?>><?php echo $tv['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<select class="form-control" name="wednesday[]" style="width:100%;">
										<?php foreach($teacher as $tv): ?>
										<option value="<?php echo $tv['id']; ?>" <?php if($v['wednesday'] == $tv['id']): ?>selected<?php endif; ?>><?php echo $tv['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<select class="form-control" name="thursday[]" style="width:100%;">
										<?php foreach($teacher as $tv): ?>
										<option value="<?php echo $tv['id']; ?>" <?php if($v['thursday'] == $tv['id']): ?>selected<?php endif; ?>><?php echo $tv['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<select class="form-control" name="friday[]" style="width:100%;">
										<?php foreach($teacher as $tv): ?>
										<option value="<?php echo $tv['id']; ?>" <?php if($v['friday'] == $tv['id']): ?>selected<?php endif; ?>><?php echo $tv['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<button type="button" class="btn btn-danger" onclick="del_curriculum('<?php echo $v['id']; ?>', this)">删除</button>
									<input type="hidden" name="curriculum_id[]" value="<?php echo $v['id']; ?>">
								</td>
							</tr>
						<?php endforeach ?>
						</tbody>
					</table>
					<button type="button" class="btn btn-success" onclick="update_curriculum()">保存</button>
					<input type="hidden" name="room_id" value="<?php echo $room_id; ?>" >
				</form>
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
	location.href = admin.url+'course/curriculum/'+room_id;
}

function add_curriculum(){
	var html = '<tr class="curriculum_list_0"><td><input class="form-control" style="display:inline;width:85px;" name="start_time[]">---<input class="form-control" style="display:inline;width:85px;" name="end_time[]"></td><td><input class="form-control" style="display:inline;" name="curr_name[]" value=""></td><td><select class="form-control" name="monday[]" style="width:100%;"><?php foreach($teacher as $tv): ?><option value="<?php echo $tv['id']; ?>"><?php echo $tv['name']; ?></option><?php endforeach; ?></select></td><td><select class="form-control" name="tuesday[]" style="width:100%;"><?php foreach($teacher as $tv): ?><option value="<?php echo $tv['id']; ?>"><?php echo $tv['name']; ?></option><?php endforeach; ?></select></td><td><select class="form-control" name="wednesday[]" style="width:100%;"><?php foreach($teacher as $tv): ?><option value="<?php echo $tv['id']; ?>"><?php echo $tv['name']; ?></option><?php endforeach; ?></select></td><td><select class="form-control" name="thursday[]" style="width:100%;"><?php foreach($teacher as $tv): ?><option value="<?php echo $tv['id']; ?>"><?php echo $tv['name']; ?></option><?php endforeach; ?></select></td><td><select class="form-control" name="friday[]" style="width:100%;"><?php foreach($teacher as $tv): ?><option value="<?php echo $tv['id']; ?>"><?php echo $tv['name']; ?></option><?php endforeach; ?></select></td><td><button type="button" class="btn btn-danger" onclick="del_curriculum(0, this)">删除</button><input type="hidden" name="curriculum_id[]" value="0"></td></tr>';
	$('.curriculum_list').append(html);
}

function update_curriculum(){
	$.post(admin.url+'course/update_curriculum',
	$('.form_curriculum').serialize(),
	function (){
		alert('保存成功');
		location.reload();
	})
}
	
function del_curriculum(id, obj){
	if(id == 0)
	{
		$($($(obj).parent()).parent()).remove();
		return;
	}
	if(confirm('确认删除该课程？该操作不可恢复，请谨慎操作！')){
		$.post(admin.url+'course/del_curriculum',
		{'id': id},
		function (){
			alert('删除成功');
			location.reload();
		})
	}
}

</script>