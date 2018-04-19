<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">文章管理</h1>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="zaobao" class="btn btn-primary">文章列表</a> >> 编辑文章 >> <?php echo isset($zaobao_info['title']) ? $zaobao_info['title'] : '添加文章'; ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="form-horizontal col-lg-12" role="form">
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">文章名</label>
						<div class="col-sm-10">
							<input class="form-control" name="title" id="name" value="<?php echo my_echo($zaobao_info['title']) ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="container" class="col-sm-2 control-label">文章正文</label>
						<div class="col-sm-10">
							<script id="container" name="content" type="text/plain">
								<?php echo my_echo($zaobao_info['content']); ?>
							</script>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="hidden" name="id" value="<?php echo my_echo($zaobao_info['id'], 0); ?>">
							<button type="button" class="btn btn-primary" onclick="save_article()">保存</button>
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
<!-- ueditor配置文件 -->
<script src="<?php echo $site_url; ?>plugins/ueditor/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script src="<?php echo $site_url; ?>plugins/ueditor/ueditor.all.js"></script>
<!-- 语言包文件(建议手动加载语言包，避免在ie下，因为加载语言失败导致编辑器加载失败) -->
<script src="<?php echo $site_url; ?>plugins/ueditor/lang/zh-cn/zh-cn.js"></script>

<script>
	var editor = UE.getEditor('container', {'initialFrameHeight' : 600});
function save_article(){
	$.post(admin.url+'zaobao/zaobao_update',
	$('form').serialize(),
	function (d){
		d = $.parseJSON(d);
		if(d.status){
			alert('文章保存成功');
			location.reload();
		}else{
			alert(d.msg);
		}
		
	})
}
</script>