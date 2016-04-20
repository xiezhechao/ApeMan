<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }
    public function test () {
        echo 1-1;
        echo 2;
        $this->display();
    }
}