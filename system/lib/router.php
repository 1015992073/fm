<?php

namespace system\lib;

class Router
{
    /**
     * 路径
     * @var string
     */
    public $directory;
    /**
     * 控制器
     * @var string
     */
    public $controller;
    /**
     * 方法名
     * @var string
     */
    public $method;
    /**
     * 参数，路径,控制器,方法以外都是参数，按顺序
     * @var array
     */
    public $arguments = [];
    /**
     * $_SERVER的值
     * @var array
     */
    public $_server;
    protected $pathArray; //路径数组

    public function __construct()
    {
        //var_dump($_SERVER);
        $this->_server = $_SERVER;
    }
    //初始化 ，根据当前路由设定路径，控制器，方法
    public function init()
    {


        //var_dump($this->_server);
        $path = trim(str_replace($this->_server['SCRIPT_NAME'], "", $this->_server["PHP_SELF"]), "/"); //获取index.php后的路径
        // echo '</br>路径为:' . $path . "</br>";

        $notfound = $this->initRouterByPath($path); //根据路径初始化路由，设置目录控制器和方法 花费1-2ms


        if ($notfound) {
            //未找到,全部跳转到内容页
            BaseService::debug()->start("内容页面(前台)");
            $this->directory = "";
            $this->controller = "Contents";
            $method =  "index";
            $controllerBaseNameSpace = 'app\\controllers\\'; //控制器基础命名空间
            $class = $controllerBaseNameSpace . "Contents";

            $obj = new $class(); //花费8-10ms

            $obj->$method();
            $apptestend2 = microtime(true);

            BaseService::debug()->end("内容页面(前台)");
        }
    }

