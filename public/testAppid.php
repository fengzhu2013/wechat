<?php
$appid = "wx1d79625d92c04591";
$redirectUrl = urlencode("https://projects.rfistudios.com.cn/testGetInfo.php");
$scope  = 'snsapi_base';
$url    = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirectUrl}&response_type=code&scope={$scope}&state=STATE#wechat_redirect";
var_dump($url);