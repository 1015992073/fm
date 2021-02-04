<?php

namespace app\config;

use system\lib\Config as sysConfig;
/**
 * Database Configuration
 *
 * @package Config
 */

class Config extends sysConfig
{

	public $appTimezone = 'Asia/Shanghai';
	public $charset = 'UTF-8';
	public $defaulController = 'Index'; //默认控制器
	public $defaulMethod = 'index'; //默认方法

	public $supportLanguage = ["zh","en"]; //系统支持语言 ，如果为空或者设置["zh"]，表示至少支持中文
	public $actionLang = 'zh'; //默认的语言,为空或者设置"zh",表示中文
	public $subDomin = []; //['demo'=>"mydemo"];//二级域名设置，下标:表示二级域名demo.xxx.com;值:表示控制器目录(可以是mydemo/desd/xxx),需要注意的是，多语言是默认保留的二级域名，如cn.xxx.com

	public $siteName   = ['zh' => '铁蛋cms系统', 'en' => 'IronBall cms']; //铁蛋cms系统,IronBall cms
	public $siteDescription = ['zh' => '铁蛋cms系统 description', 'en' => 'IronBall cms description'];

	public $SysTemplateRootDirName = "view"; //将view移到web 目录
	public $adminDirName  = 'admin'; //后台目录
	public $adminTemplateName = 'default'; //后台模板名称

	public $templateRootDirName = 'templates'; //前台模板根目录
	public $templateName = 'default'; //前台模板名称

	public $apiName = 'api'; //api前缀
	public $errpage = 'errors' . DS . '404.php'; //错误页面 ,这个目录是相对$templateRootDirName目录下子目录
	public $dbprefix = ''; //数据库前缀
	public $mysql = [
		'DSN'      => '',
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'mycms',
		'DBDriver' => 'MySQLi',
		'DBPrefix' => '',
		'charset'  => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'port'     => 3306,
	];
}
