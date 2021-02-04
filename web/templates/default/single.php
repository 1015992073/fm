<?php
if (is_array($rootCategory) && count($rootCategory) > 0) {
  
    echo '<a href="' . get_site_info("homeurl") . '">'.lang("home").'</a> / ';
    foreach ($rootCategory as $cat) {

        echo '<a href="' . get_site_info("homeurl") . $cat["term_url"] . '">' . langval($cat["terms_name"]) . '</a> / ';
    }
}
?>
<?php

if (isset($post)) {
    // var_dump($post);
?>
    <h1>标题: <?php echo langval($post["post_title"]); ?></h1>


    <p>链接:<?php echo langval($post["post_url"]); ?></p>
    <p>简介:<?php echo langval($post["post_excerpt"]); ?></p>
    <p>内容:<?php echo langval($post["post_content"]); ?></p>
<?php
} else {
?>

    <p>该分类还未添加文章</p>
<?php
}
?>