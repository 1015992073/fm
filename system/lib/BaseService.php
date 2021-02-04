<?php

/**
 *  的基础服务类
 *
 */

namespace system\lib;

use system\lib\App;
use system\lib\Autoloader;
use system\lib\router;
use system\lib\Language;
use system\lib\Debug;
use system\lib\Gdata;
use app\config\config;

/**
 *
 */
class BaseService
{

	/**
	 * Cache for instance of any services that
	 * have been requested as a "shared" instance.
	 * Keys should be lowercase service names.
	 *
	 * @var array
	 */
	static protected $instances = [];
	static public $publicdata=[];//公共数据
	/**
	 * A cache of other service classes we've found.
	 *
	 * @var array
	 */
	static protected $services = [];

	//--------------------------------------------------------------------



	//--------------------------------------------------------------------

	/**
	 * The Autoloader class is the central class that handles our
	 * spl_autoload_register method, and helper methods.
	 *
	 * @param boolean $getShared
	 *
	 * @return \CodeIgniter\Autoloader\Autoloader
	 */
	public static function autoloader(bool $getShared = true)
	{
		if ($getShared) {
			if (empty(static::$instances['autoloader'])) {
				static::$instances['autoloader'] = new Autoloader();
			}

			return static::$instances['autoloader'];
		}

		return new Autoloader();
	}

	/**
	 * The Autoloader class is the central class that handles our
	 * spl_autoload_register method, and helper methods.
	 *
	 * @param boolean $getShared
	 *
	 * @return \CodeIgniter\Autoloader\Autoloader
	 */
	public static function App(bool $getShared = true)
	{
		if ($getShared) {
			if (empty(static::$instances['app'])) {
				static::$instances['app'] = new App();
			}

			return static::$instances['app'];
		}

		return new App();
	}
//全局数据
	public static function Gdata(bool $getShared = true)
	{
		if ($getShared) {
			if (empty(static::$instances['gdata'])) {
				static::$instances['gdata'] = new Gdata();
			}

			return static::$instances['gdata'];
		}

		return new Gdata();
	}
	/**
	 * The Autoloader class is the central class that handles our
	 * spl_autoload_register method, and helper methods.
	 *
	 * @param boolean $getShared
	 *
	 * @return \CodeIgniter\Autoloader\Autoloader
	 */
	public static function router(bool $getShared = true)
	{
		

		if ($getShared) {
			if (empty(static::$instances['router'])) {
				static::$instances['router'] = new router();
			}

			return static::$instances['router'];
		}

		return new router();
	}

	/**
	 * The Autoloader class is the central class that handles our
	 * spl_autoload_register method, and helper methods.
	 *
	 * @param boolean $getShared
	 *
	 * @return \CodeIgniter\Autoloader\Autoloader
	 */
	public static function Language(bool $getShared = true)
	{
		if ($getShared) {
			if (empty(static::$instances['language'])) {
				static::$instances['language'] = new Language();
			}

			return static::$instances['language'];
		}

		return new Language();
	}

	/**
	 * The Autoloader class is the central class that handles our
	 * spl_autoload_register method, and helper methods.
	 *
	 * @param boolean $getShared
	 *
	 * @return \CodeIgniter\Autoloader\Autoloader
	 */
	public static function View(bool $getShared = true)
	{
		if ($getShared) {
			if (empty(static::$instances['view'])) {
				static::$instances['view'] = new View();
			}

			return static::$instances['view'];
		}

		return new View();
	}


	/**
	 * The Autoloader class is the central class that handles our
	 * spl_autoload_register method, and helper methods.
	 *
	 * @param boolean $getShared
	 *
	 * @return \CodeIgniter\Autoloader\Autoloader
	 */
	public static function Database(bool $getShared = true)
	{
		if ($getShared) {
			if (empty(static::$instances['database'])) {
				static::$instances['database'] = new Database();
			}

			return static::$instances['database'];
		}

		return new Database();
	}
	/**
	 * 
	 * 配置文件实例
	 *
	 * @param boolean $getShared
	 *
	 * @return \app\config
	 */
	public static function config(bool $getShared = true)
	{
		if ($getShared) {
			if (empty(static::$instances['config'])) {
				static::$instances['config'] = new config();
			}

			return static::$instances['config'];
		}

		return new config();
	}

		/**
	 * 
	 * 配置文件实例
	 *
	 * @param boolean $getShared
	 *
	 * @return \app\config
	 */
	public static function debug(bool $getShared = true)
	{
		if ($getShared) {
			if (empty(static::$instances['debug'])) {
				static::$instances['debug'] = new Debug();
			}

			return static::$instances['debug'];
		}

		return new Debug();
	}

	

	/**
	 * Provides the ability to perform case-insensitive calling of service
	 * names. 运行静态函数
	 *__call()
　　　*当对象访问不存在的方法时，__call()方法会被自动调用
	 *__callStatic()
　　　*当对象访问不存在的静态方法时，__callStatic()方法会被自动调用
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @return mixed
	 */
	public static function __callStatic(string $name, array $arguments)
	{
		$classKey = strtolower($name);
		$class = $name;



		if (empty(static::$instances[$classKey])) {
			static::$instances[$classKey] = new $class();
		}

		return static::$instances[$classKey];
	}
}
