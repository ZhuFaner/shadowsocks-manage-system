<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member extends Model
{

    public static function getMemberByPort($port){
        $member = DB::table('members')->where('port',$port)->first();
        return $member;
    }

    /**
     * 添加新用户
     * @param $port
     * @param $password
     * @param $name
     */
    public static function addNewMember($port, $password, $name){
        DB::table('members')->insert(array(
            'port'=>$port,
            'password'=>$password,
            'name'=>$name
        ));
    }

    /**
     * 根据端口号删除用户
     * @param $port
     */
    public static function deleteByPort($port){
        DB::table('members')->where('port', '=', $port)->delete();
    }

    /**
     * 根据端口号更新用户信息
     * @param $port
     * @param $password
     * @param $name
     */
    public static function updateByPort($port, $password, $name){
        DB::table('members')
            ->where('port', $port)
            ->update(['password' => $password,
                        'name' => $name ]);
    }


}
