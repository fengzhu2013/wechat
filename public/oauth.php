<?php

if (!isset($_SESSION)) {
	session_start();
}

if (!isset($_SESSION["epWechatOpenId"]) && !isset($_GET["code"]) && isset($_GET["previousURL"])) {
	$_SESSION["epWechatPreviousURL"] = $_GET["previousURL"];
	header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx2c3e75fa9fb5f50b&redirect_uri=http%3a%2f%2fep.rfistudios.com.cn%2foauth.php&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect");
	exit();
} else if (isset($_GET["code"])) {
	include("assets/plugins/wechat/wechat.class.php");

	$options = array(
		'appid' => 'wx2c3e75fa9fb5f50b',
		'appsecret' => '7b683582502070f3be95476494d9f63f'
	);

	$weObj = new Wechat($options);

	$arrayAccessToken = $weObj->getOauthAccessToken();
	if (!empty($arrayAccessToken["openid"])) {
		$_SESSION["epWechatOpenId"] = $arrayAccessToken["openid"];
		header("Location: " . $_SESSION["epWechatPreviousURL"]);
		exit();
	}
}