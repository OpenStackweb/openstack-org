<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 9/12/13
 * Time: 10:51 AM
 * To change this template use File | Settings | File Templates.
 */

class DateTimeUtils {

    public static function getCurrentDate(){
        $current_date =  date("Y-m-d");
        if(isset($_COOKIE["user_date"])){
            $current_date= $_COOKIE["user_date"];
            $current_date = Convert::raw2sql($current_date);
        }
        return $current_date;
    }

    public static  function getMonthShortName($input_date){
        $date = date_create_from_format('Y-m-d', $input_date);
        $monthNumber = date_format($date,"m");
        $time = mktime(0, 0, 0, $monthNumber);
        $name = strftime("%b", $time);
        return $name;
    }

    public static function getDay($input_date){
        $date = date_create_from_format('Y-m-d', $input_date);
        return date_format($date,"d");
    }

    public static function getYear($input_date){
        $date = date_create_from_format('Y-m-d', $input_date);
        return date_format($date,"Y");
    }

    public static function getDayDiff($input_date_l,$input_date_r){
        $date_1 = date_create_from_format('Y-m-d', $input_date_l);
        $date_2 = date_create_from_format('Y-m-d', $input_date_r);
        $interval = date_diff($date_1, $date_2);
        return $interval->format('%a')+1;
    }
}