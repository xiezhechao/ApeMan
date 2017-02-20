<?php
/**
 * Created by IntelliJ IDEA.
 * User: 谢哲超
 * Date: 15/11/6
 * Time: 09:41
 */

namespace Admin\Controller;


use Think\Auth;
use Think\Controller;
use Think\PageBackUp;
use Org\Util\Input;

class CommonController extends Controller {

    protected $_name;
    protected $_mod;
    public $_title = array();

    public function __construct() {
        parent::__construct();
        if (!hasLogin()) {
            redirect(U('Public/login'));
        }
        $sidebar = sidebar();
        $page_header = pageHeader();
        Input::noGPC();
        $this->_auth();
        $this->_name = CONTROLLER_NAME;
        $this->assign('sidebar',$sidebar);
        $this->assign('page_header',$page_header);
    }

    protected function _auth () {
        $account = session('auth');
        $auth_name = $account['account'];
        $super_admin = explode(',', C('ADMINISTRATOR'));
        if (!in_array($auth_name, $super_admin)) {
            $auth = new Auth();
            $rule = MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME;
            if (!$auth->check($rule, $account['id'])) {
                header("HTTP/1.0 404 Not Found");
                $this->display('Public/404');
                exit();
            }
        }
    }

    public function index() {
        try {
            $map = $this->_search();
            $mod = M($this->_name);
            !empty($mod) && $this->_list($mod, $map);
        } catch (exception $e) {}
        $this->display();
    }

    protected function _search() {
        $map = array();
        try {
            $mod = M($this->_name);
            foreach ($mod->getDbFields() as $key => $val) {
                if (substr($key, 0, 1) == '_') {
                    continue;
                }
                if ($_REQUEST[$val] != '') {
                    $map[$val] = $_REQUEST[$val];
                }
            }
        } catch (exception $e) {}
        return $map;
    }

    protected function _list($model = array(), $map = array(), $sort_by = '', $order_by = '', $fields = '*', $page_size = 10) {
        if (!isset($model)) {
            return array();
        }
        $pk = $model->getPk();
        $sort = '';
        if ($_REQUEST['sort']) {
            $sort = trim($_REQUEST['sort']);
        } else {
            if (!empty($sort_by)) {
                $sort = $sort_by;
            } else {
                $sort = $pk;
            }
            if ($_REQUEST['order']) {
                $order = trim($_REQUEST['order']);
            } else {
                if (!empty($order_by)) {
                    $order = $order_by;
                } else {
                    $order = 'DESC';
                }
            }
        }
        $count = $model->where($map)->count();
        $page = new PageBackUp($count,$page_size);
        $select = $model->field($fields)->where($map)->order($sort . ' ' . $order);
        if ($page_size) {
            $select->limit($page->firstRow . ',' . $page->listRows);
            $this->assign("page", $page);
        }
        $list = $select->select();
        $p = $_REQUEST['p'] ? intval($_REQUEST['p']) : 1;
        $this->assign('p', $p);
        $this->assign('list', $list);
        return $list;
    }

    public function curl_post($url, $fields = array()) {
        // 初始化一个 cURL 对象
        $ch = curl_init();
        // 设置你需要抓取的URL
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_INFILESIZE,2);
        // 运行cURL，请求网页
        $return_data = curl_exec($ch);
        // 关闭URL请求
        curl_close($ch);
        return $return_data;
    }

}