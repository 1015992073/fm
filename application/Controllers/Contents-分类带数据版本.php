<?php

namespace App\Controllers;

use system\lib\Controller;

class Contents extends Controller
{
	public $data = [];
	public $category;



	/**
	 * 文件查找原则
	 * 原则上以控制器为主，然后在看是视图
	 */
	public function index()
	{

		//echo '当前内容url:' . uri_string() . "<br>";
		//$allCategory =   sqlQuery(['table' => 'category',  "pagesize" => 10000]);
		//var_dump($allCategory);
		$path_array = explode("/", uri_string()); //返回数组

		if (preg_match('/^[1-9][0-9]*(.html)?$/', $path_array[count($path_array) - 1]) && count($path_array) > 1) {
			//产品详细页
			if (in_array($path_array[0], $this->config->supportLanguage)) {
				//第一个是语言，删除语言
				array_shift($path_array);
			}

			$postId = str_replace(".html", "", $path_array[count($path_array) - 1]);
			//echo '產品詳細 ,id:' . $postId;
			$thePost = get_the_post($postId); //数组就表示文字  sqlQuery(['table' => 'posts', "where" => array("	post_id" => $postId)]);
			//var_dump($thePost);
			if (is_array($thePost) && count($thePost) > 0) {
				//存在详细页 ，在看分类是否正确
				$newPath = implode('/', $path_array); //去掉语言后的链接，对比分类的链接，如果匹配就是正确连接

				if ($newPath == $thePost["post_url"]) {
					//文章正确
					$this->data["post"] = $thePost;
					$this->data["title"] = '';
					$rootCategory = getTheParentsCategory($thePost["post_parent"]);
					$this->data["rootCategory"] = array_reverse($rootCategory);  //面包屑
					foreach ($rootCategory as $cat) {
						if ($this->data["title"] == "") {
							$this->data["title"] = langval($cat["terms_name"]);
						} else {
							$this->data["title"] .= " / " . langval($cat["terms_name"]);
						}
					}
					$this->data["title"] = langval($thePost["post_title"]) . " / " . $this->data["title"];
					$this->view($this->data, 2); //1 表示是分类 ,2详细页

				} else {
					$this->data["title"] = lang("000080"); //页面不存在
					$this->view($this->data, 3); //1 表示是分类 ,2详细页,0 404页
				}
			} else {
				$this->data["title"] = lang("000080"); //页面不存在
				$this->view($this->data, 3); //1 表示是分类 ,2详细页,0 404页
			}
		} else {
			//echo '分类</br>';
			$page = 1;
			if (preg_match('/page[1-9][0-9]*$/', $path_array[count($path_array) - 1])) {
				//带分页的分类
				$page = str_replace("page", "", $path_array[count($path_array) - 1]);
				array_pop($path_array); //删除页数
				//echo '带分页分类，页数:' . $page;
			}
			if (in_array($path_array[0], $this->config->supportLanguage)) {
				//第一个是语言，删除语言
				array_shift($path_array);
			}

			$theCategory =   sqlQuery(['table' => 'category', "where" => array("terms_slug" => $path_array[count($path_array) - 1])]); //查询最后一个分类名 

			if (is_array($theCategory) && count($theCategory) > 0) {
				$thecat = null;
				$newPath = implode('/', $path_array) . "/"; //去掉page后的链接，对比分类的链接，如果匹配就是正确连接
				//从分类匹配出结果
				foreach ($theCategory  as $cat) {
					if ($cat["term_url"] == $newPath) {
						$thecat = $cat;
					}
				}
				if ($thecat) {
					//分类正确
					//获取子类，分类应该包括其子类的所有文章
					$thePostList = getPosts(["page" => $page, 'term_id' => $thecat["term_id"]]); //获取文章列表

					$this->data["posts"] = $thePostList;
					$this->data["title"] = '';
					$rootCategory = getTheParentsCategory($thecat["term_id"]);

					$this->data["rootCategory"] = array_reverse($rootCategory);  //面包屑
					foreach ($rootCategory as $cat) {
						if ($this->data["title"] == "") {
							$this->data["title"] = langval($cat["terms_name"]);
						} else {
							$this->data["title"] .= " / " . langval($cat["terms_name"]);
						}
					}
					$this->view($this->data, 1); //1 表示是分类 ,2详细页

				} else {
					//分类不正确
					$this->data["title"] = lang("000081"); //分类不存在
					$this->view($this->data, 3);
					//echo "页面不存在";
				}
			} else {
				$this->data["title"] = lang("000081"); //分类不存在
				$this->view($this->data, 3);
				//echo "页面不存在";
			}
		}
	}

	//重写视图函数
	protected  function view($data = null,  $type = null)
	{
		/**
		 * 前台的模板支持 当前模板目录下/语言/模板文件  比如想额外修改en语言的模板，可以将该模板名放到en目录下
		 */
	
		$ViewDir = WEBPATH . $this->config->templateRootDirName . DS  . $this->config->templateName . DS; //前台模板目录

		if (isset($data) && is_array($data)) {
			//如果有数据，并且数据必须是数组 ，将数组拆成下标的变量
			extract($data);
		} else {
			extract(["data" => $data]);
		}

		$viewHeadFile = findTemplateFileInDir("header.php", $ViewDir);
		if ($viewHeadFile) {
			include_once($viewHeadFile);
		}
		//加载 分类模板或者详细页模板 这个地方可以修改成不同分类不同详细页实现不同模板
		$viewFile = findTemplateFileInDir("index.php", $ViewDir);
		if ($type == 1) {
			$viewFile = findTemplateFileInDir("category.php", $ViewDir);
		} else if ($type == 2) {
			$viewFile = findTemplateFileInDir("single.php", $ViewDir);
		} else if ($type == 3) {
			$viewFile = findTemplateFileInDir("404.php", $ViewDir);
		}

		if ($viewFile) {
			include_once($viewFile);
		} else {
			include_once(findTemplateFileInDir("index.php", $ViewDir));
		}

		$viewFooterFile = findTemplateFileInDir("footer.php", $ViewDir);
		if ($viewFooterFile) {
			include_once($viewFooterFile);
		}

		


	}
}
