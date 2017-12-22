<?php
/**
 * Created by PhpStorm.
 * Desc: 菜单
 * User: allen <cyb929@163.com>
 * Date: 2017/12/20
 * Time: 下午4:23
 */

require_once 'config.inc.php';

$menu = new Wechat\WechatMenu();

$data = '';
$response = $menu->createMenu(json_decode($data,true));

echo ($response);