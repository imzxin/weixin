<?php
/**
 * Created by PhpStorm.
 * Desc: 参数配置获取
 * User: allen <cyb929@163.com>
 * Date: 2017/12/20
 * Time: 下午4:07
 */
namespace Wechat\Lib;

class Config
{
    public function __construct(){
        extract($GLOBALS);
    }

    public static function get($key){
        return $GLOBALS[$key];
    }
}