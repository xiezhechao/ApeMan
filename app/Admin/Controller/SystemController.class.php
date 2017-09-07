<?php
/**
 * Created by IntelliJ IDEA.
 * User: 谢哲超
 * Date: 15/11/8
 * Time: 19:57
 */

namespace Admin\Controller;

use Think\Page;
use Think\PageBackUp;

class SystemController extends CommonController {

    public function core () {
        $this->display();
    }

    public function authRuleList () {
        $list = array();
        $menuType = 1;
        $list = $this->subMenu(0,$menuType,$list);
        $this->assign('list', $list);
        $this->display();
    }

    private function subMenu($pid, $menuType, $list) {
        $menu = menu($pid,$menuType);
        if (count($menu) > 0) {
            for ($i = 0, $len = count($menu); $i < $len; $i++) {
                $vo = $menu[$i];
                $list[] = $vo;
                $list = $this->subMenu($vo['id'],$menuType,$list);
            }
        }
        return $list;
    }

    public function configs () {
        $params = array();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
        if($_GET['listrows']){
            $listrows = intval($_GET['listrows']);
            $params[] = "listrows=" . intval($_GET['listrows']);
        }else{
            $listrows = 10;
            $params['listrows'] = $listrows;
        }

        $m_config = M("configs","sys_");
        $list = $m_config->page($p.",".$listrows)->select();
        $count = $m_config->count();
        $Page = new Page($count, $listrows);
        if (!empty($_GET['by'])) {
            $params[] = 'by='.trim($_GET['by']);
        }
        if ($_GET['desc_order']) {
            $params[] = "desc_order=" . trim($_GET['desc_order']);
        } elseif($_GET['asc_order']){
            $params[] = "asc_order=" . trim($_GET['asc_order']);
        }
        $Page->parameter = $params;

        foreach ($list as $k => $v) {
            if (is_file($v['path'])) {
                $list[$k]['status'] = 1;
            } else {
                $list[$k]['status'] = 0;
            }
        }

        $this->assign('page', $Page->show());
        $this->assign("configList",$list);
        $this->display();
    }

    public function writeToBat () {
        $path = 'D:\Software\IDE\sublime text 2\sublime.bat';
        $fs = fopen($path,'w');
        $config_path = $_POST['path'];
        $content = '@echo off '.PHP_EOL.'start "" "D:\Software\IDE\sublime text 2\sublime_text.exe" '.$config_path;
        $r = fwrite($fs,$content);
        fclose($fs);
        echo json_encode($r);
    }
}