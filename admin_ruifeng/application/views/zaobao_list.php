<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">早报管理</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                文章列表 <a href="zaobao/edit_zaobao/" class="btn btn-primary">添加文章</a>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>文章标题</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($zaobao_list as $v): ?>
                            <tr>
                                <td><?php echo $v['id']; ?></td>
                                <td><?php echo $v['title']; ?></td>
                                <td><?php echo $v['time']; ?></td>
                                <td>
                                    <a href="zaobao/edit_zaobao/<?php echo $v['id']; ?>" class="btn btn-primary">编辑</a>
                                    <button type="button" class="btn btn-danger" onclick="del_zaobao(<?php echo $v['id']; ?>)">删除</button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
					<?php echo $pagin; ?>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6 -->
</div>
<!-- /.row -->
<script>
    function del_zaobao(id){
        if(confirm('确认删除该文章？该操作不可恢复，请谨慎操作！')){
            $.get(admin.url+'zaobao/zaobao_del/'+id,
                function (){
                    location.reload();
                })
        }
    }
</script>