<?php
/**
 * Created by PhpStorm.
 * Desc: 相关授权
 * User: allen <cyb929@163.com>
 * Date: 2018/1/5
 * Time: 下午4:19
 */

namespace Wechat;

use Wechat\Lib\Common;
use Wechat\Lib\Tools;

class WechatAuthorize extends Common
{
    const AUTHORIZE_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
    const AUTHORIZE_URL = '/authorize?';
    const AUTHORIZE_TOKEN_URL = '/sns/oauth2/access_token?';
    const AUTHORIZE_REFRESH_URL = '/sns/oauth2/refresh_token?';
    const AUTHORIZE_USERINFO_URL = '/sns/userinfo?';
    const AUTHORIZE_AUTH_URL = '/sns/auth?';

    /**
     * Oauth 授权跳转接口
     * @param string $callback 授权回跳地址
     * @param string $state 为重定向后会带上state参数（填写a-zA-Z0-9的参数值，最多128字节）
     * @param string $scope 授权类类型(可选值snsapi_base|snsapi_userinfo)
     * @return string
     */
    public function getAuthorizeRedirect($callback, $state = '', $scope = 'snsapi_base')
    {
        $redirect_uri = urlencode($callback);
        return self::AUTHORIZE_PREFIX . self::AUTHORIZE_URL . "appid={$this->appId}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
    }


    /**
     * 通过 code 获取 AccessToken 和 openid
     * @return bool|array
     */
    public function getAuthorizeAccessToken()
    {
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        if (empty($code)) {
            Tools::log("getOauthAccessToken Fail, Because there is no access to the code value in get. MSG - {$this->appId}");
            return false;
        }
        $result = Tools::httpGet(self::API_BASE_URL_PREFIX . self::AUTHORIZE_TOKEN_URL . "appid={$this->appId}&secret={$this->appSecret}&code={$code}&grant_type=authorization_code");
        if ($result) {
            $json = json_decode($result, true);
            if (empty($json) || !empty($json['errcode'])) {
                $this->errCode = isset($json['errcode']) ? $json['errcode'] : '505';
                $this->errMsg = isset($json['errmsg']) ? $json['errmsg'] : '无法解析接口返回内容！';
                Tools::log("WechatOauth::getOauthAccessToken Fail.{$this->errMsg} [{$this->errCode}] ERR - {$this->appId}");
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 刷新access token并续期
     * @param string $refresh_token
     * @return bool|array
     */
    public function getAuthorizeRefreshToken($refresh_token)
    {
        $result = Tools::httpGet(self::API_BASE_URL_PREFIX . self::AUTHORIZE_REFRESH_URL . "appid={$this->appId}&grant_type=refresh_token&refresh_token={$refresh_token}");
        if ($result) {
            $json = json_decode($result, true);
            if (empty($json) || !empty($json['errcode'])) {
                $this->errCode = isset($json['errcode']) ? $json['errcode'] : '505';
                $this->errMsg = isset($json['errmsg']) ? $json['errmsg'] : '无法解析接口返回内容！';
                Tools::log("WechatAuthorize::getAuthorizeRefreshToken Fail.{$this->errMsg} [{$this->errCode}]", "ERR - {$this->appId}");
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 获取授权后的用户资料
     * @param string $access_token
     * @param string $openid
     * @return bool|array {openid,nickname,sex,province,city,country,headimgurl,privilege,[unionid]}
     * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
     */
    public function getAuthorizeUserInfo($access_token, $openid)
    {
        $result = Tools::httpGet(self::API_BASE_URL_PREFIX . self::AUTHORIZE_USERINFO_URL . "access_token={$access_token}&openid={$openid}");
        if ($result) {
            $json = json_decode($result, true);
            if (empty($json) || !empty($json['errcode'])) {
                $this->errCode = isset($json['errcode']) ? $json['errcode'] : '505';
                $this->errMsg = isset($json['errmsg']) ? $json['errmsg'] : '无法解析接口返回内容！';
                Tools::log("WechatAuthorize::getAuthorizeUserInfo Fail.{$this->errMsg} [{$this->errCode}] ERR - {$this->appId}");
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 检验授权凭证是否有效
     * @param string $access_token
     * @param string $openid
     * @return bool 是否有效
     */
    public function getAuthorizeAuth($access_token, $openid)
    {
        $result = Tools::httpGet(self::API_BASE_URL_PREFIX . self::AUTHORIZE_AUTH_URL . "access_token={$access_token}&openid={$openid}");
        if ($result) {
            $json = json_decode($result, true);
            if (empty($json) || !empty($json['errcode'])) {
                $this->errCode = isset($json['errcode']) ? $json['errcode'] : '505';
                $this->errMsg = isset($json['errmsg']) ? $json['errmsg'] : '无法解析接口返回内容！';
                Tools::log("WechatAuthorize::getAuthorizeAuth Fail.{$this->errMsg} [{$this->errCode}] ERR - {$this->appId}");
                return false;
            } elseif (intval($json['errcode']) === 0) {
                return true;
            }
        }
        return false;
    }


}