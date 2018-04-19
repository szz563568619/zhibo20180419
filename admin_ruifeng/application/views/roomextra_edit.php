<style>.examine, .customer{display: none;}</style>
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">房间信息优化管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <a href="roomextra/">房间信息优化列表</a> >> <?php echo !my_echo($roomextra['id']) ? '添加房间信息优化' : '编辑房间信息优化'; ?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form class="form-horizontal col-lg-8" role="form">
					<div class="form-group">
                        <label for="domain" class="col-sm-2 control-label">房间域名</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="domain" id="domain" rows="3" placeholder="填写如：www.example.com"><?php echo my_echo($roomextra['domain']); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="info" class="col-sm-2 control-label">房间底部信息</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="info" id="info" rows="6"><?php echo my_echo($roomextra['info']); ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label">标题</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="title" id="title" value="<?php echo my_echo($roomextra['title']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="keywords" class="col-sm-2 control-label">关键词</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="keywords" id="keywords" value="<?php echo my_echo($roomextra['keywords']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-sm-2 control-label">描述</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="description" id="description" rows="5"><?php echo my_echo($roomextra['description']) ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="hidden" name="id" value="<?php echo $roomextra['id']; ?>">
                            <button type="button" class="btn btn-primary" onclick="save_roomextra()">保存</button>
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
    function save_roomextra(){
        $.post(admin.url+'roomextra/save_roomextra',
            $('form').serialize(),
            function (result){
                result = $.parseJSON(result);
                if(result.status){
                    alert('保存成功');
                    location.href = admin.url+'roomextra';
                }else{
                    alert(result.msg);
                }
            })

    }
</script>