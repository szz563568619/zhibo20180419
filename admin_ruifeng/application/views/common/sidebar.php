<?php
//$permission = $this->session->userdata('permission');
$permission = $permission['permission'];
$permission = explode(',', $permission);
?>
<nav class="navbar-default navbar-static-side" role="navigation">
	<div class="sidebar-collapse">
		<ul class="nav" id="side-menu">
			<?php
			if(in_array('examine', $permission)):
			?>
			<li>
				<a href="examine"><i class="fa fa-fw fa-eye"></i>留言审核</a>
			</li>
			<?php endif; ?>
			<?php
			if(in_array('speaker', $permission)):
			?>
			<li>
				<a href="speaker"><i class="fa fa-fw fa-eye"></i>留言查看</a>
			</li>
			<?php endif; ?>
			<?php
			if(in_array('admin', $permission)):
			?>
			<li>
				<a href="roomextra"><i class="fa fa-fw fa-home"></i>房间信息优化管理</a>
			</li>
			<?php endif; ?>
			<?php
			if(in_array('base', $permission) ):
			?>
			<li>
				<a href="room"><i class="fa fa-fw fa-home"></i>房间管理</a>
			</li>
			<li class="<?php if($current_function == 'course'): ?>active<?php endif; ?>">
				<a href="#"><i class="fa fa-fw fa-calendar"></i>课程管理<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li><a href="course/curriculum"><i class="fa fa-fw fa-calendar"></i>课程表</a></li>
					<li><a href="course/teacher"><i class="fa fa-fw fa-users"></i>老师管理</a></li>
					<li><a href="course/data_list"><i class="fa fa-fw fa-file"></i>本周重点数据</a></li>
				</ul>
			</li>
			<li class="<?php if($current_function == 'specialist'): ?>active<?php endif; ?>">
				<a href="#"><i class="fa fa-fw fa-graduation-cap"></i>专家管理<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li><a href="specialist/specialist_edit"><i class="fa fa-fw fa-graduation-cap"></i>添加专家</a></li>
					<li><a href="specialist/specialist_list"><i class="fa fa-fw fa-list"></i>专家列表</a></li>
				</ul>
			</li>
			<li>
				<a href="upgrade/"><i class="fa fa-fw fa-level-up"></i>升级通告</a>
			</li>
			<!--<li>
				<a href="visitoripon/"><i class="fa fa-fw fa-list"></i>游客ip在线时间列表</a>
			</li>-->
			<?php endif; ?>
			<?php
			if(in_array('base', $permission) OR in_array('customer', $permission) ):
			?>
			<li>
				<a href="ip/ip_list"><i class="fa fa-fw fa-ban"></i>IP限制</a>
			</li>
			<li>
				<a href="nospeaking/nospeaking_list"><i class="fa fa-fw fa-ban"></i>用户禁言</a>
			</li>
			<?php endif; ?>
			<?php
			if(in_array('admin', $permission) ):
			?>
			<!--<li>
				<a href="obsvideo"><i class="fa fa-fw fa-video-camera"></i>视频直播切换</a>
			</li>-->
			<li>
				<a href="admin"><i class="fa fa-fw fa-user"></i>管理员</a>
			</li>
			<li>
				<a href="online_peo"><i class="fa fa-fw fa-list"></i>在线用户列表</a>
			</li>
			<?php endif; ?>
			<?php
			if(in_array('member', $permission) ):
			?>
			<li class="<?php if($current_function == 'member'): ?>active<?php endif; ?>">
				<a href="#"><i class="fa fa-fw fa-users"></i>会员管理<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<li><a href="member/member_edit"><i class="fa fa-fw fa-user-plus"></i>添加会员</a></li>
					<li><a href="member/member_list"><i class="fa fa-fw fa-list"></i>会员列表</a></li>
					<li><a href="member/group"><i class="fa fa-fw fa-group"></i>会员组</a></li>
				</ul>
			</li>
			<li>
				<a href="chat"><i class="fa fa-fw fa-list"></i>聊天记录</a>
			</li>
			<?php endif; ?>
			<?php
			if(in_array('seo', $permission)):
			?>

			<li>
				<a href="source"><i class="fa fa-fw fa-share-alt"></i>来源管理</a>
			</li>
			<li>
				<a href="visitor"><i class="fa fa-fw fa-user"></i>游客管理</a>
			</li>
			<?php endif; ?>
			<?php
			if(in_array('base', $permission) ):
			?>
			<!-- <li>
				<a href="desktop"><i class="fa fa-fw fa-user"></i>保存桌面统计管理</a>
			</li> -->
			<?php endif; ?>
			<?php
			if(in_array('customer', $permission) ):
			?>
			<li>
				<a href="visitor/im"><i class="fa fa-fw fa-wechat"></i>游客会话</a>
			</li>
			<?php endif; ?>
			<?php
			if(in_array('customer', $permission) OR in_array('admin', $permission) ):
			?>
			<li>
				<a href="zaobao"><i class="fa fa-fw fa-link"></i>早报管理</a>
			</li>
			<?php endif; ?>
			<?php
			if(in_array('teacher', $permission) ):
			?>
			<li class="<?php if($current_function == 'laoshi'): ?>active<?php endif; ?>">
				<a href="#"><i class="fa fa-fw fa-users"></i>老师直播管理<span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<!-- <li><a href="tchat"><i class="fa fa-fw fa-video-camera"></i>老师直播</a></li> -->
					<li><a href="wenda"><i class="fa fa-fw fa-question-circle"></i>问答管理</a></li>
				</ul>
			</li>
			<?php endif; ?>
			<?php
			if(in_array('customer', $permission) OR in_array('teacher', $permission)):
			?>
			<li>
				<a href="groupchat/im"><i class="fa fa-fw fa-wechat"></i>APP群聊</a>
			</li>
			<?php endif; ?>
			<?php
			if(in_array('dazi', $permission)):
			?>
			<li>
				<a href="tchat"><i class="fa fa-fw fa-video-camera"></i>文字直播管理</a>
			</li>
			<?php endif; ?>
		</ul>
	<!-- /#side-menu -->
	</div>
	<!-- /.sidebar-collapse -->
</nav>
<!-- /.navbar-static-side -->
