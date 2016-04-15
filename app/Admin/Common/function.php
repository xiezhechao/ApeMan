<?php
/**
 * Created by IntelliJ IDEA.
 * User: 谢哲超
 * Date: 15/11/7
 * Time: 03:05
 */

function hasLogin () {
    $cookie = cookie(C('COOKIE_NAME'));
    if ($cookie) {
        $User = D('User');
        $auth = $User->where("account = '%s'",$cookie['account'])->find();
        if ($auth) {
            session('auth',$auth);
            return true;
        } else {
            return false;
        }
    }
    return isset($_SESSION['auth']);
}

//左侧主菜单
function sidebar () {
    $rule = MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME;
    $parentsId = sidebarChooseParents();
    $sidebar = '';
    $menu = menu(0,1);
    for ($i = 0, $len = count($menu); $i < $len; $i++) {
        $vo = $menu[$i];
        if ($vo['is_header']) {
            $sidebar .= '<li class="heading"><h3 class="uppercase">'.$vo['title'].'</h3></li>';
        } else {
            $first_class = array();
            $first_class[] = $i == 0 ? 'start' : '';
            $first_class[] = $i == $len - 1 ? 'last' : '';
            $first_class[] = in_array($vo['id'],$parentsId) ? 'active open' : '';
            $first_class = join(' ',$first_class);
            $sec_class = array();
            $sec_class[] = count(menu($vo['id'],$vo['menu_type'])) > 0 ? 'arrow' : '';
            $sec_class[] = $rule == $vo['name'] ? 'open' : '';
            $sec_class = join(' ',$sec_class);
            $select_span = $rule == $vo['name'] ? '<span class="selected"></span>' : '';
            $href = $vo['name'] ? U($vo['name']) : 'javascript:;';
            $sidebar .= '<li class="'.$first_class.'"><a href="'.$href.'"><i class="'.$vo['icon'].'"></i><span class="title">'.$vo['title'].'</span>'.$select_span.'<span class="'.$sec_class.'"></span></a>';
            $sidebar = subMenu($vo['id'],$vo['menu_type'],$parentsId, $sidebar);
            $sidebar .= '</li>';
        }
    }
    return $sidebar;
}

//左侧子菜单
function subMenu ($pid, $menu_type, $parentsId, $sidebar) {
    $menu = menu($pid,$menu_type,$sidebar);
    $len = count($menu);
    if ($len > 0) {
        $sidebar .= '<ul class="sub-menu">';
        for ($i = 0; $i < $len; $i++) {
            $vo = $menu[$i];
            $active_class = in_array($vo['id'], $parentsId) ? 'active' : '';
            $href = $vo['name'] ? U($vo['name']) : 'javascript:;';
            $sub = menu($vo['id'],$vo['menu_type']);
            $arrow = count($sub) > 0 ? 'arrow' : '';
            $open = in_array($vo['id'], $parentsId) ? ' open ' : '';
            $icon = $vo['icon'];
            $sidebar .= '<li class="'.$active_class.'"><a href="'.$href.'"><i class="'.$icon.'"></i>'.$vo['title'].'<span class="'.$arrow.$open.'"></span></a>';
            $sidebar = subMenu($vo['id'] ,$menu_type, $parentsId ,$sidebar);
            $sidebar .= '</li>';
        }
        $sidebar .= '</ul>';
    }
    return $sidebar;
}

//获取可访问规则
function menu ($pid, $menu_type) {
    $rules = array();
    $admin_list = explode(',', C('ADMINISTRATOR'));
    $map = array(
        'pid'           => array('EQ', $pid),
        'menu_type'     => array('EQ', $menu_type),
    );
    if(!in_array(session('auth')['account'], $admin_list)) {
        $auth = new \Think\Auth();
        $uid = session('auth')['id'];
        $groups = $auth->getGroups($uid);
        foreach ($groups as $k => $v) {
            $rule_all_id = $v['rules'];
            if (!empty($groups)) {
                $map['id'] = array('IN',$rule_all_id);
                $tmp =  M('auth_rule')->where($map)->order('sort ASC')->select();
                $rules = array_merge($rules, $tmp);
            }
        }
        return $rules;
    } else {
        return  M('auth_rule')->where($map)->order('sort ASC')->select();
    }
}

function sidebarChooseParents () {
    $parentsId = array();
    $map = array(
        'name'  => array('EQ', MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME)
    );
    $rule = M('auth_rule')->where($map)->find();
    $parentsId = findParentId($rule['pid'], $parentsId);
    $parentsId[] = $rule['id'];
    return $parentsId;
}

function findParentId($pid, $parentsId) {
    if ($pid == 0) {
        return $parentsId;
    }
    $parentsId[] = $pid;
    $rule = M('auth_rule')->where('id = %d', $pid)->find();
    return findParentId($rule['pid'], $parentsId);
}

function actionTitle() {
    $map = array(
        'name'  => array('EQ', MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME)
    );
    $rule = M('auth_rule')->where($map)->field('id, name, title, pid')->find();
    return $rule['title'];
}

function pageHeader () {
    $map = array(
        'name'  => array('EQ', MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME)
    );
    $rule = M('auth_rule')->where($map)->field('id, name, title, pid')->find();
    if ($rule['id'] == 1) {
        return array();
    }
    $menu = array();
    $menu[] = $rule;
    $menu = pageHeaderExt($rule['pid'],$menu);
    foreach ($menu as $k => $v) {
        if ($v['is_header']) {
            unset($menu[$k]);
            continue;
        }
        if (empty($v['name'])) {
            $menu[$k]['name'] = 'javascript:;';
        } else {
            $menu[$k]['name'] = U($v['name']);
        }
    }
    return array_reverse($menu);
}

function pageHeaderExt ($pid, $menu) {
    if ($pid == 0 || $pid == 1) {
        return $menu;
    }
    $rule = M('auth_rule')->where('id = %d', $pid)->field('id, name, title, pid')->find();
    $menu[] = $rule;
    $menu = pageHeaderExt($rule['pid'], $menu);
    return $menu;
}
