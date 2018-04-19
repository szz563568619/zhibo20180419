<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">房间管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="room/">房间列表</a> >> <?php echo isset($room_info['id']) ? '房间'.$room_info['id'] : '添加房间'; ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="form-horizontal col-lg-8" role="form">
					<div class="form-group">
						<label for="id" class="col-sm-2 control-label">房间ID</label>
						<div class="col-sm-10">
							<input class="form-control" name="id" id="id" value="<?php echo my_echo($room_info['id']) ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">房间名</label>
						<div class="col-sm-10">
							<input class="form-control" name="name" id="name" value="<?php echo my_echo($room_info['name']) ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="title" class="col-sm-2 control-label">标题</label>
						<div class="col-sm-10">
							<input class="form-control" name="title" id="title" value="<?php echo my_echo($room_info['title']) ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="video" class="col-sm-2 control-label">展视互动视频代码</label>
						<div class="col-sm-10">
							<textarea class="form-control" name="video" id="video" rows="5"><?php echo my_echo($room_info['video']) ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="obs_video" class="col-sm-2 control-label">OBS视频代码</label>
						<div class="col-sm-10">
							<textarea class="form-control" name="obs_video" id="obs_video" rows="5"><?php echo my_echo($room_info['obs_video']) ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">是否开启消息删除</label>
						<div class="col-sm-10">
							<label class="radio-inline">
								<input type="radio" name="del_public_msg" value="1" <?php if(my_echo($room_info['del_public_msg'],0) === 1): ?>checked<?php endif; ?>> 是
							</label>
							<label class="radio-inline">
								<input type="radio" name="del_public_msg" value="0" <?php if(my_echo($room_info['del_public_msg'],0) === 0): ?>checked<?php endif; ?>> 否
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="pwd" class="col-sm-2 control-label">房间密码</label>
						<div class="col-sm-10">
							<input class="form-control" name="pwd" id="pwd" value="<?php echo my_echo($room_info['pwd']) ?>" placeholder="留空则无密码">
						</div>
					</div>
					<div class="form-group">
						<label for="keywords" class="col-sm-2 control-label">关键词</label>
						<div class="col-sm-10">
							<input class="form-control" name="keywords" id="keywords" value="<?php echo my_echo($room_info['keywords']) ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="description" class="col-sm-2 control-label">描述</label>
						<div class="col-sm-10">
							<textarea class="form-control" name="description" id="description" rows="5"><?php echo my_echo($room_info['description']) ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="shikuang" class="col-sm-2 control-label">实况直播</label>
						<div class="col-sm-10">
							<input class="form-control" name="shikuang" id="shikuang" value="<?php echo my_echo($room_info['shikuang']) ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="forbidden" class="col-sm-2 control-label">屏蔽关键词</label>
						<div class="col-sm-10">
							<input class="form-control" name="forbidden" id="forbidden" value="<?php echo my_echo($room_info['forbidden']) ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="statistics" class="col-sm-2 control-label">统计代码</label>
						<div class="col-sm-10">
							<textarea class="form-control" name="statistics" id="statistics" rows="5"><?php echo my_echo($room_info['statistics']) ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="qq" class="col-sm-2 control-label">客服QQ号</label>
						<div class="col-sm-10">
							<input class="form-control" name="qq" id="qq" value="<?php echo my_echo($room_info['qq']) ?>">
						</div>
					</div>
					<div class="form-group">
                        <label for="phone" class="col-sm-2 control-label">客服电话号</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="phone" id="phone" value="<?php echo my_echo($room_info['phone']) ?>">
                        </div>
                    </div>
					<div class="form-group">
						<label for="qq_code" class="col-sm-2 control-label">客服QQ代码</label>
						<div class="col-sm-10">
							<textarea class="form-control" name="qq_code" id="qq_code" rows="5"><?php echo my_echo($room_info['qq_code']) ?></textarea>
						</div>
					</div>
					<div class="form-group">
                        <label for="initpeo" class="col-sm-2 control-label">在线人数初始值</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="initpeo" id="v" value="<?php echo my_echo($room_info['initpeo']) ?>">
                        </div>
                    </div>
					<div class="form-group">
						<label class="col-sm-2 control-label">自动发送管理员消息</label>
						<div class="col-sm-10">
							<?php $auto_adminmsg = my_echo($room_info['auto_adminmsg'], 0); ?>
							<label class="radio-inline">
								<input type="radio" name="auto_adminmsg" value="1" <?php if($auto_adminmsg == 1): ?>checked<?php endif; ?>> 是
							</label>
							<label class="radio-inline">
								<input type="radio" name="auto_adminmsg" value="0" <?php if($auto_adminmsg == 0): ?>checked<?php endif; ?>> 否
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
	$.post(admin.url+'room/update_room',
	$('form').serialize(),
	function (result){
		result = $.parseJSON(result);
		if(result.status){
			alert('房间保存成功');
			location.href = admin.url+'room/';
		}else{
			alert(result.msg);
		}
		
	})
}

</script>