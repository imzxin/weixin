<?php
/**
 * Created by PhpStorm.
 * Desc: 微信模板消息
 * User: allen <cyb929@163.com>
 * Date: 2017/12/21
 * Time: 下午1:59
 */

namespace Wechat;

use Wechat\Lib\Common;
use Wechat\Lib\Tools;

class WechatMessage extends Common
{

    /**
     * 获取模板列表
     * @return bool|array
     */
    public function getAllPrivateTemplate()
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $result = Tools::httpPost(self::API_URL_PREFIX . "/template/get_all_private_template?access_token={$this->accessToken}", []);
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
     * 获取设置的行业信息
     * @return bool|array
     */
    public function getTMIndustry()
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $result = Tools::httpPost(self::API_URL_PREFIX . "/template/get_industry?access_token={$this->accessToken}", []);
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
     * 删除模板消息
     * @param string $tpl_id
     * @return bool
     */
    public function delPrivateTemplate($tpl_id)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $data = array('template_id' => $tpl_id);
        $result = Tools::httpPost(self::API_URL_PREFIX . "/template/del_private_template?access_token={$this->accessToken}", Tools::json_encode($data));
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
     * 模板消息 设置所属行业
     * @param string $id1 公众号模板消息所属行业编号，参看官方开发文档 行业代码
     * @param string $id2 同$id1。但如果只有一个行业，此参数可省略
     * @return bool|mixed
     */
    public function setTMIndustry($id1, $id2 = '')
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $data = array();
        !empty($id1) && $data['industry_id1'] = $id1;
        !empty($id2) && $data['industry_id2'] = $id2;
        $json = Tools::json_encode($data);
        $result = Tools::httpPost(self::API_URL_PREFIX . "/template/api_set_industry?access_token={$this->accessToken}", $json);
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
     * 模板消息 添加消息模板
     * 成功返回消息模板的调用id
     * @param string $tpl_id 模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
     * @return bool|string
     */
    public function addTemplateMessage($tpl_id)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $data = Tools::json_encode(array('template_id_short' => $tpl_id));
        $result = Tools::httpPost(self::API_URL_PREFIX . "/template/api_add_template?access_token={$this->accessToken}", $data);
        if ($result) {
            $json = json_decode($result, true);
            if (empty($json) || !empty($json['errcode'])) {
                $this->errCode = isset($json['errcode']) ? $json['errcode'] : '505';
                $this->errMsg = isset($json['errmsg']) ? $json['errmsg'] : '无法解析接口返回内容！';
                return $this->checkRetry(__FUNCTION__, func_get_args());
            }
            return $json['template_id'];
        }
        return false;
    }

    /**
     * 发送模板消息
     * @param array $data 消息结构
     * @return bool|array
     */
    public function sendTemplateMessage($data)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $result = Tools::httpPost(self::API_URL_PREFIX . "/message/template/send?access_token={$this->accessToken}", Tools::json_encode($data));
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
     * 根据标签进行群发 ( 订阅号与服务号认证后均可用 )
     * @param array $data 消息结构
     * @return bool|array
     */
    public function sendMassMessage($data)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $result = Tools::httpPost(self::API_URL_PREFIX . "/message/mass/send?access_token={$this->accessToken}", Tools::json_encode($data));
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
     * 根据标签进行群发 ( 订阅号与服务号认证后均可用 )
     * @param array $data 消息结构
     * 注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *       然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @return bool|array
     */
    public function sendGroupMassMessage($data)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $result = Tools::httpPost(self::API_URL_PREFIX . "/message/mass/sendall?access_token={$this->accessToken}", Tools::json_encode($data));
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
     * 删除群发图文消息 ( 订阅号与服务号认证后均可用 )
     * @param string $msg_id 消息ID
     * @return bool
     */
    public function deleteMassMessage($msg_id)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $data = Tools::json_encode(array('msg_id' => $msg_id));
        $result = Tools::httpPost(self::API_URL_PREFIX . "/message/mass/delete?access_token={$this->accessToken}", $data);
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
     * 预览群发消息 ( 订阅号与服务号认证后均可用 )
     * @param array $data
     * 注意: 视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *       然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @return bool|array
     */
    public function previewMassMessage($data)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $result = Tools::httpPost(self::API_URL_PREFIX . "/message/mass/preview?access_token={$this->accessToken}", Tools::json_encode($data));
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
     * 查询群发消息发送状态 ( 订阅号与服务号认证后均可用 )
     * @param string $msg_id 消息ID
     * @return bool|array
     * {
     *     "msg_id":201053012, //群发消息后返回的消息id
     *     "msg_status":"SEND_SUCCESS", //消息发送后的状态，SENDING表示正在发送 SEND_SUCCESS表示发送成功
     * }
     */
    public function queryMassMessage($msg_id)
    {
        if (!$this->accessToken && !$this->getAccessToken()) {
            return false;
        }
        $data = Tools::json_encode(array('msg_id' => $msg_id));
        $result = Tools::httpPost(self::API_URL_PREFIX . "/message/mass/get?access_token={$this->accessToken}", $data);
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