<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">会员管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="member/member_list">会员列表</a> >> <?php echo ($member_info['id'] != 0) ? '编辑会员：'.$member_info['name'] : '添加会员'; ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="form-horizontal col-lg-8 member_form" role="form">
					<div class="form-group">
						<label class="col-sm-2 control-label">会员名称</label>
						<div class="col-sm-10">
							<input class="form-control" name="name" value="<?php echo my_echo($member_info['name']); ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">密码</label>
						<div class="col-sm-10">
							<input type="password" class="form-control" name="password">
						</div>
					</div>
					<div class="form-group" onclick="show_say()" id ="group">
						<label class="col-sm-2 control-label">会员组</label>
						<div class="col-sm-10">
							<select class="form-control" name="gid">
								<?php
								$gid = my_echo($member_info['gid'], '');
								foreach($group_list as $gv):
								?>
								<option value="<?php echo $gv['id']; ?>" <?php if($gv['id'] == $gid): ?>selected<?php endif; ?>><?php echo $gv['name']; ?></option>
								<?php 
								endforeach;
								 ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">是否公司人员</label>
						<div class="col-sm-10">
							<label class="radio-inline">
								<input type="radio" name="is_company" value="0" <?php echo my_echo($member_info['is_company'], 0) ? '' : 'checked'; ?> > 否
							</label>
							<label class="radio-inline">
								<input type="radio" name="is_company" value="1" <?php echo my_echo($member_info['is_company'], 0) ? 'checked' : ''; ?> > 是
							</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">手机号</label>
						<div class="col-sm-10">
							<input class="form-control" name="phone" value="<?php echo my_echo($member_info['phone']); ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">QQ号</label>
						<div class="col-sm-10">
							<input class="form-control" type="text" name="qq" value="<?php echo my_echo($member_info['qq']); ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">是否开户</label>
						<div class="col-sm-10">
							<?php
								$is_open = my_echo($member_info['is_open'], 0);
								if(!$is_open):
							?>
							<label class="radio-inline">
								<input type="radio" name="is_open" value="0" <?php echo $is_open ? '' : 'checked'; ?> > 否
							</label>
							<label class="radio-inline">
								<input type="radio" name="is_open" id="open" value="1" <?php echo $is_open ? 'checked' : ''; ?> > 是
							</label>
						<?php else: ?>
							<i style="color: red;">已开户</i>
						<?php endif; ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">专属客服</label>
						<div class="col-sm-10">
							<select class="form-control" name="cid">
								<?php
								$cid = my_echo($member_info['cid'], '');
								foreach($customer_service_list as $cv):
								?>
								<option value="<?php echo $cv['id']; ?>" <?php if($cv['id'] == $cid): ?>selected<?php endif; ?>><?php echo $cv['nick']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">专属老师</label>
						<div class="col-sm-10">
							<select class="form-control" name="tid">
								<?php
								$tid = my_echo($member_info['tid'], '');
								foreach($teacher_service_list as $cv):
								?>
								<option value="<?php echo $cv['id']; ?>" <?php if($cv['id'] == $tid): ?>selected<?php endif; ?>><?php echo $cv['nick']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">来源</label>
						<div class="col-sm-10">
							<select class="form-control" name="source">
								<option value="" <?php if(!my_echo($member_info['source'])): ?>selected<?php endif; ?>>无</option>
								<?php
								foreach($source as $v):
								?>
								<option value="<?php echo $v['source']; ?>" <?php if($v['source'] == my_echo($member_info['source'])): ?>selected<?php endif; ?>><?php echo $v['source']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">关键词</label>
						<div class="col-sm-10">
							<input class="form-control" type="text" name="keyword" value="<?php echo my_echo($member_info['keyword']); ?>">
						</div>
					</div>
					<div class="form-group" id="say" style="display:none;">
						<label class="col-sm-2 control-label">欢迎语</label>
						<div class="col-sm-10">
							<textarea class="form-control" name="say" placeholder="直播室聊天中自动发送" value=""><?php echo my_echo($member_info['say']); ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" name="id" value="<?php echo $member_info['id']; ?>">
							<input type="hidden" name="is_mobile_reg" value="<?php echo my_echo($member_info['is_mobile_reg'],0); ?>">
							<button type="button" class="btn btn-primary" onclick="member_update()">保存</button>
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

function member_update(){
	$.post(admin.url+'member/member_update',
	$('.member_form').serialize(),
	function (data){
		data = $.parseJSON(data);
		if(data.status){
			alert('会员保存成功');
			location.href = admin.url+'member/member_list';
		}else{
			alert(data.msg);
		}
	})
}

show_say();
function show_say(){
	if($('#group option:selected').val() == 0){
		$('#say').show();
	}else{
		$('#say').hide();
	}
}


</script>