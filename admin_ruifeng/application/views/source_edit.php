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
				<a href="source/">来源列表</a> >> <?php echo isset($source_info['id']) ? '来源:'.$source_info['source'] : '添加来源'; ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="form-horizontal col-lg-8" role="form">
					<div class="form-group">
						<label for="host" class="col-sm-2 control-label">域名</label>
						<div class="col-sm-10">
							<input class="form-control" name="host" id="host" value="<?php echo my_echo($source_info['host']) ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="source" class="col-sm-2 control-label">来源名</label>
						<div class="col-sm-10">
							<input class="form-control" name="source" id="source" value="<?php echo my_echo($source_info['source']) ?>">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" name="id" value="<?php echo my_echo($source_info['id'], 0); ?>">
							<button type="button" class="btn btn-primary" onclick="update_source()">保存</button>
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

function update_source(){
	$.post(admin.url+'source/update_source',
	$('form').serialize(),
	function (){
		location.href = admin.url+'source/';	
	})
}

</script>