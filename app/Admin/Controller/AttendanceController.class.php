<?php
/**
 * Created by IntelliJ IDEA.
 * User: xiezhechao
 * Date: 16/9/7
 * Time: 09:13
 */

namespace Admin\Controller;


use Think\Page;

class AttendanceController extends CommonController{

    private $work_time = "09:00";

    private $time_from_work = "17:30";

    private $over_time_start_time = "18:30";

    public function index() {
        $params = array();
        $where = array();
        $p = isset($_GET["p"]) ? $_GET["p"] : 1;
        if ($_GET["listrows"]) {
            $listrows = intval($_GET["listrows"]);
            $params['listrows'] = intval($_GET["listrows"]);
        } else {
            $listrows = 10;
            $params["listrows"] = 10;
        }

        $from = $_REQUEST['from'];
        if ($from) {
            $where[] = "dim_date.date_id >= ".date('Ymd',strtotime($from));
            $params['from'] = $from;
        }
        $to = $_REQUEST['to'];
        if ($to) {
            $where[] = "dim_date.date_id <= ".date('Ymd',strtotime($to));
            $params['to'] = $to;
        }
        $user = $_REQUEST['user'];
        $userName = $_REQUEST['userName'];
        if ($user) {
            $where[] = "company_user.number = ".$user;
            $params['user'] = $user;
        } elseif ($userName) {
            $where[] = "company_user.name LIKE '%".$userName."%'";
            $params['userName'] = $userName;
        }

        if (!$from && !$to) {
            $where[] = "dim_date.date_id >= ".date("Ym01", strtotime("last month"));
            $where[] = "dim_date.date_id <= ".date("Ymt", strtotime("last month"));
            $_REQUEST['from'] = date("Y-m-01", strtotime("last month"));
            $_REQUEST['to'] = date("Y-m-t", strtotime("last month"));
        }

        $where[] = 'hiredate <= date_id';
        $where[] = 'departure_date = 0 OR departure_date >= date_id';

        $reback = array();
        $reback['from'] = $_REQUEST['from'];
        $reback['to'] = $_REQUEST['to'];
        $reback['user'] = $_REQUEST['user'];
        $reback['userName'] = $_REQUEST['userName'];
        $this->assign('reback',$reback);

        $u = M('user','company_');
        $users = $u->select();
        $this->assign('users',$users);

        $m_dim_date = M("date", "dim_");
        $list = $m_dim_date->join("company_user")->join("company_attendance ON company_user.number = company_attendance.number AND dim_date.date_id = company_attendance.date","LEFT")->where($where)->field("dim_date.date_id AS `date`, dim_date.week_name, company_user.number, company_user.name, company_user.wage, company_attendance.start_time, company_attendance.end_time, dim_date.is_holiday, overtime_hours, holiday_name")->order("dim_date.date_id, company_user.number ASC")->page($p.",".$listrows)->select();
        $count = $m_dim_date->join("company_user")->join("company_attendance ON company_user.number = company_attendance.number AND dim_date.date_id = company_attendance.date","LEFT")->where($where)->count();
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
        $this->assign("list",$list);

        $total_overtime_hours = $m_dim_date->join("company_user")->join("company_attendance ON company_user.number = company_attendance.number AND dim_date.date_id = company_attendance.date","LEFT")->where($where)->sum("overtime_hours");
        $this->assign("total_overtime_hours", $total_overtime_hours);

        $late_where = array_merge($where,
            array(
                "dim_date.is_holiday = 0",
                "start_time > UNIX_TIMESTAMP(CONCAT(dim_date.date_name, ' ".$this->work_time."'))" ,
            ));
        $total_late_count = $m_dim_date->join("company_user")->join("company_attendance ON company_user.number = company_attendance.number AND dim_date.date_id = company_attendance.date","LEFT")->where($late_where)->count();
        $this->assign("total_late_count", $total_late_count);
        $this->display("index");
    }

