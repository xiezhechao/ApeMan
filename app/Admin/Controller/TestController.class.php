<?php
/**
 * Created by IntelliJ IDEA.
 * User: xiezhechao
 * Date: 16/8/31
 * Time: 19:31
 */

namespace Admin\Controller;


class TestController extends CommonController {

    public function index() {
        $this->display();
    }

    public function receive () {
        ob_start();
        echo 222111;
        ob_end_flush();
        exit;
    }

    public function test() {
        $this->display();
    }

    public function a() {
        $this->assign('var',3);
        $this->display();
    }


}