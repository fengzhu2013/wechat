<?php
namespace app\system\model;


use think\Model;

class SystemInfo extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 获得微信系统中最后一条记录
     * @return array
     */
    public function getLastInfo()
    {
        $info = self::where([])->order('id','desc')->find();
        if ($info) {
            return self::$formatObj->formatArrKey($info->toArray());
        }
        return [];
    }


}