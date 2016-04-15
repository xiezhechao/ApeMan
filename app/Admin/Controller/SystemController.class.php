<?php
/**
 * Created by IntelliJ IDEA.
 * User: 谢哲超
 * Date: 15/11/8
 * Time: 19:57
 */

namespace Admin\Controller;


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
}