<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 创建令牌号
 * @param $userId
 * @param $password
 * @param $time
 * @return string
 */
function createSESSION_ID($userId,$password,$time)
{
    return md5($userId.$password.$time);
}

function getExtension($filename)
{
    $index = strrpos($filename,'.');
    if ($index !== false) {
        return substr($filename,$index+1);
    }
    return false;
}



