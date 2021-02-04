
<?php
$admin_home_url=get_site_info("admin_url");//后台首页
$the_controller_url=$admin_home_url . $controller//当前控制器链接
?>
<!--通用列表-->
<div class="ui-content container">

	<h4><?= isset($title) ? $title : ""  ?></h4>
	<div class="row">
		<a class="btn btn-primary" href="<?= get_site_info("admin_url") . $controller ?>/add" role="button"><?= lang("add_butt") ?></a>
	</div>
	<?php
	//var_dump($data);
	//var_dump($fields);
	?>
	<?php

	if (isset($data) && is_array($data)) {
		if (count($data) > 0) {
			if (isset($fields) && is_array($fields)) {
	?>
				<table class="table">
					<thead>
						<tr>
							<?php foreach ($fields as $key=> $field ) : ?>
								<?php if (isset($field["listshow"]) && $field["listshow"]) : ?>
									<th scope="col"><?= lang($key) ?></th>
								<?php endif ?>
							<?php endforeach ?>
							<th scope="col"><?= lang("action") ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data as $item) : ?>
							<tr>
								<?php foreach ($fields as $key=>  $field) : ?>
									<?php 
									
									if($field["multiple"]){
										//多语言		
										$langValue=	langval($item[$key]);
									}else{
										$langValue=$item[$key];
									}
									?>

									<?php if (isset($field["listshow"]) && $field["listshow"]) : ?>
										<td scope="col"><?= $langValue ?></td>
									<?php endif ?>
								<?php endforeach ?>
								<td><a class="" href="<?= $the_controller_url ?>/edit/<?= $item[$primary_key] ?>" role="button"><?= lang("edit") ?></a><a class="" href="<?= $the_controller_url ?>/del/<?= $item[$primary_key] ?>" role="button"><?= lang("del") ?></a></td>
							</tr>
						<?php endforeach ?>


					</tbody>
				</table>

			<?php
			} //end if $fields
			echo get_page_nav($the_controller_url.'/list/',$query_tol,$page,$pagesize);
		} else {
			?>
			<div class="alert alert-warning" role="alert">
				<?= lang("empty_data_info") ?>
			</div>

		<?php
		}
	} else {
		?>
		<div class="alert alert-danger" role="alert">
			<?= lang("data_get_err") ?>
		</div>
	<?php
	}
	?>





</div>
