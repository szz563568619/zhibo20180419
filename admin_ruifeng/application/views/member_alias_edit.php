<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">水军小号管理</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="member/member_alias/<?php echo $mid; ?>"><?php echo $member_name; ?></a> >> <?php echo ($alias_id != 0) ? '编辑小号：'.$info['name'] : '添加小号'; ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="form-horizontal col-lg-8 member_form" role="form">
					<div class="form-group">
						<label class="col-sm-2 control-label">小号名称</label>
						<div class="col-sm-10">
							<input class="form-control" name="name" value="<?php echo my_echo($info['name']); ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">会员组</label>
						<div class="col-sm-10">
							<select class="form-control" name="gid">
								<?php
								$gid = my_echo($info['gid'], '');
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
						<div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" name="alias_id" value="<?php echo $alias_id; ?>">
							<input type="hidden" name="mid" value="<?php echo $mid; ?>">
							<button type="button" class="btn btn-primary" onclick="alias_update()">保存</button>
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

function alias_update(){
	$.post(admin.url+'member/alias_update',
	$('.member_form').serialize(),
	function (data){
		data = $.parseJSON(data);
		if(data.status){
			alert('小号保存成功');
			location.href = admin.url+'member/member_alias/<?php echo $mid; ?>';
		}else{
			alert(data.msg);
		}
	})
}

</script>