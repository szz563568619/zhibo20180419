<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">视频直播切换</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="javascript:;">视频直播切换</a>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="form-horizontal col-lg-8" role="form">
					<div class="form-group">
						<label class="col-sm-2 control-label">是否使用OBS视频代码</label>
						<div class="col-sm-10">
							<?php $is_obs_video = my_echo($room_info['is_obs_video'], 0); ?>
							<label class="radio-inline">
								<input type="radio" name="is_obs_video" value="1" <?php if($is_obs_video == 1): ?>checked<?php endif; ?>> 是
							</label>
							<label class="radio-inline">
								<input type="radio" name="is_obs_video" value="0" <?php if($is_obs_video == 0): ?>checked<?php endif; ?>> 否
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" name="old_id" value="<?php echo my_echo($room_info['id'], 0); ?>">
							<button type="button" class="btn btn-primary" onclick="update_room()">保存</button>
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

function update_room(){
	$.post(admin.url+'obsvideo/update_room',
	$('form').serialize(),
	function (result){
		result = $.parseJSON(result);
		if(result.status){
			alert('保存成功');
			location.href = admin.url+'obsvideo/';
		}else{
			alert(result.msg);
		}
		
	})
}

</script>