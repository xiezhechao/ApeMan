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