<?php

/**
 *
 * @filesource
 */

//语言输出
if (!function_exists('lang')) {
	/**
	 *
	 * @param string|null $key
	 *
	 * @return =
	 */
	function lang(string $key = null, $lang = '')
	{
		return system\lib\BaseService::Language()->lang($key, $lang);
	}
}

//显示数据库值，判断书否是多语言 ，是返回当前语言，不是原文显示 ，如果是多语言，但是没有当前设置的语言，显示第一个
if (!function_exists('langval')) {
	/**
	 * 语言输出，没找到输出空
	 */
	function langval($val = '',$lang = null)
	{
		if (isset($val) || $val===0 || $val===false ) {

			$config = system\lib\BaseService::config();
			if (strpos($val, "{") === 0) {
				//可能有多语言
				$optionArray = json_decode($val, true);
				if(isset($lang) ){

					if (isset($optionArray[$lang])) {
						return  $optionArray[$lang];
					}else{
						return  "";
					}
				}else{
					if (isset($optionArray[$config->actionLang])) {
						return  $optionArray[$config->actionLang];
					} else {
						if (is_array($optionArray) && count($optionArray) > 0) {
							$newval = array_keys($optionArray);
							return $optionArray[$newval[0]];
						} else {
							return  "";
						}
					}
				}
				
			} else {
				return  $val;
			}
		}
	}
}


//显示错误信息页面
if (!function_exists('errshow')) {
	function errshow($message = null)
	{
		return system\lib\BaseService::View()->errshow($message);
	}
}

//获取当前域名
if (!function_exists('base_url')) {
	function base_url()
	{
		$router = system\lib\BaseService::router();

		return $router->_server["REQUEST_SCHEME"] . "://" . $router->_server["SERVER_NAME"] . str_replace("index.php", "", $router->_server["SCRIPT_NAME"]); //REQUEST_URI 网站根目录是/
	}
}


//获取站点信息  
if (!function_exists('get_site_info')) {
	function get_site_info($show = null)
	{
		if (isset($show)) {
			$config = system\lib\BaseService::config();
			//$routes = service("Router");

			switch ($show) {
				case "name":
					if ($config->actionLang) {
						return $config->siteName[$config->actionLang];
					} else if (is_array($config->siteName) && count($config->siteName) > 0) {
						foreach ($config->siteName as $key => $name) {
							return $name;
							break;
						}
					} else if (is_string($config->siteName) && $config->siteName != "") {
						return $config->siteName;
					} else {
						return '';
					}

					break;
				case "description":
					return $config->siteDescription[$config->actionLang];
					break;
				case "homeurl":
					$lang = ($config->actionLang == $config->supportLanguage[0]) ? "" :  $config->actionLang;
					return base_url() . $lang.'/' ;
					break;
				case "baseurl":
					return base_url();
					break;
				case "template_url":
					//前臺模板路径
					return base_url() . $config->templateRootDirName . "/" . $config->templateName . "/";
					break;
				case "admin_url":
					return base_url()  . $config->adminDirName . "/";
					break;
				case "admin_template_url":
					//后臺模板路径,
					return base_url()  . $config->SysTemplateRootDirName . "/" . $config->adminDirName . "/" . $config->adminTemplateName . "/";
					break;
			}
		} else {
			return SYSVERSION;
		}
	}
}



