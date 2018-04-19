<?php
namespace app\wechat\logic;


use app\wechat\model\User;

class Common
{

    /**
     * 根据openid获得内部标识符
     * @param string $openid
     * @return string
     */
    public static function getUserIdByOpenId(string $openid):string
    {
        $User   = new User();
        $info   = $User->getInfoByOpenid($openid);
        if ($info && isset($info['userId'])) {
            return $info['userId'];
        }
        return '';
    }
}