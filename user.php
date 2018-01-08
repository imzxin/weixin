<?php
/**
 * Created by PhpStorm.
 * User: allen <cyb929@163.com>
 * Date: 2018/1/5
 * Time: 下午4:43
 */
header("Content-type: text/html; charset=utf-8");
require_once 'config.inc.php';

$authorize = new Wechat\WechatAuthorize();
$url = 'http://xxxx/user.php';
$authorize_url = $authorize->getAuthorizeRedirect($url);

if($_GET['code']){
  $response = $authorize->getAuthorizeAccessToken();
  if(!$response){
      header('Location: '.$authorize_url);exit;
  }

  if($response['access_token']){
      $user = $authorize->getAuthorizeUserInfo($response['access_token'],$response['openid']);
      var_dump($user);
  }
}else {
    header('Location: '.$authorize_url);exit;
}