//查找前台模板，主要是支持多语言的多模板
if (!function_exists('findTemplateFileInDir')) {
	function findTemplateFileInDir($fileName, $ViewDir = null)
	{
		if (is_string($fileName) && $fileName != "") {
			$config = system\lib\BaseService::config();
			if (!isset($ViewDir)) {
				$ViewDir = VIEWPATH;
			}

			if (file_exists($ViewDir . $config->actionLang . DS . $fileName)) {
				//如果存在 :前台模板/当前语言/父分类slug/子分类slug.php
				return 	$ViewDir . $config->actionLang . DS . $fileName; //返回目录形式的视图

			} else if (file_exists($ViewDir .  $fileName)) {
				//如果存在 :前台模板/父分类slug/子分类slug.php 没有语言，下同
				return 	$ViewDir . $fileName; //返回目录形式的视图
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

//根据当前路由(路径，控制器，方法)加载视图模板文件，支持不同语言不同模板
if (!function_exists('get_view_file')) {
	function get_view_file($ViewDir = '')
	{
		$router = system\lib\BaseService::router();
		$config = system\lib\BaseService::config();
		$jointMark = "-";
		$directory = $router->directory;
		$controller = $router->controller;
		$method = $router->method;
		$isadmin = false;
		if (stripos($directory, $config->adminDirName) === 0) {
			//当前路由路径有admin ，表示是admin ，因为admin支持多模板，所以视图目录应该是去掉admin
			$directory = substr($directory, strlen($config->adminDirName));
			if (strlen($directory) > 0) {
				//去掉前面的/
				$directory = substr($directory, 1) . DS; //路径后面加上/
			}
			$isadmin = true;
		}

		//如果存在 :后台模板/目录/目录/控制器/方法.php
		$templateFile = findTemplateFileInDir($directory  . DS . $controller . DS . $method . '.php', $ViewDir);
		if ($templateFile) {
			//$viewfile =	$ViewDir . str_replace("\\", DS, $directory) . $controller . DS . $method . '.php';
			//echo $templateFile;
			return 	$templateFile; //返回目录形式的视图

		}

		//2.連綫格式 xx-xxx-xxxx.php
		$templateFile = findTemplateFileInDir(str_replace(DS, $jointMark, $directory) . $jointMark . $controller . $jointMark . $method . '.php', $ViewDir);
		if ($templateFile) {
			//如果存在 :后台模板/目录-目录-控制器-方法.php
			//echo $templateFile;
			return 	$templateFile; //返回制器-方法的视图
		}

		//3.控制器/方法名 cat/add.php 
		$templateFile = findTemplateFileInDir($controller . DS . $method . '.php', $ViewDir);
		if ($templateFile) {
			//echo $templateFile;
			return 	$templateFile; //返回制器/方法的视图.php
		}
		//4. 是方法index ,看是否有控制器命名的php
		if (strtolower($method) == "index") {
			//4.1 如果存在 :后台模板/目录/目录/控制器.php
			$templateFile = findTemplateFileInDir($directory . $controller . '.php', $ViewDir);
			if ($templateFile) {
				//echo $templateFile;
				return 	$templateFile;
			}
			//4.2 如果存在 :后台模板/目录-目录-控制器.php
			$templateFile = findTemplateFileInDir(str_replace(DS, $jointMark, $directory) . $controller . '.php', $ViewDir);
			if ($templateFile) {
				//如果存在 :后台模板/目录-控制器.php
				//echo $templateFile;
				return 	$templateFile;
			}
		}

		//后台的通用增删更查

		if ($isadmin) {
			//当前路由路径有admin ，应该看是否使用通用增删更查 视图
			$templateFile = findTemplateFileInDir(str_replace(DS, $jointMark, $directory) .  $method . '.php', $ViewDir);
			if ($templateFile) {
				//如果存在 :后台模板/目录-方法.php
				//echo $templateFile;
				return 	$templateFile;
			}
		}

		return false;
	}
}

if (!function_exists('sqlQuery')) {
	/**
	 * 查询
	 * ,每次查询都检查了表存在，所有执行2次数据库查询操作,后期优化可以考虑取消表存在这个查询
	 * @param array $query  array(table=>"","pagesize"=>10,page=>1,orderby:'title DESC, name ASC',where:"",select=>'title, content, date')
	 * where:可以取如下：
	 * 数组 array('name', $name) or array('name !=', $name) 
	 * 字符串 "name='Joe' AND status='boss' OR status='active'";
	 * @return array
	 * 
	 */
	function sqlQuery($query = [])
	{
		if (isset($query) && isset($query["table"]) && is_string($query["table"]) && $query["table"] != '') {
			//$t1 = microtime(true); //获取程序1，结束的时间
			$sql    =  system\lib\BaseService::database();
			$table = $query["table"];
			/*
			if ($sql->table_exists(strtolower($table))) {
				//表存在
				} else {
				return false; //表不存在
			}
			*/
			//$sql = $db->table($table);
			$pagesize = (isset($query["pagesize"]) && is_integer($query["pagesize"])) ? $query["pagesize"] : false;
			$page = (isset($query["page"]) && is_numeric($query["page"])) ? $query["page"] : 1;
			$orderby = (isset($query["orderby"]) && is_string($query["orderby"])) ? $query["orderby"] : 'sort ASC, create_date DESC';
			$where = isset($query["where"]) ? $query["where"] : false; //['name !=' => $name, 'id <' => $id, 'date >' => $date, 'title' => $title, ]; or  "name='Joe' AND status='boss' OR status='active'";
			$orWhereIn = isset($query["orWhereIn"]) ? $query["orWhereIn"] : false;
			$whereIn = isset($query["whereIn"]) ? $query["whereIn"] : false;
			$select = (isset($query["select"]) && is_string($query["select"])) ? $query["select"] : false; //例子：'title, content, date'

			if ($select) {
				$sql->select($select);
			}
			if ($orderby) {
				$sql->order_by($orderby);
			}
			if ($pagesize) {
				$sql->limit($page, $pagesize); //
			}


			if ($where) {
				if (is_array($where) || is_string($where)) {
					$sql->where($where);
				}
			}
			//orWhereIn 和WhereIn 最好二选一
			if ($orWhereIn) {
				if (is_array($orWhereIn)) {
					// $orWhereIn 必须是 array("key"=>val)
					foreach ($orWhereIn as $key => $val) {
						$sql->or_where_in($key, $val);
					}
				}
			}
			if ($whereIn) {
				if (is_array($whereIn)) {
					// $orWhereIn 必须是 array("key"=>val)
					foreach ($whereIn as $key => $val) {
						$sql->where_in($key, $val);
					}
				}
			}
			$querylist = $sql->get($table);
			//$t2_3 = microtime(true); //获取程序1，结束的时间
			//echo '</br>查询用时:' . (($t2_3 - $t1) * 1000) . 'ms' . "<br>";
			return $querylist;
		} else {
			return false; //查询格式错误(表名要是字符串)
		}
	}
}


//从数据库获取所有分类 ，主要是保证每次获取分类都只查询一次

if (!function_exists('getAllCategoryToSql')) {
	function getAllCategoryToSql($orderby="sort ASC")
	{
		$str = microtime(true);

		$Gdata   =  system\lib\BaseService::Gdata(); //全局数据
		if (!isset($Gdata->data["allcategory"])) {

			//$Gdata->data["allcategory"] = sqlQuery(['table' => 'category', "orderby" => "term_id ASC"]);
			$Gdata->data["allcategory"] = sqlQuery(['table' => 'category', "orderby" => $orderby]);
		}
		$end = microtime(true);
		//echo "获取所有分类用时:" . (($end - $str) * 1000) . 'ms</br>';
		return $Gdata->data["allcategory"];
	}
}

// 迭代 获取分类父类
if (!function_exists('getTheParentsCategory')) {
	function getTheParentsCategory($catId = 0)
	{
		if (!is_numeric($catId)) {
			return [];
		}

		$categorys = getAllCategoryToSql();
		$tree = array();

		while ($catId != 0) {
			foreach ($categorys as $item) {
				if ($item['term_id'] == $catId) {

					$tree[$catId] = $item;
					$catId = $item['term_parents'];
					break;
				}
			}
		}
		return $tree;
	}
}

// 获取指定id分类信息
if (!function_exists('getTheCategory')) {
	/**
	 * 获取分类树 ，如果指定分类id ，返回gai ，$cat_p_c= 0 :返回该分类从顶级父到该分类，1:该分类子类，2：该分类顶级分类到该分类的子类
	 * 
	 */
	function getTheCategory($catId = 0)
	{
		$theCategory = sqlQuery(['table' => 'category',  "where" => array('term_id' => $catId)]); //全部分类;
		if (is_array($theCategory) && count($theCategory) > 0) {
			return $theCategory[0];
		} else {
			return false;
		}
	}
}


// 获取指定分类的子类，包括子类的子类 , 树状结构
if (!function_exists('getTheChildCategory')) {
	/**
	 * 获取指定分类子类，包括子类的子类
	 * $level :表示层级 如test1/test2 ,分类的层级
	 * 
	 */
	function getTheChildCategory($parent_id = 0, $data = [], $level = '')
	{
		$tree = array();
		if (count($data) == 0) {
			$data = getAllCategoryToSql();
		}

		foreach ($data as $k => $v) {
			if ($v["term_parents"] == $parent_id) {

				$theLevel = ($level != '') ? $level . '/' . $v["terms_slug"] : $v["terms_slug"];
				unset($data[$k]);
				if (!empty($data)) {
					$children = getTheChildCategory($v["term_id"], $data, $theLevel);
					if (!empty($children)) {
						$v["child"] = $children;
					}
				}
				$tree[$theLevel] = $v;
			}
		}
		return $tree;
	}
}



// 获取指定菜单树状结构
if (!function_exists('getTheChildMenu')) {
	/**
	 * 获取指定分类子类，包括子类的子类
	 * $level :表示层级 如test1/test2 ,分类的层级
	 * 
	 */
	function getTheChildMenu($parent_id = 0, $data = [])
	{
		$tree = array();
		if (count($data) == 0) {
			$data = sqlQuery(['table' => 'Menu',  "pagesize" => 10000]);
		}

		foreach ($data as $k => $v) {
			if ($v["menu_parents"] == $parent_id) {

				//$theLevel = ($level != '') ? $level . '/' . $v["terms_slug"] : $v["terms_slug"];
				unset($data[$k]);
				if (!empty($data)) {
					// $children = getTheChildMenu($v["term_id"], $data, $theLevel);
					$children = getTheChildMenu($v["menu_id"], $data);
					if (!empty($children)) {
						$v["child"] = $children;
					}
				}
				//$tree[$theLevel] = $v;
				$tree[] = $v;
			}
		}
		return $tree;
	}
}

// 获取指定分类的子类，包括子类的子类 , 一维数组，不包含层级 ,不包括自己
if (!function_exists('getTheChildCategoryToList')) {
	/**
	 * 获取指定分类子类，包括子类的子类
	 * $parent_ids :储存已经添加的父类
	 * 
	 */
	function getTheChildCategoryToList($parent_id = 0,  $categorys = null)
	{

		$subs = array();
		$cach = []; //临时储存
		$catindex = []; //key 为分类id ，值为该分类的index位置
		$parentandchild = []; //key 为分类id ，值为该分类所有子类
		if (!isset($categorys)) {
			$categorys = getAllCategoryToSql();
		}
		if ($parent_id === 0) {
			return $categorys;
		}
		$num = 1;
		foreach ($categorys as $item) {

			if ($item['term_parents'] == $parent_id) {

				$subs[] = $item;
				$subs = array_merge($subs, getTheChildCategoryToList($item['term_id'], $categorys));
			}
			$num++;
		}


		return $subs;
	}
}




// 获取文列表
if (!function_exists('getPosts')) {
	/**
	 * 获取文章列表
	 *$data=["page"=>1,"pagesize"=>"10","where"=>array("term_id"=>11),"orderby":'title DESC, name ASC',rand:false];
	 * "where"=>array("term_id"=>1);//获取分类为1的文章(包括它的子类)
	 * "where"=>array("term_id"=>array(1,2,3));//获取分类1,2,3，包括他们所有子类的文章
	 * "where"=>array("term_id >="=>1);//获取分类id大于1的所有分类
	 * "where"=>array("term_id "=>1,"publish_date >="=>"2021-01-01");//获取所有日期大于2021-01-01 的分类为1的文章
	 */
	function getPosts($data = [])
	{

		if (!isset($data)) {
			$data = [];
		}

		$sql    =  system\lib\BaseService::database();
		$table = 'posts';

		$pagesize = (isset($data["pagesize"]) && is_integer($data["pagesize"])) ? $data["pagesize"] : 10;
		$page = (isset($data["page"]) && is_numeric($data["page"])) ? $data["page"] : 1;


		if (isset($data["rand"]) && $data["rand"] === true) {
			$data["where"]["post_id >="] = '(SELECT floor(RAND() * (SELECT MAX(`post_id`) FROM `posts`)))'; //随机产生文章
		}
		//system\lib\BaseService::debug()->start("查询语句生成");
		if (isset($data["where"]) && is_array($data["where"])) {
			//"name='Joe' AND status='boss' OR status='active'";
			foreach ($data["where"] as $key => $item) {


				//如果没有< > ! ，看$item 是否是数组，  term_id 单独列出来  ，如果term_id 是一个数字，就表示获取它包括他的子类，如果是数组就不用 ,$item也只能有一个
				if ($key == "term_id") {

					if ($item === 0) {
						//顶级
						continue;
					}
					if (strpos($key, "!") !== false || strpos($key, ">") !== false || strpos($key, "<") !== false || strpos($key, "=") !== false) {
						$sql->where($key . '"' . $item . '"');
					} else {
						$ids = [];
						$myArray = [];
						if (is_array($item)) {
							$myArray = $item;
						} else if (is_numeric($item)) {
							$myArray = explode(",", $item);
						}


						foreach ($myArray as $id) {

							$theChild =  getTheChildCategoryToList($id);

							array_push($ids, $id);
							foreach ($theChild as $cat) {
								array_push($ids, $cat["term_id"]);
							}
						}

						array_flip($ids);
						$sql->where_in("post_parent", $ids);
					}
				} else {

					if (strpos($key, "!") !== false || strpos($key, ">") !== false || strpos($key, "<") !== false || strpos($key, "=") !== false) {
						//大于等于小于 ,如果是带符号，值必须是字符串或者数字，否则忽略这个选项
						if (is_string($item)) {
							if (strpos($item, "SELECT") === false) {
								$sql->where($key . '"' . $item . '"');
							} else {
								$sql->where($key . ' ' . $item . ' ');
							}
						} else if (is_numeric($item)) {
							$sql->where($key . ' ' . $item);
						}
					} else {
						if (is_array($item)) {
							$sql->where_in($key, $item);
						} else if (is_string($item) || is_numeric($item)) {
							$sql->where($key . '"' . $item . '"');
						}
					}
				}
			}
		}
		//system\lib\BaseService::debug()->end("查询语句生成");

		$sql->select("* ,(SELECT terms_name FROM `category` WHERE category.term_id=posts.post_parent ) as terms_name,(SELECT term_url FROM `category` WHERE category.term_id=posts.post_parent ) as term_url");
		$sql->limit($page, $pagesize); //
		system\lib\BaseService::debug()->start("文章列表查询");
		$querylist = $sql->get($table, true); //返回统计结果 array("total" => $count[0]["total"], "list" => $result);
		system\lib\BaseService::debug()->end("文章列表查询");
		return $querylist;
	}
}




// 通过id获取文章信息
if (!function_exists('get_the_post')) {
	/**
	 * 获取指定分类子类，包括子类的子类
	 * $level :表示层级 如test1/test2 ,分类的层级
	 * 
	 */
	function get_the_post($post_id = '')
	{
		if (isset($post_id) && $post_id != '') {
			$select = "* ,(SELECT terms_name FROM `category` WHERE category.term_id=posts.post_parent ) as terms_name,(SELECT term_url FROM `category` WHERE category.term_id=posts.post_parent ) as term_url";

			$thePost =   sqlQuery(['table' => 'posts', "select" => $select, "where" => array("post_id" => $post_id)]);
			if (is_array($thePost) && count($thePost) > 0) {
				return $thePost[0];
			} else {
				return false; //文章数据为空
			}
		} else {
			return false; //参数错误
		}
	}
}

//获取当前链接 index.php/后的链接
if (!function_exists('uri_string')) {

	function uri_string()
	{
		$router = system\lib\BaseService::router();
		return trim($router->_server["PATH_INFO"], "/");
	}
}

//获取分页导航
if (!function_exists('get_page_nav')) {
	function get_page_nav($link = "", $tol = 0, $page = 1, $pagesize = 10)
	{

		$shownum = 10; //总共显示为5个 $shownum*2+1个
		$befnum = ceil($shownum / 2); //前面显示个数
		$tol = is_numeric($tol) ? ($tol < 0 ? 0 : $tol) : 0;
		$pagesize = is_numeric($pagesize) ? ($pagesize <= 0 ? 10 : $pagesize) : 10;
		$page = is_numeric($page) ? ($page <= 0 ? 1 : $page) : 1;
		$pagenav = '';
		$tolnum = ($tol % $pagesize) == 0 ? $tol / $pagesize : ceil($tol / $pagesize);

		$first = '<a class="page-numbers" href="' . $link . "page1" . '">首页</a>'; //首页
		$end = '<a class="page-numbers" href="' . $link . "page" . $tolnum . '">最后一页</a>'; //最后一页

		if ($page > 1) {
			//上一页
			$pagenav .= '<a class="prev page-numbers" href="' . $link . "page" . ($page - 1) . '">上一页</a>'; //上一页
		}
		if ($tolnum <= $shownum) {
			//总数小于要显示的

			for ($i = 1; $i <= $tolnum; $i++) {
				$active = ($i == $page) ? "current" : "";
				$pagenav .= '<a class="page-numbers ' . $active . ' " href="' . $link . "page" . $i . '">' . $i . '</a>'; //全部
			}
		} else {
			/*
        总数大于要显示的 ,通过当前确定开始页数
        */

			if ($page <= $befnum) {
				$thes = 1;
				$thee = $shownum;
			} else if ($page > $befnum && $page + ($shownum - $befnum) <= $tolnum) {
				$thes = $page - $befnum + 1;
				$thee = $page + ($shownum - $befnum);
			} else if ($page + ($shownum - $befnum) > $tolnum) {

				$thes = $tolnum - $shownum;
				$thee = $tolnum;
			}

			for ($i = $thes; $i <=  $thee; $i++) {
				$active = $i == $page ? "current" : "";
				$pagenav .= '<a class="page-numbers ' . $active . ' " href="' . $link . "page" . $i . '">' . $i . '</a>'; //
			}
		}


		if ($page < $tolnum) {
			//下一页
			$pagenav .= '<a class="next page-numbers" href="' . $link . "page" . ($page + 1) . '">下一页</a>'; //上一页
		}


		$pagenav .= '<a class="next page-numbers" href="javascript:void(0)">共' . $tolnum . '页</a>'; //共多少页面

		return  $pagenav;
	}
}

//查询语句
if (!function_exists('sql_query')) {
	function sql_query($query = [])
	{
		$sql    =  system\lib\BaseService::database();
		if (isset($query) && is_array($query) && isset($query["table"])) {
			$table = $query["table"];
			$page = 1;
			$pagesize = 10;
			if (isset($query["page"]) && is_integer($query["page"]) && $query["page"] > 1) {
				$page = $query["page"];
			}
			if (isset($query["pagesize"]) && is_integer($query["pagesize"]) && $query["pagesize"] != 10) {
				$pagesize = $query["pagesize"];
			}
			$sql->limit($page, $pagesize);
			if (isset($query["where"])) {
				$sql->where($query["where"]);
			}
			if (isset($query["or_where"])) {
				$sql->or_where($query["or_where"]);
			}
			if (isset($query["orderby"])) {
				$sql->order_by($query["orderby"]);
			}
			//还可以继续加database 里的函数
			return $sql->get($table, true); //返回带总数的列表
		} else {
			return false;
		}
	}
}

//直接使用字符串查询语句
if (!function_exists('sql_str_query')) {
	function sql_str_query($querystr = null, $return = true)
	{

		if (isset($querystr) && is_string($querystr) && $querystr != "") {
			$sql    =  system\lib\BaseService::database();
			$result = $sql->query($querystr);
			if ($return === true) {
				$resultarray = $sql->getfetch($result);
				return $resultarray;
			} else {
				return $result;
			}
		} else {
			return false;
		}
	}
}

//直接使用字符串查询语句,多条 用;分割
if (!function_exists('multi_sql_str_query')) {
	function multi_sql_str_query($querystr = null, $return = true)
	{

		if (isset($querystr) && is_string($querystr) && $querystr != "") {
			$sql    =  system\lib\BaseService::database();
			return $sql->multi_query($querystr);
		} else {
			return false;
		}
	}
}

// 获取文列表
if (!function_exists('get_posts')) {
	/**
	 * 获取文章列表  ，参数参考get_table_list() 函数de$query
	 *$data=["page"=>1,"pagesize"=>"10","where"=>array("term_id"=>11),"orderby":'title DESC, name ASC',"select"=>"*"];
	 * "where"=>array("post_parent"=>1);//获取分类为1的文章(包括它的子类)
	 * "where"=>array("post_parent"=>array(1,2,3));//获取分类1,2,3，注意包括他们所有子类的文章
	 * "where"=>array("post_parent >="=>1);//获取分类id大于1的所有分类
	 * "where"=>array("post_parent "=>1,"publish_date >="=>"2021-01-01");//获取所有日期大于2021-01-01 并且分类为1的文章
	 * where可以是posts表里的任何字段(一个或者多个)
	 */
	function get_posts($query = [])
	{
		$get_post_s = microtime(true);
		if (is_string($query) && $query != "") {
			return	get_table_list($query);
		} else if (isset($query)) {
			if (isset($query["where"]) && is_array($query["where"]) && count($query["where"]) > 0) {
				$where = $query["where"];
				if (isset($where["term_name"])) {
				}

				if (isset($where["post_parent"])) {
					//如果设置了文章分类，分类的子类也应该包括进来
					$newids = [];
					$p_ids = [];
					if (is_array($where["post_parent"])) {
						$p_ids = $where["post_parent"];
					} else if (is_numeric($where["post_parent"])) {
						$p_ids = [$where["post_parent"]];
					}
					$ids_titme_s = microtime(true);
					foreach ($p_ids as $id) {
						$getTheChildCategoryToList_s = microtime(true);
						$theChild =  getTheChildCategoryToList($id);
						$getTheChildCategoryToList_e = microtime(true);
						echo "getTheChildCategoryToList 用时:" . (($getTheChildCategoryToList_e - $getTheChildCategoryToList_s) * 1000) . 'ms</br>';
						array_push($newids, $id);

						foreach ($theChild as $cat) {
							if (!in_array($cat["term_id"], $newids)) {
								array_push($newids, $cat["term_id"]);
							}
						}
					}
					$ids_titme_end = microtime(true);
					echo "foreach 批量处理所有父类id用时:" . (($ids_titme_end - $ids_titme_s) * 1000) . 'ms</br>';
					$query["where"]["post_parent"] = $newids;
				} //处理 文章分类id end
			}

			$query["select"] = "* ,(SELECT terms_name FROM `category` WHERE category.term_id=posts.post_parent ) as terms_name,(SELECT term_url FROM `category` WHERE category.term_id=posts.post_parent ) as term_url";
			$query["table"] = "posts";
			$get_table_list_s = microtime(true);
			$thelist =	get_table_list($query);
			$get_post_end = microtime(true);
			//echo "get_table_list 用时:" . (($get_post_end - $get_table_list_s) * 1000) . 'ms</br>';
			//echo "get_post 用时:" . (($get_post_end - $get_post_s) * 1000) . 'ms</br>';
			return $thelist;
		} else {
			return false;
		}
	}
}



// 获取文列表
if (!function_exists('get_posts_test')) {
	/**
	 * 获取文章列表  这个是2条一起查询 ，性能没有2条语句分开执行的快 ，测试用，可以删除
	 *$data=["page"=>1,"pagesize"=>"10","where"=>array("term_id"=>11),"orderby":'title DESC, name ASC',rand:false];
	 * "where"=>array("post_parent"=>1);//获取分类为1的文章(包括它的子类)
	 * "where"=>array("post_parent"=>array(1,2,3));//获取分类1,2,3，包括他们所有子类的文章
	 * "where"=>array("post_parent >="=>1);//获取分类id大于1的所有分类
	 * "where"=>array("post_parent "=>1,"publish_date >="=>"2021-01-01");//获取所有日期大于2021-01-01 并且分类为1的文章
	 */
	function get_posts_test($where = [])
	{
		$sql = '';
		if (is_string($where) && $where != "") {
			$sql = $where;
		} else if (isset($where) && is_array($where) && count($where) > 0) {
			$pagesize = (isset($where["pagesize"]) && is_integer($where["pagesize"])) ? $where["pagesize"] : 10;
			$page = (isset($where["page"]) && is_numeric($where["page"])) ? $where["page"] : 1;
			$where_str = '';
			if (is_array($where) && count($where) > 0) {

				if (isset($where["term_name"])) {
				}

				if (isset($where["post_parent"])) {
					//如果设置了文章分类，分类的子类也应该包括进来
					$newids = [];
					$p_ids = [];
					if (is_array($where["post_parent"])) {
						$p_ids = $where["post_parent"];
					} else if (is_numeric($where["post_parent"])) {
						$p_ids = [$where["post_parent"]];
					}

					foreach ($p_ids as $id) {
						$theChild =  getTheChildCategoryToList($id);
						array_push($newids, $id);
						foreach ($theChild as $cat) {
							if (!in_array($cat["term_id"], $newids)) {
								array_push($newids, $cat["term_id"]);
							}
						}
					}
					$where["post_parent"] = $newids;
				} //处理 文字分类id end

				foreach ($where as $key => $item) {
					$where_c = '';
					if (strpos($key, "!") !== false || strpos($key, ">") !== false || strpos($key, "<") !== false || strpos($key, "=") !== false) {
						if (is_numeric($item)) {
							$where_c = $key  . $item;
						} else if (is_string($item)) {
							$where_c = $key . "'" . $item . "'";
						}
					} else {
						if (is_string($item)) {
							if (is_numeric($item)) {
								$where_c = $key  . "="  . $item;
							} else if (is_string($item)) {
								$where_c = $key  . "=" . "'" . $item . "'";
							}
						} else if (is_array($item)) {
							$in_array = "('" . implode("','", $item) . "')";
							$where_c = $key . " IN " . $in_array;
						}
					}

					if (strlen($where_str) > 0) {
						$where_str .= " AND " . $where_c;
					} else {
						$where_str = $where_c;
					}
				} //end foreach

			} else {
				return false;
			}
			$table = 'posts';
			$limit = " LIMIT " . ($page - 1) * $pagesize . ", " . $pagesize;
			$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `" . $table . "` WHERE  " . $where_str . $limit . " ;SELECT FOUND_ROWS()";
		} else {
			return false;
		}
		$db    =  system\lib\BaseService::database();
		//echo $sql;
		return $db->multi_query($sql);
	}
}


//通用获取表列表
if (!function_exists('get_table_list')) {
	/**
	 * 获取根据条件 获取数据库列表
	 * 参看get_posts ，主要是 where
	 * return :list ,返回列表，tol,只返回数量，为空或者不设置返回列表和数量
	 * $query=["page"=>1,"pagesize"=>"10","table"=>"posts","where"=>array("term_id"=>11),"orderby"=>'title DESC, name ASC',"select"=>"*",return=>""];
	 */
	function get_table_list($query = [])
	{

		//	$get_table_list_s = microtime(true);
		$sql = '';
		if (is_string($query) && $query != "") {
			$sql = $query;
		} else if (isset($query) && isset($query["table"])) {

			$where_str = '';
			if (isset($query["where"]) && is_array($query["where"]) && count($query["where"]) > 0) {
				$where = $query["where"];
				foreach ($where as $key => $item) {
					$where_c = '';
					if (strpos($key, "!") !== false || strpos($key, ">") !== false || strpos($key, "<") !== false || strpos($key, "=") !== false) {
						if (is_numeric($item)) {
							$where_c = $key  . $item;
						} else if (is_string($item)) {
							$where_c = $key . "'" . $item . "'";
						}
					} else {
						if (is_string($item)) {
							if (is_numeric($item)) {
								$where_c = $key  . "="  . $item;
							} else if (is_string($item)) {
								$where_c = $key  . "=" . "'" . $item . "'";
							}
						} else if (is_array($item)) {
							$in_array = "('" . implode("','", $item) . "')";
							$where_c = $key . " IN " . $in_array;
						}
					}

					if (strlen($where_str) > 0) {
						$where_str .= " AND " . $where_c;
					} else {
						$where_str = $where_c;
					}
				} //end foreach
				$where_str = " WHERE  " . $where_str;
			}


			$table = $query["table"];
			$pagesize = (isset($query["pagesize"]) && is_integer($query["pagesize"])) ? $query["pagesize"] : false;
			$page = (isset($query["page"]) && is_numeric($query["page"])) ? $query["page"] : 1;
			$orderby = (isset($query["orderby"]) && is_string($query["orderby"])) ? " ORDER BY " . $query["orderby"] : " ORDER BY create_date DESC"; //' ORDER BY sort ASC, create_date DESC';//添加排序比不添加快很多  
			$select = (isset($query["select"]) && is_string($query["select"])) ? $query["select"] : '*';
			if ($pagesize !== false) {
				$limit = " LIMIT " . ($page - 1) * $pagesize . ", " . $pagesize;
			} else {
				$limit = "";
			}

			$get_list_sql_str = "SELECT " . $select . " FROM " . $table .  $where_str . $orderby . $limit;
			$get_tol_num_sql_str = "SELECT create_date FROM " . $table . $where_str;
			//echo $get_list_sql_str . "</br>";
			//echo $get_tol_num_sql_str . "</br>";
			$db    =  system\lib\BaseService::database();
			//return array("total" => $resultnum->num_rows, "list" => $resultarray);

			if (isset($query["return"]) && $query["return"] == "list") {
				$sqlq1s = microtime(true);
				$result = $db->query($get_list_sql_str); //获取列表
				$sqlq1e = microtime(true);
				//echo "第1条查询用时:" . (($sqlq1e - $sqlq1s) * 1000) . 'ms</br>';
				$resultarray = $db->getfetch($result); //处理列表
				$sqlq1c = microtime(true);
				//echo "第1条处理用时:" . (($sqlq1c - $sqlq1e) * 1000) . 'ms</br>';
				return $resultarray;
			} else {

				if (isset($query["return"]) && $query["return"] == "tol") {
					$sqlq2s = microtime(true);
					$resultnum = $db->query($get_tol_num_sql_str); //获取数量
					$sqlq2e = microtime(true);
					//echo "第2条查询用时:" . (($sqlq2e - $sqlq2s) * 1000) . 'ms</br>';
					return $resultnum->num_rows;
				} else {
					$sqlq1s = microtime(true);
					$result = $db->query($get_list_sql_str); //获取列表
					$sqlq1e = microtime(true);
					//echo "第1条查询用时:" . (($sqlq1e - $sqlq1s) * 1000) . 'ms</br>';
					$resultarray = $db->getfetch($result); //处理列表
					$sqlq1c = microtime(true);
					//echo "第1条处理用时:" . (($sqlq1c - $sqlq1e) * 1000) . 'ms</br>';

					$sqlq2s = microtime(true);

					$resultnum = $db->query($get_tol_num_sql_str); //获取数量

					$sqlq2e = microtime(true);
					//echo "第2条查询用时:" . (($sqlq2e - $sqlq2s) * 1000) . 'ms</br>';
					return array("total" => $resultnum->num_rows, "list" => $resultarray);
				}
			}
			//$get_table_list_e = microtime(true);
			//echo "get_table_list 用时:" . (($get_table_list_e - $get_table_list_s) * 1000) . 'ms</br>';
		} else {
			return false;
		}
	}
}

//创建表单元素
if (!function_exists('createFieldHtml')) {
	function createFieldHtml($fieldItem)
	{
		//$fieldItem 为每个控制器modelFields 数组
		if (isset($fieldItem) && is_array($fieldItem) && isset($fieldItem["primary_key"]) && $fieldItem["primary_key"] != "") {
			$multipleLang = isset($fieldItem["multiple"]) ? $fieldItem["multiple"] : false; //是否是多语言
			$field = $fieldItem["primary_key"]; //表字段名
			$field_name='data'.((isset($fieldItem["field_name"]) && $fieldItem["field_name"]!="")?$fieldItem["field_name"]:$field);//字段名
			$value = isset($fieldItem["value"])?$fieldItem["value"]:$fieldItem["default"];
			$type = isset($fieldItem["type"]) ? $fieldItem["type"] : "text"; //表单类型
			$option = isset($fieldItem["option"]) ? $fieldItem["option"] : []; // 可以选值，type 为select checkbox ，raido 等
			$class = isset($fieldItem["class"]) ? $fieldItem["class"] : ""; //给表单自定义css class ，可能有
			$lang= isset($fieldItem["lang"])?$fieldItem["lang"]:null;//当前语言
			$form_item='';
			switch ($type) {
				case 'textarea':

					$form_item = '<textarea class="form-control ' . $class . ' "  rows="3" name="' .  $field_name . '" placeholder="">' . langval($value,$lang) . '</textarea>';

					break;
				case 'text':

					$form_item = '<input type="text" class="form-control ' . $class . '" placeholder="" value="' . langval($value,$lang) . '" name="' . $field_name . '">';

					break;
				case 'select':
					$options = '';
					if (isset($option) && count($option)) {
						foreach ($option  as $key => $val) {
							if (isset($value) && $key == $value) {
								$options .= '<option selected value ="' . $key . '">' . $val . '</option>';
							} else {
								$options .= '<option value ="' . $key . '">' . $val . '</option>';
							}
						}
					}
					$form_item = '<select class="form-control ' . $class . ' " name="' . $field_name . '" >' . $options . '</select>';

					break;
				case 'checkbox':

					$options = '';
					if (isset($option) && count($option) > 0) {

						foreach ($option  as $key => $val) {
							//check是多选，$value 有好几个
							if (isset($value) && is_array($value) && in_array($key, $value)) {
								$options .= '<div class="form-check ' . $class . ' "> <input class="form-check-input" type="checkbox" name="' . $field_name . '[]"   value="' . $key . '" checked><label class="form-check-label" >' . $val . ' </label> </div>';
							} else {
								$options .= '<div class="form-check ' . $class . ' "> <input class="form-check-input" type="checkbox" name="' . $field_name . '[]"   value="' . $key . '"><label class="form-check-label" >' . $val . ' </label> </div>';
							}
						}
					}
					$form_item =  $options;

					break;
				case 'radio':

					$options = '';
					if (isset($option) && count($option)) {

						foreach ($option  as $key => $val) {
							if (isset($value) && $key == $value) {
								$options .= '<div class="form-check ' . $class . ' "> <input class="form-check-input" type="radio" name="' . $field_name . '"   value="' . $key . '" checked><label class="form-check-label" >' . $val . ' </label> </div>';
							} else {
								$options .= '<div class="form-check ' . $class . ' "> <input class="form-check-input" type="radio" name="' . $field_name . '"   value="' . $key . '"><label class="form-check-label" >' . $val . ' </label> </div>';
							}
						}
					}
					$form_item =  $options;

					break;
				case 'hidden':
					$form_item = '<input type="hidden" class="form-control ' . $class . ' " placeholder="" value="' . $value . '" name="' .$field_name . '">';
					break;
				case 'password':
					$form_item = '<input type="password" class="form-control ' . $class . ' " placeholder="" value="' . $value . '" name="' . $field_name . '">';
					break;
					
			}
			return $form_item;
		}
	}
}

//获取配置
if (!function_exists('get_config')) {
	function get_config($key)
	{
		//$fieldItem 为每个控制器modelFields 数组
	if(isset($key) && $key!=''){
		$config = system\lib\BaseService::config();
		if(isset($config->$key)){
			return $config->$key;
		}
	}
	}
}
