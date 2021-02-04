<?php

/**

 *
 * @package    多语言类包

 * @filesource
 */

namespace system\lib;

/**
 * Handle system messages and localization.
 *
 * Locale-based, built on top of PHP internationalization.
 *
 * @package lib
 */
class Controller
{

	protected $router;
	protected $config;
	protected $db;
	public $data = []; //控制器數據
	//--------------------------------------------------------------------

	public function __construct()
	{
		$this->db = BaseService::database();
		$this->router = BaseService::router();
		$this->config = BaseService::config();
	}
//视图，如果自定义控制器，继承主控制器，然后重写这个view即可
	protected function view($viewdata = [], $viewFile = null)
	{
		if (!isset($viewFile)) {
		
			$ViewDir = VIEWPATH ;//默认去application\Views 查找
			$viewFile = get_view_file($ViewDir);
		}
		$viewFile = substr(strrchr($viewFile, '.'), 1) == "php" ? $viewFile : ($viewFile . ".php"); //加上php后缀
	
		if (file_exists($viewFile)) {
			include_once($viewFile);
			if (isset($data) && is_array($data)) {
				//如果有数据，并且数据必须是数组 ，将数组拆成下标的变量
				extract($data);
			} else {
				extract(["data" => $data]);
			}
		} else {
			echo '视图文件' . $viewFile . "未找到";
		}
	}
}
