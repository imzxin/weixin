<?php
/**
 * Created by PhpStorm.
 * Desc: 基础缓存类
 * User: allen <cyb929@163.com>
 * Date: 2017/12/20
 * Time: 下午3:52
 */
namespace Wechat\Lib;

class Cache
{

    private static $instance = array();
    private $isRedis = false;


    /**
     * 单例模型，构造函数
     * @param $redisCacheConfig
     * @param string $serverName
     * @return bool|mixed
     * @throws mixed
     */
    public static function getInstance($redisCacheConfig=null, $serverName='') {
        if (is_null($redisCacheConfig)) {
            $config = Config::get('config');
            $redisCacheConfig = $config['redis'];
        }
        if (!$redisCacheConfig['active']) {
            return false;
        }

        if (isset(self::$instance[$serverName])) {
            return self::$instance[$serverName];
        }
        if (!isset($redisCacheConfig['host'])) {
            throw new \Exception("param serverName Or redis config error...");
        }
        if (!class_exists('Redis')) {
            throw new \Exception("不能加载 Redis 类库，请检查php配置文件");
        }
        if (isset($redisCacheConfig['host']) && isset($redisCacheConfig['port']) && isset($redisCacheConfig['password']) && isset($redisCacheConfig['timeout'])) {
            self::$instance[$serverName] = new self($redisCacheConfig['host'], $redisCacheConfig['port'], $redisCacheConfig['password'], $redisCacheConfig['timeout']);
            return self::$instance[$serverName];
        }
        return false;
    }

    /**
     * 私有, 单例模型，禁止外部构造
     * @param $host
     * @param $port
     * @param $password
     * @param $timeout
     */
    private function __construct($host, $port, $password, $timeout) {
        $this->isRedis = new \Redis();
        if ($this->isRedis->connect($host, $port, $timeout)) {
            if (!empty($password)) {
                $this->isRedis->auth($password);
            }
        }
    }



    /**
     *  私有, 单例模型，禁止克隆
     */
    private function __clone() {
    }


    /**
     * 关闭连接
     */
    public function close(){
        $this->isRedis->close(); return true;
    }

    /**
     * 公有，调用对象函数
     * @param $method
     * @param $args
     * @return bool|mixed
     * @throws \Exception
     */
    public function __call($method, $args) {
        if (!$this->isRedis || !$method) {
            return false;
        }
        if (!method_exists($this->isRedis, $method)) {
            throw new \Exception("Class RedisCli not have method ($method) ");
        }
        return call_user_func_array(array($this->isRedis, $method), $args);
    }



}