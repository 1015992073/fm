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
class Config
{

	public $sysMenu; //系统菜单

	public function __construct()
	{
		$this->sysMenu = $this->init();
	}

	public function init()
	{
		$menu[0] = array("link" => '', "description" => "首页", "label" => "home", 'role' => '', "status" => 1);

		return  $menu;
	}
}
