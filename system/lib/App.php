<?php

namespace system\lib;

class App
{
    /**
     * 路由
     * @var  class router
     */
    public $version = 'v0.1';


    public function __construct()
    {
        //var_dump($_SERVER);
        // error_reporting(0); //屏蔽程序中的错误
        // set_error_handler(array($this, "error_handler")); //自定义错误

    }
    /**
     * 运行app ，整个程序的开始
     * @var  class router
     */
    public function run()
    {

       
        BaseService::debug()->start("配置加载");
        $config = BaseService::config();
        BaseService::debug()->end("配置加载");
        BaseService::debug()->start("路由控制器与视图总");
        BaseService::router()->init();//router() 这个函数差不多花费2-3ms
        BaseService::debug()->end("路由控制器与视图总");
        BaseService::debug()->start("语言加载");
        $lang = BaseService::Language(); //加载对应语言文件 ，router可能有改变语言，所以要在路由初始化后init
        BaseService::debug()->end("语言加载");

        date_default_timezone_set($config->appTimezone); //设置时区

    }

    public function error_handler($error_level, $error_message, $file, $line)
    {
        $EXIT = FALSE;
        switch ($error_level) {
                //提醒级别
            case E_NOTICE:
            case E_USER_NOTICE:
                $error_type = 'Notice';
                break;
                //警告级别
            case E_WARNING:
            case E_USER_WARNING:
                $error_type = 'warning';
                break;
                //错误级别
            case E_ERROR:
            case E_USER_ERROR:
                $error_type = 'Fatal Error';
                $EXIT = TRUE;
                break;
                //其他未知错误
            default:
                $error_type = 'Unknown';
                $EXIT = TRUE;
                break;
        }
        //直接打印错误信息，也可以写文件，写数据库，反正错误信息都在这，任你发落
        printf("<font color='#FF0000'><b>%s</b></font>:%s in<b>%s</b> on line <b>%d</b><br>\n", $error_type, $error_message, $file, $line);
        if ($EXIT == true) {
            //如果错误影响到程序的正常执行，跳转到友好的错误提示页面
            // echo '<script>location = "err.html";</scrpit>';
        }
    }
}
