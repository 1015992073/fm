
<?php
 //$list=get_posts(array("page"=>1,"pagesize"=>40,"where"=>array("post_parent"=>1)));
//var_dump( $list);
//$lastpost=get_table_list(['table' => 'posts',"pagesize"=>20,"orderby"=>'post_id',"return"=>"list"]);
//var_dump( $lastpost);

$get_posts = microtime(true);
$pagesize=20;
//$all=get_posts(["pagesize"=>$pagesize,"orderby"=>'sort DESC',"return"=>"list"]);//不查询总数
//$posts=$all;
$all=get_posts(["pagesize"=>$pagesize,"orderby"=>'sort DESC']);
$posts=$all["list"];
$get_postse = microtime(true);
?>
<style>
.ui-mian{ width: 700px; margin: 0 auto;}
.ui-list .ui-item{ border:1px solid #ccc; margin-bottom: 10px; padding:15px;border-radius: 10px;} 
</style>
<div  class="ui-mian">
<h1>前台首页</h1>
<p>这个地方添加首页内容吧</p>
    <div  class="ui-title">共找到<?php echo isset($all["total"])?$all["total"]:$pagesize;?>篇文章，显示:<?php echo $pagesize;?>篇,用时<?php echo  (($get_postse -$get_posts) )?>秒</div>
    <p>说明:获取产品总数非常耗时间!</p>
<div  class="ui-list">
<?php

if(isset($posts) && is_array($posts)){
$homeurl = get_site_info("homeurl"); //不在循环里面使用可以快30ms
foreach ($posts as $post) {

?>
        <div class="ui-item">

            <h3>标题:<a href="<?php echo $homeurl . $post["post_url"]; ?>"><?php echo langval($post["post_title"]); ?></a></h3>
            <p>创建时间:<?php echo langval($post["create_date"]); ?></p>
            <p>排序:<?php echo langval($post["sort"]); ?></p>
            <p>文章id:<?php echo $post["post_id"]; ?></p>
            <p>分类ID:<?php echo $post["post_parent"]; ?></p>
            <p>分类名:<a href="<?php echo $homeurl . $post["term_url"]; ?>"><?php echo langval($post["terms_name"]); ?></a></p>
            <p>分类链接:<?php echo $homeurl . $post["term_url"]; ?></p>
            <p>链接:<?php echo $homeurl . $post["post_url"]; ?></p>
            <p>简介:<?php echo langval($post["post_excerpt"]); ?></p>
        </div>
    <?php

    }
}else{
    echo '<p>还未添加任何数据</p>';
}
    ?>
</div>
</div>