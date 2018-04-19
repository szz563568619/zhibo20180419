<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">本周重点数据</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<button class="btn btn-primary" onclick="add_data()">添加数据</button>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="table-responsive form_curriculum">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>星期</th>
								<th>数据</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody class="curriculum_list">
						<?php foreach($data_list as $v): ?>
							<tr class="curriculum_list_<?php echo $v['id']; ?>">
								<td>
									<select class="form-control" name="week[]" style="width:100%;">
										<?php 
										$week = array(1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五');
										for($i=1;$i<=5;$i++): 
											?>
										<option value="<?php echo $i; ?>" <?php if($v['week'] == $i): ?>selected<?php endif; ?>><?php echo $week[$i]; ?></option>
										<?php endfor; ?>
									</select>
								</td>
								<td><input class="form-control" style="display:inline;" name="info[]" value="<?php echo $v['info']; ?>"></td>
								<td>
									<button type="button" class="btn btn-danger" onclick="del_data('<?php echo $v['id']; ?>', this)">删除</button>
									<input type="hidden" name="id[]" value="<?php echo $v['id']; ?>">
								</td>
							</tr>
						<?php endforeach ?>
						</tbody>
					</table>
					<button type="button" class="btn btn-success" onclick="update_data()">保存</button>
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

function add_data(){
	var html = '<tr class="curriculum_list_0"><td><select class="form-control" name="week[]" style="width:100%;"><?php $week = array(1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五');for($i=1;$i<=5;$i++): ?><option value="<?php echo $i; ?>"><?php echo $week[$i]; ?></option><?php endfor; ?></select></td><td><input class="form-control" style="display:inline;" name="info[]" value=""></td><td><button type="button" class="btn btn-danger" onclick="del_data(0, this)">删除</button><input type="hidden" name="id[]" value="0"></td></tr>';
	$('.curriculum_list').append(html);
}

function update_data(){
	$.post(admin.url+'course/update_data',
	$('.form_curriculum').serialize(),
	function (){
		alert('保存成功');
		location.reload();
	})
}
	
function del_data(id, obj){
	if(id == 0)
	{
		$($($(obj).parent()).parent()).remove();
		return;
	}
	if(confirm('确认删除该数据？该操作不可恢复，请谨慎操作！')){
		$.post(admin.url+'course/del_data',
		{'id': id},
		function (){
			alert('删除成功');
			location.reload();
		})
	}
}

</script>