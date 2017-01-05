<?php
/**
 * Created by IntelliJ IDEA.
 * User: xiezhechao
 * Date: 16/9/7
 * Time: 09:13
 */

namespace Admin\Controller;


class AttendanceController extends CommonController{

    public function index() {

        $where = array();
        $from = $_POST['from'];
        if ($from) {
            $where[] = "date_id >= ".date('Ymd',strtotime($from));
        }
        $to = $_POST['to'];
        if ($to) {
            $where[] = "date_id <= ".date('Ymd',strtotime($to));
        }
        $user = $_POST['user'];
        $userName = $_POST['userName'];
        if ($user) {
            $where[] = "t2.number = ".$user;
        } elseif ($userName) {
            $where[] = "t2.name LIKE '%".$userName."%'";
        }

        if (!$from && !$to) {
            $where[] = "date_id >= ".getFirstDayForMonth(strtotime('2016-06-01'));
            $where[] = "date_id <= ".getLastDayForMonth(time());
        }

        $where[] = 'hiredate <= date_id';
        $where[] = 'departure_date >= date_id';

        if ($where) {
            $where = ' WHERE '.join(' AND ',$where);
        } else {
            $where = '';
        }

        $reback = array();
        $reback['from'] = $_POST['from'];
        $reback['to'] = $_POST['to'];
        $reback['user'] = $_POST['user'];
        $reback['userName'] = $_POST['userName'];
        $this->assign('reback',$reback);

        $u = M('user','company_');
        $users = $u->select();
        $this->assign('users',$users);

        $m = M();
        $sql = "SELECT date_id AS `date`, week_name, t.number, `name`, wage, start_time, end_time, is_holiday FROM (SELECT t1.date_id, t1.week_name, t2.number, t2.name, t2.wage, t1.`is_holiday`, t2.`hiredate`, t2.`departure_date` FROM dim_date AS t1, company_user AS t2 ".$where."
) t LEFT JOIN company_attendance AS t3 ON t.number = t3.number AND t.date_id = t3.date
ORDER BY t.date_id, t.number ASC";
        $list = $m->query($sql);
        $total = array('hours'=>0);
        $late = 0;
        $deductions = 0;
        foreach ($list as $k => $v) {
            $status = '';
            if ($v['is_holiday']) {
                $status .= '公休';
                if ($v['start_time'] > 0 && $v['end_time'] > 0) {
                    $h = ($v['end_time'] - $v['start_time']) / 60 / 60;
                    $list[$k]['overtimeHours'] = round($h,1) . '[节假日]';
                    $total['hours'] += $list[$k]['overtimeHours'];
                    $status .= ',加班';
                }
            } else {
                $time = intval(date('H',$v['end_time']));

                if ($time >= 19 || ($time == 18 && intval(date('i',$v['end_time'])) >= 30)) {
                    $status .= '加班';
                    $s = strtotime($v['date'].' 17:30:00');
                    $h = ($v['end_time'] - $s) / 60 / 60 ;
                    $list[$k]['overtimeHours'] = round($h,1);
                    $total['hours'] += $list[$k]['overtimeHours'];
                }
                $morning = strtotime($v['date'].' 9:01:00');
                if ($v['start_time'] > 0) {
                    $t = $v['start_time'] - $morning;
                    if ($t > 0) {
                        $status .= ' 迟到';
                        if ($t < 60 * 10) {
                            $deductions += 5;
                        } elseif ($t < 15 * 60) {
                            $deductions += 10;
                        } elseif ($t < 20 * 60) {
                            $deductions += 15;
                        } elseif ($t < 30 * 60) {
                            $deductions += 20;
                        } else {
                            $deductions += $v['wage'] / 21.75 / 2;
                        }
                        $late++;
                    }
                }
                if (!$v['start_time'] && !$v['end_time']) {
                    $status = ' 旷工';
                }
            }
            $list[$k]['status'] = $status;
            $list[$k]['date'] = date('Y/m/d',strtotime($v['date']));
        }
        $this->assign('late',$late);
        $this->assign('dedu',round($deductions,2));
        $this->assign("total",$total);
        $this->assign("list",$list);
        $this->display();
    }

    public function importExcel() {

        set_time_limit(0);
        $name = 'excel';
        $dir = 'data/uploads/';

        if ($_FILES[$name]["error"] > 0) {
            echo "Return Code: " . $_FILES[$name]["error"] . "<br />";
        } else {
            move_uploaded_file($_FILES[$name]["tmp_name"],
                $dir . $_FILES[$name]["name"]);
        }

        $filePath = $dir . $_FILES[$name]["name"];

        $this->loadExcel2Data($filePath);
        $this->reloadData();

        $this->display('index');
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
        $sql = "INSERT INTO company_attendance (number, start_time, end_time, `date`)
SELECT t1.number,
CASE WHEN t1.time = t2.time AND 12 < CAST(FROM_UNIXTIME(t1.time, '%H') AS SIGNED) THEN 0
ELSE t1.time
END AS start_time,
CASE WHEN t2.time = t1.time AND 12 > CAST(FROM_UNIXTIME(t2.time, '%H') AS SIGNED) THEN 0
ELSE t2.time
END AS end_time,
t1.date FROM (
SELECT number, `time`, `date` FROM `company_attendance_log` GROUP BY number, `DATE` ORDER BY `TIME` ASC
) t1 LEFT JOIN (
SELECT number, `time`, `date` FROM
(SELECT number, `time`, `date` FROM `company_attendance_log` ORDER BY `TIME` DESC) tmp GROUP BY number, `DATE` ORDER BY `TIME` ASC
) t2 ON t1.number = t2.number AND t1.date = t2.date";
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