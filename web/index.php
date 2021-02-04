<?php

/*
$minPHPVersion = '7.2';
if (phpversion() < $minPHPVersion) {
    die("Your PHP version must be {$minPHPVersion} or higher to run CodeIgniter. Current version: " . phpversion());
}
unset($minPHPVersion);
*/
$appbegin = microtime(true);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('SYSVERSION') or define('SYSVERSION', "v1.0");
if (!defined('ROOTPATH')) {
    define('ROOTPATH', realpath(__DIR__ . '/..') .  DS); //根目录
}

if (!defined('APPPATH')) {
    define('APPPATH', ROOTPATH . 'application' . DS); //application目录
}
if (!defined('SYSTEMPATH')) {
    define('SYSTEMPATH', ROOTPATH . 'system' . DS); //application目录
}

if (!defined('LIBPATH')) {
    define('LIBPATH', SYSTEMPATH . 'lib' . DS); //library目录
}
if (!defined('CONTROLLERSPATH')) {
    define('CONTROLLERSPATH', APPPATH . 'Controllers' . DS); //Controllers目录
}

if (!defined('VIEWPATH')) {
    define('VIEWPATH', APPPATH . 'Views' . DS); //Views目录
}

if (!defined('WEBPATH')) {
    define('WEBPATH', ROOTPATH . 'web' . DS); //Views目录
}

if (!defined('ADMIN')) {
    define('ADMIN', 'admin'); //admin 命名空间
}

defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

define('APP_DEBUG', True); // 调试模式

require_once LIBPATH . 'Autoloader.php'; //定义命名空间CodeIgniter\Autoloader 下的Autoloader 类
require_once LIBPATH . 'BaseService.php'; //定义命名空间CodeIgniter\Autoloader 下的Autoloader 类
require_once SYSTEMPATH . 'Common.php'; //加载全局函数
$lodafile = microtime(true);
//echo "加载自动，基础服务 、全局文件" . (($lodafile - $appbegin) * 1000) . 'ms</br>';
$loader = system\lib\BaseService::autoloader(); //定义一个自动加载器
$loader->initialize(); //初始化自动加载器
$loader->register();
//COMPOSER 自动加载项目
$autofile = microtime(true);
//echo "初始自动加载:" . (($autofile - $lodafile) * 1000) . 'ms</br>';
if (is_file(COMPOSER_PATH)) {
    if (!defined('VENDORPATH')) {
        define('VENDORPATH', realpath(ROOTPATH . 'vendor') . DIRECTORY_SEPARATOR);
    }
    require_once COMPOSER_PATH;
}
$composerstr = microtime(true);
//echo "composer自动加载加载:" . (($composerstr - $autofile) * 1000) . 'ms</br>';
system\lib\BaseService::debug()->start("app");

$app = system\lib\BaseService::App();//有2-5ms
$app->run();
system\lib\BaseService::debug()->end("app");
//system\lib\BaseService::debug()->print(); //打印调试结果
$append = microtime(true);
echo "程序总时长:" . (($append - $appbegin) * 1000) . 'ms';

/**
 * 2021-01-14
 * 加载自动，基础服务 、全局文件9.9990367889404ms
 * 初始自动加载:0.99992752075195ms
 * composer自动加载加载:4.9989223480225ms(已经去掉)
 * debug 初始:4.000186920166ms
 * app 45.99ms
 * 程序总时长:67.992925643921ms
 * 
 */
