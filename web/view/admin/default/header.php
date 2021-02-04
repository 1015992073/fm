<!DOCTYPE html>
<?php
	$home_url=get_site_info("homeurl");
	$admin_template_url=get_site_info("admin_template_url");
	$admin_url=get_site_info("admin_url");
	?>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title><?php echo (isset($title) ? $title . "-" : "") . get_site_info("name"); ?></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/png" href="/favicon.ico" />
	<link rel="stylesheet" href="<?php echo $admin_template_url ?>static/js/bootstrap-4.5.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo $admin_template_url ?>static/css/style.css">
	<script src="<?php echo$admin_template_url ?>static/js/jquery-2.1.1.min.js"></script>
	<script src="<?php echo $admin_template_url ?>static/js/bootstrap-4.5.0/js/bootstrap.min.js"></script>
	<script src="<?php echo $admin_template_url ?>static/js/jquery.GetDataByAjax.js"></script>
	<script src="<?php echo $admin_template_url ?>static/js/main.js"></script>

	<!-- STYLES -->
</head>

<body>
	<div class=" container  ui-header-top">
		<div class="row">
			<div class="col-2">欢迎 ！ admin</div>
			<div class="col-2"></div>
		</div>
	</div>
	<div class="ui-page container">
		<div class="row">
			<div class="ui-admin-nav col-2">
				<div class="ui-nav-item"><a href="<?php echo $admin_url ?>">首页</a></div>
				<div class="ui-nav-item"><a href="">内容</a>
					<div class="ui-chlid-list">
						<div class="ui-nav-item"><a href="<?php echo $admin_url; ?>type">类型</a></div>
						<div class="ui-nav-item"><a href="<?php echo $admin_url; ?>category">分类</a></div>
						<div class="ui-nav-item"><a href="<?php echo $admin_url; ?>posts">文章</a></div>
						
					</div>

				</div>
				<div class="ui-nav-item"><a href="">文件</a>
					<div class="ui-chlid-list">
						<div class="ui-nav-item"><a href="">图片</a></div>
						<div class="ui-nav-item"><a href="">视频</a></div>
						<div class="ui-nav-item"><a href="">代码</a></div>
					</div>

				</div>
				<div class="ui-nav-item"><a href="">权限</a>
					<div class="ui-chlid-list">
						<div class="ui-nav-item"><a href="">角色</a></div>
						<div class="ui-nav-item"><a href="">用户</a></div>
						
					</div>

				</div>

				<div class="ui-nav-item"><a href="">设置</a>
				<div class="ui-chlid-list">
						<div class="ui-nav-item"><a href="">系统设置</a>
						<div class="ui-chlid-list">
						<div class="ui-nav-item"><a href="">系统备份</a></div>
					
					</div>
						</div>
						<div class="ui-nav-item"><a href="">模板设置</a></div>
						
					</div>
			</div>
				<div class="ui-nav-item"><a href="">系统</a>
				<div class="ui-chlid-list">
						<div class="ui-nav-item"><a href="">菜单</a></div>
						<div class="ui-nav-item"><a href="">插件</a></div>
						<div class="ui-nav-item"><a href="">开发</a></div>
						<div class="ui-nav-item"><a href="">帮助</a></div>
						<div class="ui-nav-item"><a href="">关于系统</a></div>
					</div>
			</div>
			
			</div>
			<div class="ui-content col-10">