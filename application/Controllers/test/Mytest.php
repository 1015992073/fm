<?php

namespace App\Controllers\test;

use app\Controllers\Contents;
use system\lib\BaseService;


class Mytest extends Contents
{

	public function __construct()
	{
		parent::__construct();
	}
	public function index(...$args)
	{

		//$list=BaseService::debug()->createCatTestData(["4", "2,4", "2,4"]); //批量生成分类
		BaseService::debug()->createTestPosts(60000); //批量生产文章
		//$appbegin11 = microtime(true);
			//$append11 = microtime(true);
		//echo "获取总分类花费:" . (($append11 - $appbegin11) * 1000) . 'ms';
		//getTheChildCategoryToList(1);
		
		//$list =getAllCategoryToSql();
		
	
		//return $this->view($this->data);
	}
	public function fangfa()
	{

		createString();
		echo '运行控制test\Mytest\的方法fangfa';
	}
}
