<?php
$admin_home_url = get_site_info("admin_url"); //后台首页
$the_controller_url = $admin_home_url . $controller; //当前控制器链接
$home_url = get_site_info("homeurl"); //首页
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

				<div class="ui-list-tree ui-cat-list-tree">
					<div class="table">
						<div class="thead">
							<div class="tr">
								<div class="click-more ui-col" scope="col"></div>
								<?php foreach ($fields as $key => $field) : ?>
									<?php if (isset($field["listshow"]) && $field["listshow"]) : ?>

										<div class=" ui-col <?= $key ?> " scope="col"><?= lang($key) ?></div>
									<?php endif ?>
								<?php endforeach ?>

								<div class="action ui-col" scope="col">操作</div>
							</div>
						</div>
						<div class="tbody">

							<?php
							$g_data = ["the_controller_url" => $the_controller_url, "home_url" => $home_url, "admin_home_url" => $admin_home_url, "primary_key" => $primary_key, "fields" => $fields]; //变量 ，传递用
							$htmls = microtime(true);
							show_cat_by_cat_tree($data, "", $g_data);
							$htmle = microtime(true);
							echo "显示列表用时:" . (($htmle - $htmls) * 1000) . 'ms';
							?>

						</div>
					</div>
				</div>


			<?php
			} //end if $fields
			//echo get_page_nav($the_controller_url.'/list/',$query_tol,$page,$pagesize);
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

<?php

function show_cat_by_cat_tree($list = [], $flg = "", $g_data = [])
{
	$the_controller_url = $g_data["the_controller_url"];
	$primary_key = $g_data["primary_key"];
	$admin_home_url = $g_data["admin_home_url"];
	$home_url = $g_data["home_url"];
	$fields = $g_data["fields"];
	if (is_array($list) && count($list) > 0) {
		foreach ($list as $item) {
			$f_flg = "┣" . $flg;
			//$flg = $flg . "━";
			$ismore = ''; //有子类显示+号

			if (isset($item["child"]) && is_array($item["child"]) && count($item["child"]) > 0) {
				$ismore = "+";
			}

?>
			<div class="ui-item">
				<div class="tr">
					<div class="click-more ui-col" scope="col"><?= $ismore; ?></div>
					<?php foreach ($fields as $key => $field) : ?>
						<?php if (isset($field["listshow"]) && $field["listshow"]) : ?>
							<?php
							if ($field["multiple"]) {
								//多语言		
								$langValue = langval($item[$key]);
							} else {
								$langValue = $item[$key];
							}
							if ($key == "terms_name") {
								$langValue = $f_flg . $langValue;//分类名添加层级关系
							}
							?>
							<div class="ui-col <?= $key ?> "><?= $langValue ?></div>
						<?php endif ?>
					<?php endforeach ?>
					<div class="action ui-col">
						<a class="action-butt action-butt-edit" href="<?= $the_controller_url ?>/edit/<?= $item[$primary_key] ?>"><?= lang("edit") ?></a>
						<a class="action-butt action-butt-del" target="_blank" href="<?= $home_url . $item["term_url"] ?>"><?= lang("preview") ?></a>
						<a class="action-butt action-butt-del" href="<?= $admin_home_url ?>category/add/<?= $item[$primary_key] ?>"><?= lang("add").lang("child_term")  ?></a>
						<a class="action-butt action-butt-del" href="<?= $admin_home_url ?>posts/add/<?= $item[$primary_key] ?>"><?= lang("add") . lang("posts") ?></a>
						<a class="action-butt action-butt-del" href="<?= $the_controller_url ?>/del/<?= $item[$primary_key] ?>"><?= lang("del") ?></a>
					</div>
				</div>
				<?php
				if (isset($item["child"]) && is_array($item["child"]) && count($item["child"]) > 0) {
					echo  '<div class="ui-child">';
					show_cat_by_cat_tree($item["child"], $flg . "━", $g_data);
					echo  '</div>';
				}
				?>

			</div>

<?php
		} //end  foreach
	} //end if
} //end funcion
?>
<script>
	$(document).ready(function() {
		$(".click-more").click(function() {
			let p_tr = $(this).parent(".tr");
			if ($(this).html() == "+") {
				$(this).html("-")

				p_tr.next(".ui-child").show();

			} else if ($(this).html() == "-") {
				$(this).html("+");
				p_tr.next(".ui-child").hide();
			}


		})
	})
</script>