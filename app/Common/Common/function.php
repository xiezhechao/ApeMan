<?php
/**
 * Created by IntelliJ IDEA.
 * User: xiezhechao
 * Date: 16/9/10
 * Time: 11:11
 */

/**
 * @param $timestamp 时间戳
 */
function isWeekend ($timestamp) {
    $week = array(0 => true ,6 =>true);
    return $week[date('w',$timestamp)];
}

function getFirstDayForMonth ($stimestamp) {
    return date('Ym01', $stimestamp);
}

function getLastDayForMonth ($timestamp) {
    return date('Ymd', strtotime(getFirstDayForMonth($timestamp)." +1 month -1 day"));
}

function formatNum ($number) {
    $number = floatval($number);
    if ($number == floor($number)) {
        return floor($number);
    }
    return $number;
}

function fileErrorInfo ($error_code) {
    switch ($error_code) {
        case 1 :
            return "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。";
        case 2 :
            return "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。";
        case 3 :
            return "文件只有部分被上传。";
        case 4 :
            return "没有文件被上传。";
        case 6 :
            return "找不到临时文件夹。PHP 4.3.10 和 PHP 5.0.3 引进。";
        case 7 :
            return "文件写入失败。";
    }
}
