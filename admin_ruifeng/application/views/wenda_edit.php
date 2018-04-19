<style>
    .chat-panel .panel-title{padding-bottom: 10px;}
    .chat-panel .panel-title span{color: #66afe9;}
    .chat-content div{width: 700px;height:auto;word-wrap: break-word;word-break:break-all;padding-bottom: 30px;font-size: 14px;border-bottom: 1px solid #c4e3f3;}
    .chat-all{height: 800px;overflow-y: scroll;}
</style>
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><a href="wenda">返回问题列表</a></h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default col-lg-6">
			<div class="panel-heading">
				'<?php echo $wenti['uname']; ?>'问：<?php echo $wenti['content']; ?>
			</div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form class="form-horizontal col-lg-12" role="form">
                    <div class="form-group">
                        <div class="col-sm-8">
                            <textarea class="form-control" name="content" id="content" placeholder="输入回答内容！" rows="8"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
							<input  type="hidden" name="did" value="<?php echo $wenti['id']; ?>"/>
							<input  type="hidden" name="uid" value="<?php echo $wenti['uid']; ?>"/>
							<input  type="hidden" name="uname" value="<?php echo $wenti['uname']; ?>"/>
							<input  type="hidden" name="gid" value="<?php echo $wenti['gid']; ?>"/>
                            <button type="button" class="btn btn-primary" onclick="send_msg()">回答问题</button>
                            <button type="reset" class="btn btn-danger">清空</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.panel-body -->
        </div>
        <div class="panel panel-default col-lg-6">
			<div class="panel-heading">
				回答列表
			</div>
            <!-- /.panel-heading -->
            <div class="panel-body chat-all">
                <?php foreach($daan as $v): ?>
                <div class="panel-body chat-panel list_<?php echo $v['id']; ?>">
                        <div class="panel-title">
                            <span><?php echo $v['tname']; ?></span>
                            <span><?php echo $v['time']; ?></span>
                            <button type="button" class="btn btn-danger" onclick="del(<?php echo $v['id']; ?>,<?php echo $wenti['id']; ?>)">删除回答</button>
                        </div>
                    <div class="chat-content">
                            <div><?php echo $v['content']; ?></div>
                        </div>
                </div>
                <?php endforeach; ?>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6 -->
</div>
<!-- /.row -->
<script>
    function send_msg(){
        $.post(admin.url+'wenda/save_daan',
            $('form').serialize(),
            function (result){
                result = $.parseJSON(result);
                if(result.status){
                    $('textarea').val('');
                    deal_msg(result.data);
                }else{
                    alert(result.msg);
                }
            })

    }
    function del(id,did){
        $.post(admin.url+'wenda/del_da',
            {id:id,did:did},
            function (){
                $('.list_'+id).remove();
            })
    }
	
    function deal_msg(data){
        var html='<div class="panel-body chat-panel list_'+data.id+'"> <div class="panel-title"> <span>'+data.tname+'</span> <span>'+data.time+'</span><button type="button" class="btn btn-danger" onclick="del('+data.id+','+data.did+')">删除回答</button> </div> <div class="chat-content"> <div>'+data.content+'</div> </div> </div>';
        $('.chat-all').prepend(html);
    }

    /*绑定发送消息的快捷键，Enter或者Ctrl+Enter*/
    document.onkeydown = function (){
        var e = e || window.event;
        var keyCode = e.keyCode || e.which || e.charCode;
        if( (e.ctrlKey && (e.keyCode == 13)) ||  e.keyCode == 13 ){
            var element = e.srcElement||e.target;
            if( $(element).attr('id') == 'content'){
                send_msg();
            }
        }
    }
</script>