<?php
/**
 * Created by IntelliJ IDEA.
 * User: xiezhechao
 * Date: 17/2/14
 * Time: 11:37
 */

namespace Admin\Controller;


use Think\Page;
use Think\PageBackUp;

class EmployeeController extends CommonController {

    public function index() {
        $params = array();
        $p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
        if($_GET['listrows']){
            $listrows = intval($_GET['listrows']);
            $params[] = "listrows=" . intval($_GET['listrows']);
        }else{
            $listrows = 10;
            $params['listrows'] = $listrows;
        }

        $m_user = M("user","company_");
        $list = $m_user->page($p.",".$listrows)->select();
        $count = $m_user->count();
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

        $this->assign('page', $Page->show());
        $this->assign("userList",$list);
        $this->display();
    }
}