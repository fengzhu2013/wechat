<?php
namespace app\wechat\model;


use think\Model;

class SystemInfo extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 倒叙获得一条信息
     * @return array
     */
    public function getInfoDesc():array
    {
        $ret = [];
        $info = self::where([])->order('id','desc')->find();
        if (empty($info)) {
            return $ret;
        }
        return self::$formatObj->formatArrKey($info->toArray());
    }


}