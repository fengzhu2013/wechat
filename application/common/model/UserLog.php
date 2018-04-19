<?php
namespace app\common\model;


use think\Model;

class UserLog extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 插入一条信息，如果成功，返回主键
     * @param array $info
     * @return int
     */
    public function insetOneInfo(array $info):int
    {
        $info = self::$formatObj->formatArrKey($info,'i');
        $ret  = $this->data($info)->isUpdate(false)->save();
        return $this->id;
    }


}