<?php
return array(
    /*多语言*/
    'LANG_SWITCH_ON'    => true,   // 开启语言包功能
    'LANG_AUTO_DETECT'  => true, // 自动侦测语言 开启多语言功能后有效
    'LANG_LIST'         => 'zh-cn,en-us', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE'      => 'l', // 默认语言切换变量

    /*默认控制器*/
    'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', // 默认操作名称

    /* 错误设置 */
    'ERROR_MESSAGE'         =>  '页面错误！请稍后再试～',//错误显示信息,非调试模式有效
    'ERROR_PAGE'            =>  'app/Admin/View/404.html',	// 错误定向页面
    'SHOW_ERROR_MSG'        =>  false,    // 显示错误信息
    'TRACE_MAX_RECORD'      =>  100,    // 每个级别的错误信息 最大记录数


    // 加载扩展配置文件
    'LOAD_EXT_CONFIG' => array('db','url','auth','other'),

    //表单令牌
    'TOKEN_ON'      =>    true,        // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    '__hash__',   // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'    =>    'md5',        //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    true,         //令牌验证出错后是否重置令牌 默认为true


    //调试信息
    'SHOW_PAGE_TRACE' => false,// 显示页面Trace信息

    /* 公共文件夹设置 */
    'TMPL_PARSE_STRING'  =>array(
        '__PUBLIC__' => __ROOT__.'/static',          // 更改默认的/Public 替换规则
        '__JS__'     => __ROOT__.'/static/js/',      // 增加新的JS类库路径替换规则
        '__UPLOAD__' => __ROOT__.'/static/uploads/', // 增加新的上传路径替换规则
    ),
);