    /**
     * 根据路径初始化路由，设置目录控制器和方法
     * 控制器目录使用小写或者首字符大写
     * 控制类首字母大写
     * 方法全部小写
     * 查找方式：先文件后目录
     * 未找到返回false
     */
    public function  initRouterByPath($path = null)
    {
        $controllerBaseNameSpace = 'app\\controllers\\'; //控制器基础命名空间
        $config = BaseService::config();

        $isNotfind = true; //没有找到控制或者方法
        if (isset($path) && is_string($path)) {

            // 1. 和多语言 ,多语言分二级和
            if (is_array($config->supportLanguage)) {

                foreach ($config->supportLanguage as $lang) {

                    if ((strpos($path, $lang) === 0 && strlen($path) == strlen($lang)) || strpos($path, $lang . "/") === 0) {
                        //以语言开头，将路径里的语言去掉
                        // $path=str_replace($lang . "/", '', $path);
                        // $path= str_replace($lang, '', $path);

                        $config->actionLang = $lang;
                    }
                    if (!isset($config->subDomin[$lang])) {
                        $config->subDomin[$lang] = ""; //将语言加入二级域名
                    }
                }
                $repstr = '/^' .  $config->actionLang . "\/?/";
                $path =  preg_replace($repstr, '', $path);
            }

            //2.处理二级域名
            if (is_array($config->subDomin) && count($config->subDomin) > 0) {
                $dominArr = explode(".", $this->_server['SERVER_NAME']);
                foreach ($config->subDomin as $key => $val) {
                    if ($dominArr[0] == $key) {
                        //匹配到二级域名
                        if ($val != "") {
                            $path = $val . "/" . $path; //将二级域名的路径添加到原始路径上
                        }
                    }
                }
            }

            //3.处理路径开始
            if ($path == "") {

                $class = $controllerBaseNameSpace .  $config->defaulController;
                $obj = new $class();
                if (method_exists($obj, $config->defaulMethod)) {
                    // echo '默认控制器存在 和方法存在';
                    $this->directory = "";
                    $this->controller = $config->defaulController;
                    $method = $config->defaulMethod;
                    $this->method = $method;
                    $isNotfind = false; //找到
                    $obj->$method();
                } else {
                    //errshow(lang("defaul") . lang("controllerenotfound") . lang("defaul") . lang("methodnotfound") . "/" . __LINE__);
                    //echo $class . '默认控制器不存在 ，router:' . __LINE__;
                    $isNotfind = true;
                }
            } else {
                $pathArray = explode("/", $path);
                if (is_array($pathArray)) {
                    if (count($pathArray) == 1) {
                        //只有一个就是控制类文件或者文件夹(文件夹有默认控制器)
                        if (file_exists(CONTROLLERSPATH . ucfirst($pathArray[0]) . ".php")) {
                            //控制器文件存在，查看方法是否存在
                            $class = $controllerBaseNameSpace . $pathArray[0];
                            $obj = new $class();
                            if (method_exists($obj, $config->defaulMethod)) {
                                // echo  '控制器' . $class . '存在 ,方法：' . $config->defaulMethod . '存在';
                                $this->directory = "";
                                $this->controller = $pathArray[0];
                                $method =  $config->defaulMethod;
                                $this->method = $method;
                                $isNotfind = false; //找到
                                $obj->$method();
                            } else {
                                $isNotfind = true;
                                //errshow($config->defaulMethod . lang("methodnotfound"));
                                //echo '控制器' . $pathArray[0] . '存在,但是方法' . $config->defaulMethod . '不存在，router:' . __LINE__;
                            }
                        } else if (is_dir(CONTROLLERSPATH . $pathArray[0]) || is_dir(CONTROLLERSPATH . ucfirst($pathArray[0]))) {
                            //控制器文件没有，但是有同名的的文件夹，查找是否有index类（默认）
                            if (file_exists(CONTROLLERSPATH . $pathArray[0] . DS .  $config->defaulController . ".php")) {
                                $class = $controllerBaseNameSpace . $pathArray[0] . "\\" . $config->defaulController;
                                $obj = new $class();
                                if (method_exists($obj, $config->defaulMethod)) {
                                    //echo  '控制器' . $class . '存在 ,方法：' . $config->defaulMethod . '存在';
                                    $this->directory = $pathArray[0];
                                    $this->controller = $config->defaulController;
                                    $method =  $config->defaulMethod;
                                    $this->method = $method;
                                    $isNotfind = false; //找到
                                    $obj->$method();
                                } else {
                                    $isNotfind = true;
                                    // errshow($class . lang("defaul") . lang("controllerenotfound") . "/" . __LINE__);
                                    //echo '控制器根目录存在' . $pathArray[0] . '文件夹，但是下面并没有' . $config->defaulController . '控制器，router:' . __LINE__;
                                }
                            } else {
                                $isNotfind = true;
                                // errshow($pathArray[0] . lang("controllerenotfound") . "/" . __LINE__);
                                //echo '控制器' . $pathArray[0] . '不存在，router:' . __LINE__;
                            }
                        } else {
                            //控制器类文件和目录都没有
                            $isNotfind = true;
                            //errshow($pathArray[0] . lang("controllerenotfound") . "/" . __LINE__);
                            //echo '控制器' . $pathArray[0] . '不存在，router:' . __LINE__;
                        }
                    } else {
                        //超过2个 ，可能是目录也可能是控制器 ，先看目录然后看文件
                        $dirLevel = ''; //目录层级
                        while (!empty($pathArray)) {
                            $first = array_shift($pathArray); //取出第一个
                            $dirLevel .= ($dirLevel == "") ? $first : DS . $first; //构建目录或者文件
                            $theDir = CONTROLLERSPATH . $dirLevel; //控制器下 目录或者文件
                            if (file_exists($theDir . ".php")) {
                                //控制器存在 ，看方法是否存在
                                $class = $controllerBaseNameSpace . str_replace(DS, "\\", $dirLevel);
                                //$obj = new $class();
                                //if (method_exists($obj, $pathArray[0])) {
                                $refclass = new \ReflectionClass($class);
                                $method =   $pathArray[0];
                                if ($refclass->hasMethod($method)) {
                                    // echo '控制器存在 ,方法存在';

                                    //查看方法是否是public

                                    $themethod = $refclass->getMethod($method);
                                    if ($themethod->isPublic()) {
                                        $this->directory = '';
                                        $this->controller = $dirLevel;
                                        $this->method = $method;
                                        array_shift($this->arguments); //去掉方法
                                        $this->arguments = $pathArray;
                                        $isNotfind = false;
                                        $obj = $refclass->newInstance();
                                        $obj->$method($this->arguments); //这个把剩余的参数全部传递给一个参数，如果
                                    }




                                    break; //结束查找
                                } else {
                                    $isNotfind = true;
                                    // errshow($pathArray[0] . lang("methodnotfound") . "/" . __LINE__);
                                    //echo $class . '控制器存在 ,但是' . $pathArray[0] . '方法不存在，router:' . __LINE__;
                                    break; //结束查找
                                }
                            } else if (is_dir($theDir)) {
                                //目录存在,在看第二个参数（因为已经提取了一个目录，所以原来第二个现在是第一个），如果有，就看是是否有该控制器 ，如果没有看是否有默认index
                                if (count($pathArray) > 0) {
                                    //控制器
                                    $nexfile = $pathArray[0]; //下一个
                                    $controllerInDir = $theDir . DS . ucfirst($nexfile) . ".php";
                                    $controller = $pathArray[0];
                                } else {
                                    //默认
                                    $nexfile = ''; //没有下一个
                                    $controllerInDir = $theDir . DS . $config->defaulController . ".php";
                                    $controller = $config->defaulController;
                                }
                                if (file_exists($controllerInDir)) {

                                    if (count($pathArray) <= 1) {
                                        $method = $config->defaulMethod; //默认
                                    } else {
                                        $method = $pathArray[1]; //下一个是方法（原$pathArray 第三个）
                                        $this->arguments = $pathArray;
                                        array_shift($this->arguments); //去掉控制器
                                        array_shift($this->arguments); //去掉方法
                                    }
                                    $class = $controllerBaseNameSpace . str_replace(DS, "\\", $dirLevel . "\\" .  $controller);
                                    // $obj = new $class();
                                    // if (method_exists($obj, $method)) {
                                    $refclass = new \ReflectionClass($class);
                                    if ($refclass->hasMethod($method)) {
                                        // echo  '控制器' . $class . '存在 ,方法：' . $method . '存在';

                                        $themethod = $refclass->getMethod($method);
                                        if ($themethod->isPublic()) {
                                            $this->directory = $dirLevel;
                                            $this->controller =  $controller;
                                            $this->method = $method;
                                            $obj = $refclass->newInstance();
                                            $obj->$method($this->arguments); //
                                            $isNotfind = false;
                                        }

                                        break; //结束查找
                                    } else {
                                        $isNotfind = true;
                                        // errshow($pathArray[0] . lang("methodnotfound") . "/" . __LINE__);
                                        //echo $class . '控制器存在 ,但是' . $method . '方法不存在，router:' . __LINE__;
                                        break; //结束查找
                                    }
                                } else {
                                    //控制器没有，看是否有同名文件夹，有就继续循环，否则就是真没有
                                    if (!is_dir($theDir . DS .  $nexfile)) {
                                        //
                                        $isNotfind = true;
                                        // errshow($pathArray[0] . lang("controllerenotfound") . "/" . __LINE__);
                                        // echo '控制器不存在 ，router:' . __LINE__;
                                        break; //结束查找
                                    }
                                }
                            } else {
                                $isNotfind = true;
                                ///  errshow($pathArray[0] . lang("controllerenotfound") . "/" . __LINE__);
                                //  echo '控制器不存在 ，router:' . __LINE__;
                                break; //结束查找
                            }
                        } // end while
                    } // end if
                } else {
                    //错误
                    $isNotfind = true;
                    // errshow($pathArray[0] . lang("controllerenotfound") . "/" . __LINE__);
                    //  echo 'router.php错误' . __LINE__;
                }
            }
        } else {
            //参数错误 ，系统错误
            $isNotfind = true;
            // errshow(lang("system_error") . "/" . __LINE__);
            //echo 'err_path' . __LINE__;
        }
        return  $isNotfind;
    }
    //扫描控制器文件夹下面的所有控制器 $path 控制器目录 默认CONTROLLERSPATH
    public function  scanControllerDir($path = null)
    {
        if (!isset($path) || !is_dir($path)) {
            $path = CONTROLLERSPATH;
        }
        $list = [];
        $temp_list = scandir($path);
        foreach ($temp_list as $file) {
            if ($file != ".." && $file != ".") {

                if (is_dir($path  . $file)) {
                    //子文件夹，进行递归
                    $list = array_merge($list, $this->scanControllerDir($path .  $file . DS));
                } else {
                    //根目录下的文件
                    $list[] = $path . $file;
                }
            }
        }
        return $list;
    }

    //获取所有控制器
    public function  getAllController()
    {
        $filelist = $this->scanControllerDir();
        if (isset($filelist) && is_array($filelist)) {
            foreach ($filelist as $file) {
            }
        }
    }
}
