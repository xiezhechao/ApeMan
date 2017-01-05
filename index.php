<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);
define('BUILD_DIR_SECURE', false);
APP_DEBUG && define('DB_FIELD_CACHE',false);
APP_DEBUG && define('HTML_CACHE_ON',false);

//定义系统目录
define('SYS_PATH', './');
// 定义应用目录
define('APP_PATH', SYS_PATH . 'app/');
//定义数据目录
define('SYS_DATA_PATH', SYS_PATH . 'data/');
//定义扩展目录
define('EXTEND_PATH', APP_PATH . 'Extend/');
//定义配置文件目录
define('CONF_PATH', SYS_DATA_PATH . 'config/');
//定义运行时目录
define('RUNTIME_PATH', SYS_DATA_PATH . 'runtime/');
//框架目录
define('THINK_PATH', SYS_DATA_PATH . 'core/');
//项目目录
define('ROOT_PATH', dirname(__FILE__));
// 引入ThinkPHP入口文件
require THINK_PATH.'ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单
