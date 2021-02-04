<?php
$admin_home_url = get_site_info("admin_url"); //后台首页
$the_controller_url = $admin_home_url . $controller; //当前控制器链接
$langs = get_config("supportLanguage"); //系统支持的语言
$langlabelhtml='';//多语言切换

?>
<!--通用add-->
<div class="ui-content container">

	<h4><?= isset($title) ? $title : "" ?></h4>
	<div class="ui-edit-content ui-tab-box">
	<form action="<?= $the_controller_url ?>/update" method="POST">
		<div class="ui-edit-box-content ">
		
				<?php
				
				foreach ($langs as $lang) {
					$active='';
					if($langlabelhtml==""){
						$active='active';
						$langlabelhtml.='<span class="ui-tab-label-item '.$active.' ">'.lang($lang).'</span>';
					}else{
						$active='';
						$langlabelhtml.='<span class="ui-tab-label-item">'.lang($lang).'</span>';
					}
					
				?>
					<div class="ui-tab-content-item ui-lang-content ui-lang-<?php echo $lang .' ' .$active; ?> ">
						<?php
						foreach ($fields as $key => $field) {
							if ($key !== $primary_key) {
								$field["primary_key"] = $key;
								$popinfo='';//多语言提示
								//如果系统超过2种语言，字段名,因为数据都提交全部是通过$data变量，所有必须加[] ,如：最后提交的数据的是data[id] 和data[id][en]
								if (count($langs) > 1  ) {
									$field["field_name"] ="[". $key . "]". "[" . $lang . "]";
									if($field["multiple"]){
										$popinfo="(".lang("lang_input_info").lang($lang).")";
									}
								} else {
									$field["field_name"] ="[". $key. "]";
								}
								
								
								

								$field_html =	createFieldHtml($field); //创建表单元素html
						?>
								<div class="form-group">
									<label for="exampleFormControlInput1"><?= lang($key).$popinfo ?></label>
									<div class="">
										<?php echo $field_html; ?>
									</div>

									<small class="form-text text-muted"><?= lang($key . "_description") ?></small>
								</div>
						<?php
							}
						} //end  foreach fields
						?>
					</div>
					<!--ui-lang-content -->
				<?php
				} //end  foreach lang
				?>
			
		</div>
		<button type="submit" class="btn btn-primary"><?= lang('submit_butt')  ?></button>
			</form>
		<?php
			if (count($langs) > 1  ) {
				echo '<div class="ui-content-lang-label"><div class="ui-content-lang-label-list ui-tab-label">'.$langlabelhtml.'</div></div>';
			}
		?>
		
	
		
	</div>

</div>