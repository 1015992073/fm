
<!--通用更新反馈-->
<div class="ui-content container">
	<?php
	//var_dump($data);
	if (isset($data)) {
		if ($data["status"] == 'sucess') {
	?>
			<div class="alert alert-success" role="alert"><?= $data["message"] ?></div>
			<a class="btn btn-primary btn-sm" href="<?= get_site_info("admin_url") . $controller ?>/edit/<?= $data["data"] ?>" role="button"><?= lang("edit") ?></a>
		<?php
		} else if ($data["status"] == 'fail') {
		?>
			<div class="alert alert-danger" role="alert"><?= $data["message"] ?></div>
	<?php
		} else {
			//未知错误
			?>
			<div class="alert alert-danger" role="alert"><?= lang("unknown_error") ?></div>
	<?php
		}
	} else {
		//系统错误
		?>
			<div class="alert alert-danger" role="alert"><?= lang("system_error") ?></div>
	<?php
	}
	?>

<a class="btn btn-secondary btn-sm" href="<?= get_site_info("admin_url") . $controller ?>/" role="button"><?= lang("goback").lang("list")?></a>


	

</div>
