<style>
.ui-mian{ width: 700px; margin: 0 auto;}
.ui-list{ margin-top: 15px;}
.ui-list .ui-item{ border:1px solid #ccc; margin-bottom: 10px; padding:15px;border-radius: 10px;} 
.ui-brea-nav{ border:1px solid #ccc; padding: 10px;}
.ui-brea-nav a{ display: inline-block; margin-right: 10px; color: #000;}
.ui-pagenav {text-align:center;}
.ui-pagenav a {display: inline-block; padding: 5px 10px; border:1px solid #ccc; color: #000; margin-right: 5px;border-radius: 5px; }
.ui-pagenav a.current{background-color: #ccc;}

.ui-child-list{ border:1px solid #ccc; padding: 10px; margin-top: 15px;}
.ui-child-list a{ display: inline-block; margin-right: 10px; color: #000;}
</style>
<?php
$homeurl = get_site_info("homeurl"); //不在循环里面使用可以快30ms

$pagesize=20;
$get_postss = microtime(true);
$postsquery = getPosts(["page" => $page, "pagesize" => $pagesize,  "where" => array('term_id' => $term_id)]); //获取文章列表
$get_postse = microtime(true);

$posts = $postsquery["list"]; //获取文章列表
$postscount = $postsquery["total"]; //文章数量


$chiledCategory = getTheChildCategoryToList($term_id); //该分类子类



?>
<div  class="ui-mian">
<h1>前台通用分类 <?php echo (isset($rootCategory) && is_array($rootCategory) && count($rootCategory) > 0)?" : ".$rootCategory[$term_id]["terms_name"]:""; ?></h1>
<div  class="ui-title">共找到<?php echo $postscount;?>篇文章，显示:<?php echo $pagesize;?>篇,用时<?php echo  (($get_postse -$get_postss) )?>秒</div>
<p>当前分类id:<?PHP echo $term_id . "该分类文章总数:" . $postscount; ?></p>
<div class="ui-brea-nav">
    <?php
    if (isset($rootCategory) && is_array($rootCategory) && count($rootCategory) > 0) {

        $nav = '<a href="' . $homeurl  . '">' . lang("home") . '</a> / ';
        foreach ($rootCategory as $cat) {
            $nav .= '<a href="' . $homeurl  . $cat["term_url"] . '">' . langval($cat["terms_name"]) . '</a> / ';
        }
        echo  $nav;
    }
    ?>
</div>
<div  class="ui-child-list">
<?php
    if (isset($chiledCategory) && is_array($chiledCategory) && count($chiledCategory) > 0) {

        $nav = '<a href="#">当前分类下属子类:</a> / ';
        foreach ($chiledCategory as $cat) {
            $nav .= '<a href="' . $homeurl  . $cat["term_url"] . '">' . langval($cat["terms_name"]) . '</a> / ';
        }
        echo  $nav;
    }else{
        echo  '<a href="#">当前分类无子类:</a>';
    }
    ?>
</div>
<div class="ui-list">
<?php
 
if (isset($posts) && is_array($posts) && count($posts) > 0) {

foreach ($posts as $post) {

?>
        <div class="ui-item">

            <h3>标题:<a href="<?php echo $homeurl . $post["post_url"]; ?>"><?php echo langval($post["post_title"]); ?></a></h3>
            <p>文章id:<?php echo $post["post_id"]; ?></p>
            <p>分类ID:<?php echo $post["post_parent"]; ?></p>
            <p>分类名:<a href="<?php echo $homeurl . $post["term_url"]; ?>"><?php echo langval($post["terms_name"]); ?></a></p>
            <p>分类链接:<?php echo $homeurl . $post["term_url"]; ?></p>
            <p>链接:<?php echo $homeurl . $post["post_url"]; ?></p>
            <p>简介:<?php echo langval($post["post_excerpt"]); ?></p>
        </div>
    <?php

    }
    echo '<div class="ui-pagenav">'.get_page_nav($homeurl.$url,$postscount,$page,$pagesize).'</div>';
} else {
    ?>
    <p>该分类还未添加文章</p>
<?php
}
?>
</div>
</div>