<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">金牌策略</h3>
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
				<button class="btn btn-primary" onclick="add_strategy();">添加策略</button>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="table-responsive form_strategy">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>策略名</th>
								<th>标题</th>
								<th>对应等级</th>
								<th>对应老师</th>
								<th>仓位</th>
								<th>止盈位</th>
								<th>止损位</th>
								<th>做单理由</th>
								<th>时间</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody class="strategy_list">
						<?php foreach($strategy_list as $v): ?>
							<tr class="strategy_list_<?php echo $v['id']; ?>">
								<td>
									<input class="form-control" name="name[]" value="<?php echo $v['name']; ?>">
								</td>
								<td>
									<input class="form-control" name="title[]" value="<?php echo $v['title']; ?>">
								</td>
								<td>
									<select class="form-control" name="gid[]" style="width:110px;">
										<?php foreach($group_list as $gv): ?>
										<option value="<?php echo $gv['id']; ?>" <?php if($v['gid'] == $gv['id']): ?>selected<?php endif; ?>><?php echo $gv['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<select class="form-control" name="tid[]" style="width:110px;">
										<option value=""></option>
										<?php foreach($teacher_list as $tv): ?>
										<option value="<?php echo $tv['id']; ?>" <?php if($v['tid'] == $tv['id']): ?>selected<?php endif; ?>><?php echo $tv['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<input class="form-control" name="position[]" value="<?php echo $v['position']; ?>">
								</td>
								<td>
									<input class="form-control" name="profit[]" value="<?php echo $v['profit']; ?>">
								</td>
								<td>
									<input class="form-control" name="stop[]" value="<?php echo $v['stop']; ?>">
								</td>
								<td>
									<input class="form-control" name="reason[]" value="<?php echo $v['reason']; ?>">
								</td>
								<td>
									<input class="form-control" name="time[]" value="<?php echo $v['time']; ?>" style="width:165px;">
								</td>
								<td>
									<button type="button" class="btn btn-danger" onclick="del_strategy('<?php echo $v['id']; ?>', this)">删除</button>
									<input type="hidden" name="strategy_id[]" value="<?php echo $v['id']; ?>">
								</td>
							</tr>
						<?php endforeach ?>
						</tbody>
					</table>
					<button type="button" class="btn btn-success" onclick="update_strategy()">保存</button>
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
<table class="tpl" style="display:none;">
	<tr class="strategy_list_0">
		<td>
			<input class="form-control" name="name[]">
		</td>
		<td>
			<input class="form-control" name="title[]">
		</td>
		<td>
			<select class="form-control" name="gid[]" style="width:110px;">
				<?php foreach($group_list as $gv): ?>
				<option value="<?php echo $gv['id']; ?>"><?php echo $gv['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</td>
		<td>
			<select class="form-control" name="tid[]" style="width:110px;">
				<option value=""></option>
				<?php foreach($teacher_list as $tv): ?>
				<option value="<?php echo $tv['id']; ?>"><?php echo $tv['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</td>
		<td>
			<input class="form-control" name="position[]">
		</td>
		<td>
			<input class="form-control" name="profit[]">
		</td>
		<td>
			<input class="form-control" name="stop[]">
		</td>
		<td>
			<input class="form-control" name="reason[]">
		</td>
		<td>
			<input class="form-control" name="time[]" style="width:165px;">
		</td>
		<td>
			<button type="button" class="btn btn-danger" onclick="del_strategy(0, this)">删除</button>
			<input type="hidden" name="strategy_id[]" value="0">
		</td>
	</tr>
</table>

<script>

function select_room(obj){
	location.href = admin.url+'strategy/strategy_list/'+$(obj).find('option:selected').val();
}

function add_strategy(){
	$('.strategy_list').append($('.tpl tr').clone(true, true));
}

function update_strategy(){
	$.post(admin.url+'strategy/update_strategy',
	$('.form_strategy').serialize(),
	function (){
		alert('保存成功');
		location.reload();
	})
}
	
function del_strategy(id, obj){
	if(id == 0)
	{
		$($($(obj).parent()).parent()).remove();
		return;
	}
	if(confirm('确认删除该策略？该操作不可恢复，请谨慎操作！')){
		$.post(admin.url+'strategy/del_strategy',
		{'id': id},
		function (){
			alert('删除成功');
			location.reload();
		})
	}
}

</script>