<style>.examine, .customer{display: none;}</style>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">管理员管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="admin/">管理员列表</a> >> <?php echo $admin['id'] === 0 ? '添加管理员' : '编辑管理员--'.$admin['nick']; ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="form-horizontal col-lg-8" role="form">
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">账号</label>
						<div class="col-sm-10">
							<input class="form-control" name="name" id="name" value="<?php echo $admin['name']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-2 control-label">密码</label>
						<div class="col-sm-10">
							<input type="password" class="form-control" name="password" id="password">
							<?php if($admin['id'] != 0): ?>
							<span class="label label-info">留空则不修改密码</span>
							<?php endif; ?>
						</div>
					</div>
					<div class="form-group">
						<label for="nick" class="col-sm-2 control-label">昵称</label>
						<div class="col-sm-10">
							<input class="form-control" name="nick" id="nick" value="<?php echo $admin['nick']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="permission" class="col-sm-2 control-label">权限</label>
						<div class="col-sm-10">
							<div class="btn-group" >
								<?php $permission = explode(',', $admin['permission']); ?>
								<label class="btn btn-success"><input type="checkbox" name="permission[]" value="base" <?php if(in_array('base', $permission)): ?>checked<?php endif; ?>> 基本管理</label>
								<label class="btn btn-success"><input class="detail" data-class="examine" type="checkbox" name="permission[]" value="examine" <?php if(in_array('examine', $permission)): ?>checked<?php endif; ?>> 留言审核</label>
								<label class="btn btn-success"><input type="checkbox" name="permission[]" value="member" <?php if(in_array('member', $permission)): ?>checked<?php endif; ?>> 会员管理</label>
								<label class="btn btn-success"><input type="checkbox" name="permission[]" value="admin" <?php if(in_array('admin', $permission)): ?>checked<?php endif; ?>> 管理员</label>
								<label class="btn btn-success"><input type="checkbox" name="permission[]" value="speaker" <?php if(in_array('speaker', $permission)): ?>checked<?php endif; ?>> 讲课老师 </label>
								<label class="btn btn-success"><input class="detail" id="customer" data-class="customer" type="checkbox" name="permission[]" value="customer" <?php if(in_array('customer', $permission)): ?>checked<?php endif; ?>> 客服 </label>
								<label class="btn btn-success"><input class="detail" data-class="seo" type="checkbox" name="permission[]" value="seo" <?php if(in_array('seo', $permission)): ?>checked<?php endif; ?>> 推广 </label>
								<label class="btn btn-success"><input class="detail" id="teacher" data-class="teacher" type="checkbox" name="permission[]" value="teacher" <?php if(in_array('teacher', $permission)): ?>checked<?php endif; ?>> 老师直播管理 </label>
								<label class="btn btn-success"><input class="detail" id="dazi" data-class="dazi" type="checkbox" name="permission[]" value="dazi" <?php if(in_array('dazi', $permission)): ?>checked<?php endif; ?>> 打字员直播管理 </label>
							</div>
						</div>
					</div>
					<div class="form-group examine">
						<label for="rid" class="col-sm-2 control-label">审核房间</label>
						<div class="col-sm-10">
							<div class="btn-group" >
								<?php
								$rid = explode(',', $admin['rid']);
								foreach($rid_list as $v):
								?>
								<label class="btn btn-success"><input type="checkbox" name="rid[]" value="<?php echo $v['id']; ?>" <?php if(in_array($v['id'], $rid)): ?>checked<?php endif; ?>> <?php echo $v['name']; ?></label>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="sex" class="col-sm-2 control-label">性别</label>
						<label class="radio-inline">
							<input name="sex" id="sex" type="radio" value="1" <?php echo $admin['sex']==1 ? 'checked':''; ?>>男
						</label>
						<label class="radio-inline">
							<input name="sex" id="sex" type="radio" value="0" <?php echo $admin['sex']==0 ? 'checked':''; ?>>女
						</label>
					</div>
					<div class="form-group customer noteacher">
						<label for="wellcome" class="col-sm-2 control-label">欢迎语</label>
						<div class="col-sm-10">
							<input class="form-control" name="wellcome" id="wellcome" value="<?php echo $admin['wellcome']; ?>">
						</div>
					</div>
					<div class="form-group customer">
						<label for="qq" class="col-sm-2 control-label">qq号</label>
						<div class="col-sm-10">
							<input class="form-control" type="number" name="qq" id="qq" value="<?php echo $admin['qq']; ?>">
						</div>
					</div>
					<div class="form-group customer">
						<label for="phone" class="col-sm-2 control-label">联系电话</label>
						<div class="col-sm-10">
							<input class="form-control" type="number" name="phone" id="phone" value="<?php echo $admin['phone']; ?>">
						</div>
					</div>
					<div class="form-group customer">
						<label for="intro" class="col-sm-2 control-label">介绍</label>
						<div class="col-sm-10">
							<textarea class="form-control" name="intro" id="intro"><?php echo $admin['intro']; ?></textarea>
						</div>
					</div>
					<?php echo $admin['headimg']; ?>
					<div class="form-group customer">
						<label for="is_hot" class="col-sm-2 control-label">是否推荐(用于APP)</label>
						<label class="radio-inline">
							<input name="is_hot" id="is_hot" type="radio" value="1" <?php echo $admin['is_hot']==1 ? 'checked':''; ?>>是
						</label>
						<label class="radio-inline">
							<input name="is_hot" id="is_hot" type="radio" value="0" <?php echo $admin['is_hot']==0 ? 'checked':''; ?>>否
						</label>
					</div>
					<div class="form-group customer nocustomer">
						<label for="solve" class="col-sm-2 control-label">解决问题数量(用于APP)</label>
						<div class="col-sm-10">
							<input class="form-control" type="text" name="solve" id="solve" value="<?php echo $admin['solve']; ?>" placeholder="如：100">
						</div>
					</div>
					<div class="form-group customer nocustomer">
						<label for="jietao" class="col-sm-2 control-label">解套成功率(用于APP)</label>
						<div class="col-sm-10">
							<input class="form-control" type="text" name="jietao" id="jietao" value="<?php echo $admin['jietao']; ?>" placeholder="如：90%">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
							<button type="button" class="btn btn-primary" onclick="save_admin()">保存</button>
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

<script>

$(function (){
	detail_init();
	$('.detail').click(function (){allow_one($(this));detail_init();})
})

function detail_init(){
	$('.detail').each(function(){
		var cls = $(this).data('class');
		if($(this).is(':checked')){
			if(cls == 'teacher'){
				$('.customer').css('display', 'block');
				$('.noteacher').css('display', 'none');
			}else if(cls == 'customer'){
				$('.customer').css('display', 'block');
				$('.nocustomer').css('display', 'none');
			}else{
				$('.'+cls).css('display', 'block');
			}
		}else{
			$('.'+cls).css('display', 'none');
		}
	})
}
	
function save_admin(){
	$.post(admin.url+'admin/save_admin',
	$('form').serialize(),
	function (result){
		result = $.parseJSON(result);
		if(result.status){
			alert('保存成功');
			location.href = admin.url+'admin';
		}else{
			alert(result.msg);
		}
	})

}

//限制选择
function allow_one(dom){
	var count = 0;
	var limit_one = ['customer','teacher','dazi'];
	for(var i in limit_one){
		var selector = $('#'+limit_one[i]);
		if(selector.is(':checked')){
			count++;
		}
		if(count > 1){
			alert('客服/老师直播管理/打字员直播管理,只能勾选一个！');
			dom.attr("checked", false);
			return;
		}
	}
}

</script>