    public function importExcel() {

        set_time_limit(0);
        $name = 'excel';
        $dir = 'data/uploads/';

        if ($_FILES[$name]["error"] > 0) {
            $this->error(fileErrorInfo($_FILES[$name]["error"]));
        } else {
            move_uploaded_file($_FILES[$name]["tmp_name"],
                $dir . $_FILES[$name]["name"]);
        }

        $filePath = $dir . $_FILES[$name]["name"];

        $this->loadExcel2Data($filePath);
        $this->reloadData();

        $this->success("考勤导入成功");
    }

    //从EXCEL导入数据库临时表
    private function loadExcel2Data ($path) {
        $data = $this->loadExcel($path,1);
        $m = M();
        $m->execute("TRUNCATE `company_attendance_log`");
        $log = M('attendance_log','company_');
        $log->addAll($data);
    }

    //从临时表到正式表
    private function reloadData () {
        $m = M();
        $m->execute("TRUNCATE `company_attendance`");
        $sql = "INSERT INTO company_attendance (number, start_time, end_time, `date`, `overtime_hours`)
SELECT t1.number,
CASE WHEN t1.time = t2.time AND 12 < CAST(FROM_UNIXTIME(t1.time, '%H') AS SIGNED) THEN 0
ELSE t1.time
END AS start_time,
CASE WHEN t2.time = t1.time AND 12 > CAST(FROM_UNIXTIME(t2.time, '%H') AS SIGNED) THEN 0
ELSE t2.time
END AS end_time,
t1.date,
CASE
WHEN t3.`is_holiday` = 0 AND t2.time > UNIX_TIMESTAMP(CONCAT(t3.date_name, ' ".$this->over_time_start_time."'))
THEN ROUND((t2.time - UNIX_TIMESTAMP(CONCAT(t3.date_name, ' .".$this->time_from_work."'))) / 60 / 60, 1)
WHEN t3.`is_holiday` = 1 AND t1.time > 0 AND t2.time > 0
THEN ROUND((t2.time - t1.time) / 60 / 60, 1)
ELSE 0
END AS overtime_hours
FROM (
SELECT number, `time`, `date` FROM `company_attendance_log` GROUP BY number, `DATE` ORDER BY `TIME` ASC LIMIT 100000
) t1 LEFT JOIN (
SELECT number, `time`, `date` FROM
(SELECT number, `time`, `date` FROM `company_attendance_log` ORDER BY `TIME` DESC) tmp GROUP BY number, `DATE` ORDER BY `TIME` ASC
) t2 ON t1.number = t2.number AND t1.date = t2.date JOIN dim_date t3 ON t1.date = t3.date_id";
        $m->execute($sql);
    }


    //读取EXCEL
    private function loadExcel ($path, $startRow = 2) {
        $list = array();

        Vendor('PHPExcel.PHPExcel');
        $PHPExcel = new \PHPExcel();
        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($path)) {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($path)) {
                $this->success("不是有效的EXCEL表格");exit;
            }
        }
        $PHPExcel = $PHPReader->load($path);
        $currentSheet = $PHPExcel->getSheet(0); /* * 读取excel文件中的第一个工作表 */
        $allColumn = $currentSheet->getHighestColumn();
        /**取得最大的列号*/
        $allRow = $currentSheet->getHighestRow(); /* * 取得一共有多少行 */
        \PHPExcel_Cell::columnIndexFromString(); //字母列转换为数字列 如:AA变为27

        for ($i = $startRow; $i <= $allRow; $i++) {
            $tmpTime = \PHPExcel_Shared_Date::ExcelToPHP($currentSheet->getCellByColumnAndRow(1, $i)->getValue()) - 28800;
            $list[] = array(
                'number' => $currentSheet->getCellByColumnAndRow(0, $i)->getValue(),
                'time'   => $tmpTime,
                'date'   => date('Ymd', $tmpTime),
            );
        }
        return $list;
    }

}