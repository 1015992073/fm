<?php

/**

 *
 * @package   视图类

 * @filesource
 */

namespace system\lib;

/**
 * Handle system messages and localization.
 *
 * Locale-based, built on top of PHP internationalization.
 *
 * @package system\lib
 */
class View
{

	protected $data = [];
	//--------------------------------------------------------------------

	public function __construct()
	{
	}

	//并没有使用
	public  function display($viewFile = null, $data = null)
	{
		//var_dump($router);

		if (!isset($viewFile) || $viewFile == '') {
			$router = BaseService::router();
			$config = BaseService::config();
			$theController = ((isset($router->directory) && $router->directory != '') ? $router->directory . "/" : "") . (isset($router->controller) ? $router->controller . "/" : "");
			if ($theController == $config->defaulController . "/") {
				//当前路由是默认路由 ,视图就在view文件下找 
				$viewFile = VIEWPATH . $router->method . ".php";
			} else {
				$viewFile = VIEWPATH . $theController . (isset($router->method) ? $router->method : "") . ".php";
			}
		}

		$viewFile = substr(strrchr($viewFile, '.'), 1) == "php" ? $viewFile : ($viewFile . ".php"); //加上php后缀
		if (file_exists($viewFile)) {
			if (isset($data) && is_array($data)) {
				//如果有数据，并且数据必须是数组 ，将数组拆成下标的变量
				extract($data);
			}

			include_once($viewFile);
		} else {
			echo '视图文件' . $viewFile . "未找到";
		}
	}
	//错误页面
	public  function errshow($message = null)
	{
		$config = BaseService::config();
		if (!isset($message)) {
			$message = lang("unknown_error");
		}
		$viewFile = WEBPATH . $config->templateRootDirName  . DS . $config->errpage;
		if (file_exists($viewFile)) {
			$err = ["message" => $message];
			include_once($viewFile);
		}
	}
}
