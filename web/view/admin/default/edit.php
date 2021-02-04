<?php
$admin_home_url = get_site_info("admin_url"); //后台首页
$the_controller_url = $admin_home_url . $controller; //当前控制器链接
$langs = get_config("supportLanguage"); //系统支持的语言
$langlabelhtml = ''; //多语言切换

?>
<!--通用编辑-->

<div class="ui-content container">
	<h4><?= isset($title) ? $title : "" ?></h4>

	<?php
	//var_dump($data);
	if (isset($data) && is_array($data)) {
		if (count($data) > 0) {
	?>

			<form action="<?= $the_controller_url ?>/update" method="POST">
				<?php echo '<input type="hidden" value="' . $data[0][$primary_key] . '" name="data[' . $primary_key . ']">'; ?>


				<?php
				foreach ($fields as $key => $field) {
					if ($key !== $primary_key) {
						$field["primary_key"] = $key;
						$field["value"] = $data[0][$key]; //当前值，可能是多语言
						if ($field["multiple"] && count($langs) > 1) {
							//多语言
							echo '<div class="ui-tab-box ui-admin-edit-lang-box">';
							echo '<div class="ui-tab-content">';
							$langlabelhtml = "";
							foreach ($langs as $lang) {

								$active = '';
								if ($langlabelhtml == "") {
									$active = 'active';
									$langlabelhtml .= '<span class="ui-tab-label-item ' . $active . ' ">' . lang($lang) . '</span>';
								} else {
									$active = '';
									$langlabelhtml .= '<span class="ui-tab-label-item">' . lang($lang) . '</span>';
								}

								$field["field_name"] = "[" . $key . "]" . "[" . $lang . "]";
								$popinfo = "(" . lang("lang_input_info") . lang($lang) . ")";
								$field["lang"] = $lang; //当前语言 ，对应语言显示对应值

								$field_html =	createFieldHtml($field); //创建表单元素html
								if ($field["type"] != "hidden") {


				?>
									<div class="ui-tab-content-item <?= $active ?>">
										<div class="form-group">
											<label for="exampleFormControlInput1"><?= lang($key) . $popinfo ?></label>
											<div class="">
												<?= $field_html; ?>
											</div>

											<small class="form-text text-muted"><?= lang($key . "_description") ?></small>
										</div>
									</div>
							<?php
								} else {
									echo '<div class="">'. $field_html.'</div>';//隐藏选项，直接输入
								}
							} //end  foreach  $langs 

							echo '</div>';
							echo '<div class="ui-tab-label-box"><div class="ui-tab-label">' . $langlabelhtml . '</div></div>';
							echo '</div>';
						} else {
							//单语言
							$field["field_name"] = "[" . $key . "]";
							$field_html =	createFieldHtml($field); //创建表单元素html
							if ($field["type"] != "hidden") {
							?>
							<div class="form-group">
								<label for="exampleFormControlInput1"><?= lang($key) ?></label>
								<div class="">
									<?= $field_html; ?>
								</div>
								<small class="form-text text-muted"><?= lang($key . "_description") ?></small>
							</div>
						<?php
						} else {
							echo '<div class="">'. $field_html.'</div>';//隐藏选项，直接输入
						}
						}
						?>



				<?php
					} //end if  $key !== $primary_key
				} //end  forech  $fields
				?>





				<button type="submit" class="btn btn-primary"><?= lang('submit_butt')  ?></button>
			</form>
		<?php

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