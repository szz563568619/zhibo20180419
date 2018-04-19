<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">老师战法管理</h1>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="tactics" class="btn btn-primary">战法列表</a> >> <?php echo isset($tactics_info['title']) ? '编辑战法 >> '.$tactics_info['title'] : '添加战法'; ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<form class="form-horizontal col-lg-12" role="form">
					<div class="form-group">
						<label for="title" class="col-sm-1 control-label">选择老师</label>
						<div class="col-sm-10">
							<select class="form-control" name="tid">
							<?php foreach($teachers as $v): ?>
							  <option value="<?php echo $v['id']; ?>" <?php echo $v['id'] == my_echo($tactics_info['tid']) ? 'selected' : ''; ?>><?php echo $v['name']; ?></option>
							  <?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="title" class="col-sm-1 control-label">战法标题</label>
						<div class="col-sm-10">
							<input class="form-control" name="title" id="title" value="<?php echo my_echo($tactics_info['title']) ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="intro" class="col-sm-1 control-label">战法简介</label>
						<div class="col-sm-10">
							<input class="form-control" name="intro" id="intro" value="<?php echo my_echo($tactics_info['intro']) ?>">
						</div>
					</div>
					<!--<div class="form-group">
						<label for="container" class="col-sm-1 control-label">文章正文</label>
						<div class="col-sm-10">
							<script id="container" name="content" type="text/plain">
								<?php echo my_echo($tactics_info['content']); ?>
							</script>
						</div>
					</div>-->
					<div class="form-group">
                        <label for="fname" class="col-sm-1 control-label">上传战法</label>
                        <div class="col-sm-5">
							<input type="hidden" name="fname" id="fname" value="<?php echo my_echo($tactics_info['fname']); ?>">
                            <!--用来存放文件信息-->
							<div id="thelist" class="uploader-list"></div>
							<div class="btns">
								<div id="picker">选择pdf文件</div>
								<button type="button" id="ctlBtn" class="btn btn-default">开始上传</button>
							</div>
                        </div>
                        <?php
                        $fname = my_echo($tactics_info['fname']);
                        $fname_path = '../upload/zhanfa/'.$fname.'.pdf';
                        if($fname != '' AND file_exists($fname_path)):
                            ?>
                            <div class="col-sm-5">
                                已上传文件: <a href="javascript:;" target="_blank"><?php echo $fname.'.pdf'; ?></a>
                            </div>
                        <?php endif ?>
                    </div>
					<div class="form-group">
						<div class="col-sm-offset-1 col-sm-10">
							<input type="hidden" name="id" value="<?php echo my_echo($tactics_info['id'], 0); ?>">
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
<!-- ueditor配置文件 
<script src="<?php echo $site_url; ?>plugins/ueditor/ueditor.config.js"></script>-->
<!-- 编辑器源码文件 
<script src="<?php echo $site_url; ?>plugins/ueditor/ueditor.all.js"></script>-->
<!-- 语言包文件(建议手动加载语言包，避免在ie下，因为加载语言失败导致编辑器加载失败) 
<script src="<?php echo $site_url; ?>plugins/ueditor/lang/zh-cn/zh-cn.js"></script>-->
<!-- webuploader -->
<link rel="stylesheet" type="text/css" href="plugins/webuploader/webuploader.css">
<script src="plugins/webuploader/webuploader.min.js"></script>
<script src="plugins/webuploader/webuploader.pdf.js"></script>

<script>
	//var editor = UE.getEditor('container', {'initialFrameHeight' : 600});
function save_article(){
	$.post(admin.url+'tactics/tactics_update',
	$('form').serialize(),
	function (d){
		d = $.parseJSON(d);
		if(d.status){
			alert('文章保存成功');
			location.href = 'tactics';
		}else{
			alert(d.msg);
		}
		
	})
}
</script>