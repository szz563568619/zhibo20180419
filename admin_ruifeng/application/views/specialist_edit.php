<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">专家管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="specialist/specialist_list/">专家列表</a> >> <?php echo $info['id'] != -1 ? $info['name'] : '添加专家'; ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="form-horizontal col-lg-10 specialist_form" role="form">
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">专家名</label>
						<div class="col-sm-10">
							<input class="form-control" name="name" id="name" value="<?php echo my_echo($info['name']) ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">所属房间</label>
						<div class="col-sm-10">
							<select class="form-control" name="rid">
								<?php foreach($room_list as $v): ?>
								<option value="<?php echo $v['id']; ?>" <?php if($v['id'] == my_echo($info['rid'])): ?>selected<?php endif; ?>><?php echo $v['name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="sort" class="col-sm-2 control-label">排序</label>
						<div class="col-sm-2">
							<input class="form-control" name="sort" id="sort" value="<?php echo my_echo($info['sort']) ?>">
						</div>
					</div>
					<?php echo $info['avatar']; ?>
					<div class="form-group">
						<label for="content" class="col-sm-2 control-label">简介</label>
						<div class="col-sm-10">
							<textarea class="form-control" name="content" id="content" rows="10" style="width:100%;"><?php echo my_echo($info['content']); ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" name="id" value="<?php echo $info['id']; ?>">
							<button type="button" class="btn btn-primary" onclick="specialist_update()">保存</button>
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

function specialist_update(){
	$.post(admin.url+'specialist/specialist_update',
	$('.specialist_form').serialize(),
	function (){
		location.href = admin.url+'specialist/specialist_list';
	})
}

</script>