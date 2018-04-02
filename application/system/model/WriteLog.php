<?php
namespace App\system\model;

/**
 * 记录管理员操作
 * Class WriteLog
 * @package App\system\model
 */
class WriteLog
{
    /**
     * 操作对象类型
     * @var array
     */
    private static $object_type = [
        0   => 'admin',
        1   => 'article',
        2   => 'auto_reply',
        3   => 'comment',
        4   => 'menu',
        5   => 'scene',
        6   => 'scene_info',
        7   => 'system'
    ];

    private static $action = [
        0   => 'a',//添加
        1   => 'd',//删除
        2   => 'u',//修改
        3   => 'i',//导入
        4   => 'o',//导出
        5   => 'l',//登陆
    ];



    public function __construct()
    {

    }

    public static function writeLog()
    {

    }

    /**
     * 验证操作对象类型
     * @param $type
     * @return bool
     */
    private static function verifyType($type)
    {
        return true;
    }

    /**
     * 验证事件类型
     * @param $action
     * @return bool
     */
    private static function verifyAction($action)
    {
        return true;
    }

}