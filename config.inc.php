<?php
/**
 * Created by PhpStorm.
 * Desc: 项目整体配置
 * User: allen <cyb929@163.com>
 * Date: 2017/12/20
 * Time: 下午4:20
 */
session_start();
define("APPS_PATH",substr(realpath(dirname(__FILE__)),0,-4));
ini_set('display_errors', 'On');
ini_set('date.timezone','Asia/Shanghai');

//相关配置
$config = array();

//内存
$config['redis']['host'] = '127.0.0.1';
$config['redis']['port'] = 6379;
$config['redis']['password'] = '';
$config['redis']['timeout'] = 1;
$config['redis']['active'] = true;


//微信公众号配置
$config['wechat']['appId'] = '';
$config['wechat']['appSecret'] = '';
$config['wechat']['token'] = '';
$config['wechat']['encodingAesKey'] = '';


//日志配置
$config['log']['path'] = '/var/log/wwwlogs/wx.log';

if (! file_exists('vendor/autoload.php')) {
    throw new RuntimeException(
        'Unable to load application.' . PHP_EOL
        . '- Type `composer install` if you are developing locally.' . PHP_EOL
        . '- Type `vagrant ssh -c \'composer install\'` if you are using Vagrant.' . PHP_EOL
        . '- Type `docker-compose run apigility composer install` if you are using Docker.'
    );
}

// Setup autoloading
include 'vendor/autoload.php';