<?php $socket_conf = $this->config->item('socket'); ?>
<style>
    .chat-panel .panel-title{padding-bottom: 10px;}
    .chat-panel .panel-title span{color: #66afe9;}
    .chat-content div{width: 700px;height:auto;word-wrap: break-word;word-break:break-all;padding-bottom: 30px;font-size: 14px;border-bottom: 1px solid #c4e3f3;}
    .chat-all{height: 800px;overflow-y: scroll;}
</style>
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><!--<?php echo $var['name']; ?>-->文字直播</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default col-lg-6">
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form class="form-horizontal col-lg-12" role="form">
                    <!--<div class="form-group">
                        <label for="tid" class="col-sm-2 control-label">选择老师</label>
                        <div class="col-sm-5">
                            <select name="tid" id="tid" class="form-control input w200">
                                <?php foreach($teacher as $v): ?>
                                    <option value="<?php echo $v['id']; ?>"><?php echo $v['nick']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>-->
                    <div class="form-group">
                        <label for="content" class="col-sm-2 control-label">直播内容</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" name="content" id="content" placeholder="输入聊天内容！" rows="8"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="button" class="btn btn-primary" onclick="send_msg()">发送</button>
                            <button type="reset" class="btn btn-danger">清空</button>
                            <?php if(in_array('teacher', $var['permission'])): ?>是否喊单<input type="checkbox" name="handan" value="1"><?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.panel-body -->
        </div>
        <div class="panel panel-default col-lg-6">
            <!-- /.panel-heading -->
            <div class="panel-body chat-all">
                <?php foreach($tchat as $v): ?>
                <div class="panel-body chat-panel list_<?php echo $v['id']; ?>">
                        <div class="panel-title">
                            <span><?php echo $v['tname']; ?><?php if($v['handan']): ?><button type="button" class="btn btn-danger">喊单</button><?php endif; ?></span>
                            <span><?php echo $v['ftime']; ?></span>
                            <button type="button" class="btn btn-danger" onclick="del(<?php echo $v['id']; ?>)">删除</button>
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
<script src="js/socket.io-1.4.5.js"></script>
<script>
    function send_msg(){
        $.post(admin.url+'tchat/send_msg/',
            $('form').serialize(),
            function (result){
                result = $.parseJSON(result);
                if(result.status){
                    $('textarea').val('');
                    //add_html(result.data);
                }else{
                    alert(result.msg);
                }
            })

    }
    function del(id){
        $.post(admin.url+'tchat/del',
            {id:id},
            function (){
                $('.list_'+id).remove();
            })
    }
    $(document).ready(function () {
        var uid = 'admin_<?php echo $this->session->userdata['id']; ?>';
        // 连接服务端
        var socket = io('http://<?php echo $socket_conf["url"]; ?>:<?php echo $socket_conf["receive_port"]; ?>');
        // 连接后登录
        socket.on('connect', function(){
            socket.emit('login', {'uid' : uid});
        });
        // 后端推送来消息时
        socket.on('<?php echo $var['sendtype']; ?>', function(msg){
            deal_msg(msg);
        });
        // 后端推送来在线数据时
        /*socket.on('update_online_count', function(online_stat){
         $('#online_box').html(online_stat);
         });*/
    });
    function deal_msg(data){
        data = $.parseJSON(data);
		if(data.tid == '<?php echo $this->session->userdata['id']; ?>'){
			var handan = '';
			if(data.handan){handan='<button type="button" class="btn btn-danger">喊单</button>';}
			var html='<div class="panel-body chat-panel list_'+data.id+'"> <div class="panel-title"> <span>'+data.tname+handan+'</span> <span>'+data.ftime+'</span><button type="button" class="btn btn-danger" onclick="del('+data.id+')">删除</button> </div> <div class="chat-content"> <div>'+data.content+'</div> </div> </div>';
			$('.chat-all').prepend(html);
		}
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