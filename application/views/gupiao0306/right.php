 <div class="right_area">
	<div class="chat_area">
		<div class="head_right_info transparent_bg">
			<ul>
				<li style="float: left;"><a href="desktop" style="background: url(<?php echo $tpl; ?>images/desktop.png) no-repeat center center;width: 103px;"></a></li>
				
				<?php if($this->session->userdata('is_login')): ?>
				<li><a href="user/logout">退出</a></li>
				<?php else: ?>
				<li><a href="javascript:" class="dialog" id="login" data-url="page/login" data-width="420" data-height="369"></a></li>
				<li><a href="javascript:" class="dialog" id="register" data-url="page/register" data-width="420" data-height="500"></a></li>
				<?php endif; ?>
				<li><?php echo $this->session->userdata('name'); ?></li>
				<li style="background: url(<?php echo $tpl; ?>images/level/level<?php echo $this->session->userdata('gid'); ?>.png) no-repeat center center;width: 70px;"></li>
			</ul>
		</div>
	    <!--点击图片显示原图-->
	    <div id="box"><img src="" alt=""/></div>
		<div class="chat transparent_bg">
			<div class="chat_main">
				<ul id="msg_list">
					<?php foreach($chat_list as $v): ?>
					<li class="<?php if($v['gid'] == 0){echo 'xiaomishu';}elseif($v['name'] == $this->session->userdata('name')){echo 'benren';} ?> level_msg_<?php echo $v['gid']; ?>"  id="chat_<?php echo $v['score']; ?>">
						<div class="chat_head">
							<?php if(isset($v['is_mobile']) AND $v['is_mobile']){ ?><em class="mobile-icon"></em><?php } ?>
							<em class="s_time"><?php echo substr($v['time'], 11, 5); ?></em>
							<em><img src="<?php echo $tpl; ?>images/level/level<?php echo $v['gid']; ?>.png"/></em>
							<em class="uname" onclick="call_username(this)"><?php echo $v['name']; ?></em>
							<?php if($this->session->userdata('gid') == 0){?>
								<em class="nei_edit">
									<span class="del_msg" onClick="del_msg(<?php echo $v['score']; ?>,this)">删除</span>
									<span class="ip_ban" onClick="ip_save('<?php echo $v['name']; ?>')">屏蔽</span>
									<span class="ip_ban" onClick="nospeaking_save('<?php echo $v['name']; ?>')">禁言</span>
								</em>
							<?php }?>
						</div>
						<div class="chat_content">
							<div class="say"><?php echo $v['content']; ?></div>
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="chat_bottom transparent_bg">
		<div class="chat_bottom_2">
		    <div class="qq">
		    	<?php for($i = 0; $i < 5; $i++): ?>
	            <a href="javascript:" class="qq_click" data-resource="老师助理"><img src="<?php echo $tpl; ?>images/qq1.png"></a>
	            <a href="javascript:" class="qq_click" data-resource="老师助理"><img src="<?php echo $tpl; ?>images/qq2.png"></a>
	        	<?php endfor; ?>
		    </div>

		</div>
		<div class="emotion">
			<div class="chat_bottom_3">
				<a href="javascript:;" class="face"></a>
				<a href="javascript:;" class="color_bar"></a>
				<?php if($this->session->userdata('gid') != 1){ ?>
				<a href="javascript:;" class="stupian">
				<script src="<?php echo $tpl; ?>js/jquery.form.js"></script>
				<form id="fileupload-form">  <input type="file" name="upload_img" class="upload_img"></form>
				</a>
				<?php } ?>
				<a href="javascript:;" class="qingping" onClick="clear_screen()"></a>
				<!--弹幕-->
				<?php if($this->session->userdata('gid') == 0): ?>
				<!-- <a href="javascript:;" id="danmu"><input type="checkbox" name="is_dan" value="1">弹幕</a> -->
				<?php endif; ?>
				<?php
				if($alias_list AND !empty($alias_list)):
				?>
				<span>小号：</span>
				<select onchange="change_alias(this)">
					<option value="<?php echo $this->session->userdata('name'); ?>" data-gid="<?php echo $this->session->userdata('gid'); ?>">我</option>
					<?php foreach($alias_list as $v): ?>
					<option value="<?php echo $v['name']; ?>" data-gid="<?php echo $v['gid']; ?>"><?php echo $v['name']; ?></option>
					<?php endforeach; ?>
				</select>
				<?php endif; ?>
			</div>

			<table border="0" cellspacing="0" cellpadding="0" class="face01_imgs" id="div3" style="display:none;">
				<tbody>
					<tr>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/1.1.gif" data-num="1.1" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/1.2.gif" data-num="1.2" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/1.3.gif" data-num="1.3" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/1.4.gif" data-num="1.4" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/1.5.gif" data-num="1.5" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/1.6.gif" data-num="1.6" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/1.7.gif" data-num="1.7" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/1.8.gif" data-num="1.8" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/1.9.gif" data-num="1.9" width="28" class="clap"/></td>
					</tr>
					<tr>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/2.1.gif" data-num="2.1" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/2.2.gif" data-num="2.2" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/2.3.gif" data-num="2.3" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/2.4.gif" data-num="2.4" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/2.5.gif" data-num="2.5" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/2.6.gif" data-num="2.6" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/2.7.gif" data-num="2.7" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/2.8.gif" data-num="2.8" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/2.9.gif" data-num="2.9" width="28" class="clap"/></td>
					</tr>
					<tr>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/3.1.gif" data-num="3.1" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/3.2.gif" data-num="3.2" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/3.3.gif" data-num="3.3" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/3.4.gif" data-num="3.4" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/3.5.gif" data-num="3.5" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/3.6.gif" data-num="3.6" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/3.7.gif" data-num="3.7" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/3.8.gif" data-num="3.8" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/3.9.gif" data-num="3.9" width="28" class="clap"/></td>
					</tr>
					<tr>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/4.1.gif" data-num="4.1" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/4.2.gif" data-num="4.2" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/4.3.gif" data-num="4.3" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/4.4.gif" data-num="4.4" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/4.5.gif" data-num="4.5" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/4.6.gif" data-num="4.6" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/4.7.gif" data-num="4.7" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/4.8.gif" data-num="4.8" width="28" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/4.9.gif" data-num="4.9" width="28" class="clap"/></td>
					</tr>
					<tr>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/5.1.gif" data-num="5.1" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/5.2.gif" data-num="5.2" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/5.3.gif" data-num="5.3" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/5.4.gif" data-num="5.4" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/5.5.gif" data-num="5.5" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/5.6.gif" data-num="5.6" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/5.7.gif" data-num="5.7" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/5.8.gif" data-num="5.8" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/5.9.gif" data-num="5.9" width="22" class="clap"/></td>
					</tr>
					<tr>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/6.1.gif" data-num="6.1" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/6.2.gif" data-num="6.2" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/6.3.gif" data-num="6.3" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/6.4.gif" data-num="6.4" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/6.5.gif" data-num="6.5" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/6.6.gif" data-num="6.6" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/6.7.gif" data-num="6.7" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/6.8.gif" data-num="6.8" width="22" class="clap"/></td>
						<td><img src="<?php echo $tpl; ?>images/biaoqing/6.9.gif" data-num="6.9" width="22" class="clap"/></td>
					</tr>
				</tbody>
			</table>
			<table class="face02_imgs" border="0" cellspacing="0" cellpadding="0" style="display:none;">
				<tbody>
					<tr><td><img src="<?php echo $tpl; ?>images/biaoqing/7.1.gif" data-num="7.1" class="clap1" width="180"/></td></tr>
					<tr><td><img src="<?php echo $tpl; ?>images/biaoqing/7.2.gif" data-num="7.2" class="clap1" width="180"/></td></tr>
					<tr><td><img src="<?php echo $tpl; ?>images/biaoqing/7.3.gif" data-num="7.3" class="clap1" width="180"/></td></tr>
					<tr><td><img src="<?php echo $tpl; ?>images/biaoqing/7.4.gif" data-num="7.4" class="clap1" width="180"/></td></tr>
					<tr><td><img src="<?php echo $tpl; ?>images/biaoqing/7.5.gif" data-num="7.5" class="clap1" width="180"/></td></tr>
				</tbody>
			</table>
		</div>
		<div class="chat_bottom_4">
			<textarea class="text" id="msg_content" placeholder="观望一天不如咨询一遍，输入您的问题"></textarea>
			<input type="button" class="button" value="发送" onclick="send_msg()"/>

		</div>
	</div>
</div>
