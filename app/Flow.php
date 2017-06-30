<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Flow extends Model
{
    /**
     * 获取某个端口从指定时间区间的流量值
     * @param $port
     * @param $startTime
     * @param $endTime
     * @return mixed
     */
    public static function getFlowFromStartToEnd($port, $node=null, $startTime, $endTime){
        if ($node) {
            $flow = DB::table('flows')->where([
                ['port', '=', $port],
                ['date_time', '>=', $startTime],
                ['date_time', '<=', $endTime]
            ])->where('address',$node)->sum('flow');
            return $flow;
        }else{
            $flow = DB::table('flows')->where([
                ['port', '=', $port],
                ['date_time', '>=', $startTime],
                ['date_time', '<=', $endTime]
            ])->sum('flow');
            return $flow;
        }
        
    }

    public static function getAllFlowRank($startTime, $endTime, $node=null){
        if ($node) {
            $query = [['date_time', '>=', $startTime],['date_time', '<=', $endTime],['address', $node]];
        }else{
            $query = [['date_time', '>=', $startTime],['date_time', '<=', $endTime]];
        }
        $flow = DB::table('flows')
            ->select('port', DB::raw('SUM(flow) as flow'))
            ->where($query)
            ->groupBy('port')
            ->get();
        return $flow;
    }

    /**
     * 获取某个端口流量总值
     * @param $port
     * @return mixed
     */
    public static function getTotalFlow($port, $node=null){
        if ($node == null) {
            return DB::table('flows')->where('port',$port)->sum('flow');
        }else{
            return DB::table('flows')->where('port',$port)->where('address', $node)->sum('flow');
        }
    }

    public static function getHourFlow($port, $node=null,$startTime,$endTime,$hour){
        $arrayHourFlow = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $startSec = strtotime($startTime);
        $oneSchedule = 300;
        if ($node) {
            $queryArray = DB::table('flows')->where([
                ['port', '=', $port],
                ['date_time', '>=', $startTime],
                ['date_time', '<=', $endTime],
                ['address', $node]
            ])->get();    
        }else{
            $queryArray = DB::table('flows')->where([
                ['port', '=', $port],
                ['date_time', '>=', $startTime],
                ['date_time', '<=', $endTime]
            ])->get();
        }
        if (!empty($queryArray)) {
            foreach ($queryArray as $item) {
                if ($item->time < $startSec + $oneSchedule) {
                    $arrayHourFlow[0] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 2) {
                    $arrayHourFlow[1] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 3) {
                    $arrayHourFlow[2] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 4) {
                    $arrayHourFlow[3] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 5) {
                    $arrayHourFlow[4] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 6) {
                    $arrayHourFlow[5] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 7) {
                    $arrayHourFlow[6] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 8) {
                    $arrayHourFlow[7] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 9) {
                    $arrayHourFlow[8] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 10) {
                    $arrayHourFlow[9] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 11) {
                    $arrayHourFlow[10] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 12) {
                    $arrayHourFlow[11] += round($item->flow / 1024, 2);
                    continue;
                }
            }
        }
        $result = [
            'flow' => $arrayHourFlow,
            'labels' => [$hour . ':00', "", "", "", $hour . ':20', "", "", "", $hour . ':40', "", "", ""],
            'time_period' => $startTime . '~' . $endTime
        ];
        return $result;
    }

    public static function getDayFlow($port, $node=null,$startTime,$endTime){
        $arrayDayFlow = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $startSec = strtotime($startTime);
        $oneHourSec = 3600;
        if ($node) {
            $queryArray = DB::table('flows')->where([
                ['port', '=', $port],
                ['date_time', '>=', $startTime],
                ['date_time', '<=', $endTime],
                ['address', $node]
            ])->get();    
        }else{
            $queryArray = DB::table('flows')->where([
                ['port', '=', $port],
                ['date_time', '>=', $startTime],
                ['date_time', '<=', $endTime]
            ])->get();
        }
        if (!empty($queryArray)) {
            foreach ($queryArray as $item) {
                if ($item->time < $startSec + $oneHourSec) {
                    $arrayDayFlow[0] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 2) {
                    $arrayDayFlow[1] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 3) {
                    $arrayDayFlow[2] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 4) {
                    $arrayDayFlow[3] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 5) {
                    $arrayDayFlow[4] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 6) {
                    $arrayDayFlow[5] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 7) {
                    $arrayDayFlow[6] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 8) {
                    $arrayDayFlow[7] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 9) {
                    $arrayDayFlow[8] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 10) {
                    $arrayDayFlow[9] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 11) {
                    $arrayDayFlow[10] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 12) {
                    $arrayDayFlow[11] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 13) {
                    $arrayDayFlow[12] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 14) {
                    $arrayDayFlow[13] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 15) {
                    $arrayDayFlow[14] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 16) {
                    $arrayDayFlow[15] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 17) {
                    $arrayDayFlow[16] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 18) {
                    $arrayDayFlow[17] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 19) {
                    $arrayDayFlow[18] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 20) {
                    $arrayDayFlow[19] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 21) {
                    $arrayDayFlow[20] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 22) {
                    $arrayDayFlow[21] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 23) {
                    $arrayDayFlow[22] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneHourSec * 24) {
                    $arrayDayFlow[23] += round($item->flow / 1024, 2);
                    continue;
                }

            }
        }
        $result = array(
            'flow' => $arrayDayFlow,
            'labels' => ["00:00", "", "", "",
                "04:00", "", "", "",
                "08:00", "", "", "",
                "12:00", "", "", "",
                "16:00", "", "", "",
                "20:00", "", "", ""],
            'time_period' => $startTime . '~' . $endTime
        );
        return $result;
    }

    public static function getWeekFlow($port, $node=null,$startTime,$endTime){
        $arrayWeekFlow = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $startSec = strtotime($startTime);
        $oneSchedule = 86400;
        if ($node) {
            $queryArray = DB::table('flows')->where([
                ['port', '=', $port],
                ['date_time', '>=', $startTime],
                ['date_time', '<=', $endTime],
                ['address', $node]
            ])->get();    
        }else{
            $queryArray = DB::table('flows')->where([
                ['port', '=', $port],
                ['date_time', '>=', $startTime],
                ['date_time', '<=', $endTime]
            ])->get();
        }
        if (!empty($queryArray)) {
            foreach ($queryArray as $item) {
                if ($item->time < $startSec + $oneSchedule) {
                    $arrayWeekFlow[0] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 2) {
                    $arrayWeekFlow[1] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 3) {
                    $arrayWeekFlow[2] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 4) {
                    $arrayWeekFlow[3] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 5) {
                    $arrayWeekFlow[4] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 6) {
                    $arrayWeekFlow[5] += round($item->flow / 1024, 2);
                    continue;
                }
                if ($item->time < $startSec + $oneSchedule * 7) {
                    $arrayWeekFlow[6] += round($item->flow / 1024, 2);
                    continue;
                }
            }
        }
        $result = array(
            'flow' => $arrayWeekFlow,
            'labels' => ["周一", "周二", "周三", "周四", "周五", "周六", "周日"],
            'time_period' => $startTime . '~' . $endTime
        );
        return $result;
    }
}
