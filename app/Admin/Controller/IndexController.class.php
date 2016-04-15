<?php
namespace Admin\Controller;


use Think\Controller;

class IndexController extends CommonController {

    public function index(){
        $sysInfo = array(
            'serverDomain'      => $_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ] ',
            'systemOS'          => PHP_OS,
            'webServer'         => $_SERVER['SERVER_SOFTWARE'],
            'phpVersion'        => PHP_VERSION,
            'mysqlVersion'      => mysql_get_server_info(),
            'uploadMaxFileSize' => ini_get('upload_max_filesize'),
            'maxExecutionTime'  => ini_get('max_execution_time').'秒',
            'safe_mode'         => ini_get('safe_mode') ? '是' : '否',
            'zlib'              => function_exists('gzclose') ? '是' : '否',
            'curl'              => function_exists('curl_getinfo') ? '是' : '否',
            'timezone'          => function_exists('date_default_timezone_get') ? date_default_timezone_get() : '否',

        );
        $this->assign('sysInfo',$sysInfo);
        $this->display();
    }

    public function test() {
        echo 'Test11';
    }
}