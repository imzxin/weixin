<?php
/**
 * Created by PhpStorm.
 * User: allen <cyb929@163.com>
 * Date: 2017/12/20
 * Time: 下午4:25
 */
namespace Wechat\Lib;

use Wechat\Loader;

class Common
{

    /** API接口URL需要使用此前缀 */
    const API_BASE_URL_PREFIX = 'https://api.weixin.qq.com';
    const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
    const GET_TICKET_URL = '/ticket/getticket?';
    const AUTH_URL = '/token?grant_type=client_credential&';
    public $cache;
    public $token;
    public $encodingAesKey;
    public $encryptType;
    public $appId;
    public $appSecret;
    public $accessToken;
    public $postXml;
    public $_msg;
    public $errCode = 0;
    public $errMsg = "";
    public $config = array();
    public $retry = false;
    /**
     * 构造方法
     * @param array $options
     */
    public function __construct($options = array())
    {
        $config = Config::get('config');
        $this->token = isset($config['wechat']['token']) ? $config['wechat']['token'] : '';
        $this->appId = isset($config['wechat']['appId']) ? $config['wechat']['appId'] : '';
        $this->appSecret = isset($config['wechat']['appSecret']) ? $config['wechat']['appSecret'] : '';
        $this->encodingAesKey = isset($config['wechat']['encodingAesKey']) ? $config['wechat']['encodingAesKey'] : '';
        $this->config = $config;
        $this->cache = Cache::getInstance(null,'');
    }
    /**
     * 当前当前错误代码
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errCode;
    }
    /**
     * 获取当前错误内容
     * @return string
     */
    public function getError()
    {
        return $this->errMsg;
    }
    /**
     * 获取当前操作公众号APPID
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }
    /**
     * 获取SDK配置参数
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
    /**
     * 接口验证
     * @return bool
     */
    public function valid()
    {

        Tools::log('get:'.json_encode($_GET));
        $encryptStr = "";
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $postStr = file_get_contents("php://input");
            Tools::log('post:'.json_encode($postStr));

            $array = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->encryptType = isset($_GET["encrypt_type"]) ? $_GET["encrypt_type"] : '';
            if ($this->encryptType == 'aes') {
                $encryptStr = $array['Encrypt'];
                !class_exists('Prpcrypt', false) && require __DIR__ . '/Prpcrypt.php';
                $pc = new Prpcrypt($this->encodingAesKey);
                $array = $pc->decrypt($encryptStr, $this->appId);
                Tools::log('array:'.json_encode($array));

                if (!isset($array[0]) || intval($array[0]) > 0) {
                    $this->errCode = $array[0];
                    $this->errMsg = $array[1];
                    Tools::log("Interface Authentication Failed. {$this->errMsg}[{$this->errCode}]", "ERR - {$this->appId}");
                    return false;
                }
                $this->postXml = $array[1];
                empty($this->appId) && $this->appId = $array[2];
            } else {
                $this->postXml = $postStr;
            }
        } elseif (isset($_GET["echostr"])) {
            if ($this->checkSignature()) {
                @ob_clean();
                exit($_GET["echostr"]);
            }
            return false;
        }
        if (!$this->checkSignature($encryptStr)) {
            $this->errMsg = 'Interface authentication failed, please use the correct method to call.';
            return false;
        }
        return true;
    }
    /**
     * 验证来自微信服务器
     * @param string $str
     * @return bool
     */
    private function checkSignature($str = '')
    {
        $signature = (isset($_GET["signature"]) ? $_GET["signature"] : '');
        $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : '';
        $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : '';
        $tmpArr = array($this->token, $timestamp, $nonce, $str);
        sort($tmpArr, SORT_STRING);
        if (sha1(implode($tmpArr)) == $signature) {
            return true;
        }
        return false;
    }

    /**
     * 获取公众号访问 access_token
     * @param string $appId 如在类初始化时已提供，则可为空
     * @param string $appSecret 如在类初始化时已提供，则可为空
     * @param string $token 手动指定access_token，非必要情况不建议用
     * @return bool|string
     */
    public function getAccessToken($appId = '', $appSecret = '', $token = '')
    {
        if (!$appId || !$appSecret) {
            list($appId, $appSecret) = [$this->appId, $this->appSecret];
        }
        if ($token) {
            return $this->accessToken = $token;
        }
        $cacheAccessTokenKey = 'wechat_access_token_' . $appId;
        if (($access_token = $this->cache->get($cacheAccessTokenKey)) && !empty($access_token)) {
            return $this->accessToken = $access_token;
        }
        # 检测事件注册
        if (isset(Loader::$callback[__FUNCTION__])) {
            return $this->accessToken = call_user_func_array(Loader::$callback[__FUNCTION__], array(&$this, &$cacheAccessTokenKey));
        }
        $result = Tools::httpGet(self::API_URL_PREFIX . self::AUTH_URL . 'appid=' . $appId . '&secret=' . $appSecret);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                Tools::log("Get New AccessToken Error. {$this->errMsg}[{$this->errCode}]", "ERR - {$this->appId}");
                return false;
            }
            $this->accessToken = $json['access_token'];
            Tools::log("Get New AccessToken Success.", "MSG - {$this->appId}");
            $this->cache->set($cacheAccessTokenKey, $this->accessToken, 5000);
            return $this->accessToken;
        }
        return false;
    }
    /**
     * 接口失败重试
     * @param string $method SDK方法名称
     * @param array $arguments SDK方法参数
     * @return bool|mixed
     */
    protected function checkRetry($method, $arguments = array())
    {
        Tools::log("Run {$method} Faild. {$this->errMsg}[{$this->errCode}]", "ERR - {$this->appId}");
        if (!$this->retry && in_array($this->errCode, array('40014', '40001', '41001', '42001'))) {
            ($this->retry = true) && $this->resetAuth();
            $this->errCode = 40001;
            $this->errMsg = 'no access';
            Tools::log("Retry Run {$method} ...", "MSG - {$this->appId}");
            return call_user_func_array(array($this, $method), $arguments);
        }
        return false;
    }
    /**
     * 删除验证数据
     * @param string $appId 如在类初始化时已提供，则可为空
     * @return bool
     */
    public function resetAuth($appId = '')
    {
        $authname = 'wechat_access_token_' . (empty($appid) ? $this->appId : $appId);
        Tools::log("Reset Auth And Remove Old AccessToken.", "MSG - {$this->appId}");
        $this->accessToken = '';
        $this->cache->del($authname);
        return true;
    }
}