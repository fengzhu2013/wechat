<?php
namespace app\comment\model;


use think\Model;

class MassLog extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 获得信息
     * @param array $where
     * @return array
     */
    public function getInfo(array $where):array
    {
        $where = self::$formatObj->formatArrKey($where,'i');
        $info  = self::get($where);
        if (empty($info)) {
            return [];
        }
        return self::$formatObj->formatArrKey($info->toArray());
    }


    /**
     * 插入数据
     * @param array $info
     * @return array|bool|false|int
     */
    public function insertInfo(array $info)
    {
        if (count($info) === count($info,1)) {
            $ret = $this->data($info,true)->isUpdate(false)->save();
        } else {
            $ret = $this->saveAll($info,false);
        }
        return $ret;
    }


    /**
     * @param array $info
     * @return array|bool
     */
    public function updateMoreInfo(array $info)
    {
        foreach ($info as $val) {
            $data[] = self::$formatObj->formatArrKey($val,'i');
        }
        return $this->saveAll($data);
    }








}