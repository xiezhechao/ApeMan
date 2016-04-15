<?php
/**
 * Created by IntelliJ IDEA.
 * User: 谢哲超
 * Date: 15/11/6
 * Time: 09:46
 */

namespace Admin\Controller;


use Think\Controller;
use Org\Util\Input;

class PublicController extends Controller {

    public function __construct() {
        parent::__construct();
        Input::noGPC();
    }

    public function login () {
        $cookie = cookie(C('COOKIE_NAME'));
        if ($cookie) {
            $User = D('User');
            $account = $User->where("account = '%s'",$cookie['account'])->find();
            if ($account) {
                session('auth', $account);
                redirect(U('Index/index'));
            }
        }
        if($_POST) {
            $name = I('username');
            $pwd = I('password');
            $remember = I('remember');
            $User = D('User');
            $account = $User->where("account = '%s'",$name)->find();
            if ($account && md5($pwd.$account['salt']) == $account['password']) {
                session('auth', $account);
                if($remember) {
                    cookie(C('COOKIE_NAME'),array('id'=>$account['id'],'account'=>$account['account']),86400*30);
                }
                $ip = get_client_ip();
                $data = array(
                    'last_login_time'   => time(),
                    'login_count'       => intval($account['login_count']) + 1,
                    'last_login_ip'     => ip2long($ip),
                );
                if ($account && $account['id']) {
                    $User->where('id = %d',$account['id'])->save($data);
                }
                redirect(U('Index/index'));
            } else {
                $this->error("用户名或密码错误");
            }
        }
        $this->display();
    }

    public function logOut() {
        unset($_SESSION["account"]);
        unset($_SESSION[C('ADMIN_AUTH_KEY')]);
        unset($_SESSION[C('USER_AUTH_KEY')]);
        cookie(C('COOKIE_NAME'),null);
        redirect(U('Public/login'));
    }

    private function salt ($length) {
        $arr = array(0,1,2,3,4,5,6,7,8,9,'a','A','b','B','c','C','d','e','E','f','F','g','G','h','H','i','I','j','J','k','K','l','L','m','M','n','N','o','O','p','P','q','Q','r','R','s','S','t','T','u','U','v','V','w','W','x','X','y','Y','z','Z');
        $salt = '';
        $arr_len = count($arr);
        for($i = 0; $i < $length; $i++) {
            $salt .= $arr[rand(0,$arr_len)];
        }
        return $salt;
    }

    public function register () {
        $account = I('post.account');
        $password = I('post.password');
        $repassword = I('post.password');
        $nick_name = I('post.nick_name');
        $email = I('post.email');
        $street = I('post.street');
        $city = I('post.city');
        $country = I('post.country');

        if (!$account || !(strlen($account) >= 4) && !(strlen($account) <= 20)) {
            $this->ajaxReturn(array('status'=>0,'message'=>'用户名长度需要大于4且小于20'));
        }
        if (!$password || !(strlen($password) >= 6) && !(strlen($password) <= 20)) {
            $this->ajaxReturn(array('status'=>0,'message'=>'密码长度需要大于6且小于20'));
        }
        if ($password != $repassword) {
            $this->ajaxReturn(array('status'=>0,'message'=>'两次密码不一致'));
        }
        $User = D('User');
        if ($User->where("account = '%s'",array($account))->find()) {
            $this->ajaxReturn(array('status'=>0,'message'=>'用户名已存在'));
        }
        $salt = $this->salt(5);
        $data = array(
            'account'   => $account,
            'password'  => md5($password.$salt),
            'salt'      => $salt,
            'nick_name' => $nick_name,
            'email'     => $email,
            'street'    => $street,
            'city'      => $city,
            'country'   => $country,
            'create_time' => time(),
        );
        if (!$User->add($data)) {
            $this->ajaxReturn(array('status'=>0,'message'=>'注册暂时关闭，请联系管理员'));
        }
        $this->ajaxReturn(array('status'=>1,'message'=>'恭喜您，注册成功'));
    }

    public function lockScreen () {
        $this->display();
    }


}