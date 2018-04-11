<?php
namespace app\common\model;


use think\Model;

class SystemInfo extends Model
{

    /**
     * 检查要更新的变量，前提是已经调用了get方法获得了数据
     * @param $param
     * @return bool
     */
    public function checkUpdateInfo($param)
    {
        $i = 0;
        foreach ($param as $key => $val) {
            $value = self::$formatObj->formatIn($key);
            if ($this->{$value} !== $val) {
                $this->{$value} = $val;
                $i++;
            }
        }
        if (0 === $i) {
            return false;
        }
        return true;
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
