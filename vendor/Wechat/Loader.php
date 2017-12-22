<?php

namespace Wechat;
use Wechat\Lib\Cache;

spl_autoload_register(function ($class) {
    if (0 === stripos($class, 'Wechat\\')) {
        $filename = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        file_exists($filename) && require($filename);
    }
});
/**
 * Created by PhpStorm.
 * Desc: 微信SDK加载器
 * User: allen <cyb929@163.com>
 * Date: 2017/12/20
 * Time: 下午3:55
 */
class Loader
{
    /**
     * 事件注册函数
     * @var array
     */
    static public $callback = array();
    /**
     * 配置参数
     * @var array
     */
    static protected $config = array();
    /**
     * 对象缓存
     * @var array
     */
    static protected $cache = array();
    /**
     * 动态注册SDK事件处理函数
     * @param string $event 事件名称（getAccessToken|getJsTicket）
     * @param string $method 处理方法（可以是普通方法或者类中的方法）
     * @param string|null $class 处理对象（可以直接使用的类实例）
     */
    static public function register($event, $method, $class = null)
    {
        if (!empty($class) && class_exists($class, false) && method_exists($class, $method)) {
            self::$callback[$event] = array($class, $method);
        } else {
            self::$callback[$event] = $method;
        }
    }
    /**
     * 获取微信SDK接口对象(别名函数)
     * @param string $type 接口类型(Card|Custom|Device|Extends|Media|Menu|Oauth|Pay|Receive|Script|User|Poi)
     * @param array $config SDK配置(token,appid,appsecret,encodingaeskey,mch_id,partnerkey,ssl_cer,ssl_key,qrc_img)
     * @return mixed
     */
    static public function & get_instance($type, $config = array())
    {
        return self::get($type, $config);
    }
    /**
     * 获取微信SDK接口对象
     * @param string $type 接口类型(Card|Custom|Device|Extends|Media|Menu|Oauth|Pay|Receive|Script|User|Poi)
     * @param array $config SDK配置(token,appid,appsecret,encodingaeskey,mch_id,partnerkey,ssl_cer,ssl_key,qrc_img)
     * @return mixed
     */
    static public function & get($type, $config = array())
    {
        $index = md5(strtolower($type) . md5(json_encode(self::$config)));
        if (!isset(self::$cache[$index])) {
            $basicName = 'Wechat' . ucfirst(strtolower($type));
            $className = "\\Wechat\\{$basicName}";
            // 注册类的无命名空间别名，兼容未带命名空间的老版本SDK
            !class_exists($basicName, false) && class_alias($className, $basicName);
            self::$cache[$index] = new $className(self::config($config));
        }
        return self::$cache[$index];
    }

}