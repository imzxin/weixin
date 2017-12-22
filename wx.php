<?php
/**
 *
 * 微信公众号开发者入口文件
 *
 */
require_once 'config.inc.php';
$receive = new \Wechat\WechatReceive();

//获取消息体
$response = $receive->getReceive();
\Wechat\Lib\Tools::log('response:'.json_encode($response->getReceiveData()));

//获取事件类型
$event = $receive->getReceiveEvent();
\Wechat\Lib\Tools::log('event:'.json_encode($event));


//获取消息类型
$msgType = $receive->getReceiveType();
\Wechat\Lib\Tools::log('msgType:'.$msgType);

$event = $event['event'];
if(isset($event) && !empty($event)){ //依据事件类型业务处理

    switch ($event){
        case "LOCATION": //地理位置

            //$receive->text(json_encode($receive->getReceiveEventGeo()))->reply();

            break;
        case "subscribe": //关注

            $receive->text('')->reply();

            break;
        case "unsubscribe": //取消关注


            break;
    }

}else if(isset($msgType) && !empty($msgType)){
    switch ($msgType){
        case "text":

            $receive->text('你说啥！')->reply();

            break;
    }
}