<?php
/**
 * Created by PhpStorm.
 * Desc: 微信菜单
 * User: allen <cyb929@163.com>
 * Date: 2017/12/20
 * Time: 下午5:14
 */

namespace Wechat;
use Wechat\Lib\Common;
use Wechat\Lib\Tools;

class WechatMenu extends Common
{
    /** 创建自定义菜单 */
    const MENU_ADD_URL = '/menu/create?';
    /* 获取自定义菜单 */
    const MENU_GET_URL = '/menu/get?';
    /* 删除自定义菜单 */
    const MENU_DEL_URL = '/menu/delete?';
    /** 添加个性菜单 */
    const COND_MENU_ADD_URL = '/menu/addconditional?';
    /* 删除个性菜单 */
    const COND_MENU_DEL_URL = '/menu/delconditional?';
    /* 测试个性菜单 */
    const COND_MENU_TRY_URL = '/menu/trymatch?';

    /**
     * 创建自定义菜单
     * @param array $data 菜单数组数据
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013&token=&lang=zh_CN 文档
     * @return bool
     */
    public function createMenu($data)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        Tools::log('accessToken:'.$this->accessToken);
        $result = Tools::httpPost(self::API_URL_PREFIX . self::MENU_ADD_URL . "access_token={$this->accessToken}", Tools::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (empty($json) || !empty($json['errcode'])) {
                $this->errCode = isset($json['errcode']) ? $json['errcode'] : '505';
                $this->errMsg = isset($json['errmsg']) ? $json['errmsg'] : '无法解析接口返回内容！';
                return $this->checkRetry(__FUNCTION__, func_get_args());
            }
            return true;
        }
        return false;
    }
    /**
     * 获取所有菜单
     * @return bool|array
     */
    public function getMenu()
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $result = Tools::httpGet(self::API_URL_PREFIX . self::MENU_GET_URL . "access_token={$this->accessToken}");
        if ($result) {
            $json = json_decode($result, true);
            if (empty($json) || !empty($json['errcode'])) {
                $this->errCode = isset($json['errcode']) ? $json['errcode'] : '505';
                $this->errMsg = isset($json['errmsg']) ? $json['errmsg'] : '无法解析接口返回内容！';
                return $this->checkRetry(__FUNCTION__, func_get_args());
            }
            return $json;
        }
        return false;
    }
    /**
     * 删除所有菜单
     * @return bool
     */
    public function deleteMenu()
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $result = Tools::httpGet(self::API_URL_PREFIX . self::MENU_DEL_URL . "access_token={$this->accessToken}");
        if ($result) {
            $json = json_decode($result, true);
            if (empty($json) || !empty($json['errcode'])) {
                $this->errCode = isset($json['errcode']) ? $json['errcode'] : '505';
                $this->errMsg = isset($json['errmsg']) ? $json['errmsg'] : '无法解析接口返回内容！';
                return $this->checkRetry(__FUNCTION__, func_get_args());
            }
            return true;
        }
        return false;
    }
    /**
     * 创建个性菜单
     * @param array $data 菜单数组数据
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455782296&token=&lang=zh_CN 文档
     * @return bool|string
     */
    public function createCondMenu($data)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        var_dump($this->accessToken);
        $result = Tools::httpPost(self::API_URL_PREFIX . self::COND_MENU_ADD_URL . "access_token={$this->accessToken}", Tools::json_encode($data));
        var_dump($result);
        if ($result) {
            $json = json_decode($result, true);
            if (empty($json) || !empty($json['errcode'])) {
                $this->errCode = isset($json['errcode']) ? $json['errcode'] : '505';
                $this->errMsg = isset($json['errmsg']) ? $json['errmsg'] : '无法解析接口返回内容！';
                return $this->checkRetry(__FUNCTION__, func_get_args());
            }
            return $json['menuid'];
        }
        return false;
    }
    /**
     * 删除个性菜单
     * @param string $menuid 菜单ID
     * @return bool
     */
    public function deleteCondMenu($menuid)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $data = array('menuid' => $menuid);
        $result = Tools::httpPost(self::API_URL_PREFIX . self::COND_MENU_DEL_URL . "access_token={$this->accessToken}", Tools::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (empty($json) || !empty($json['errcode'])) {
                $this->errCode = isset($json['errcode']) ? $json['errcode'] : '505';
                $this->errMsg = isset($json['errmsg']) ? $json['errmsg'] : '无法解析接口返回内容！';
                return $this->checkRetry(__FUNCTION__, func_get_args());
            }
            return true;
        }
        return false;
    }
    /**
     * 测试并返回个性化菜单
     * @param string $openid 粉丝openid
     * @return bool
     */
    public function tryCondMenu($openid)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $data = array('user_id' => $openid);
        $result = Tools::httpPost(self::API_URL_PREFIX . self::COND_MENU_TRY_URL . "access_token={$this->accessToken}", Tools::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (empty($json) || !empty($json['errcode'])) {
                $this->errCode = isset($json['errcode']) ? $json['errcode'] : '505';
                $this->errMsg = isset($json['errmsg']) ? $json['errmsg'] : '无法解析接口返回内容！';
                return $this->checkRetry(__FUNCTION__, func_get_args());
            }
            return $json;
        }
        return false;
    }
}