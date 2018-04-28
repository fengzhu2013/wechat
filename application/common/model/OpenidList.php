<?php
namespace app\common\model;


use think\Model;

class OpenidList extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 插入一条数据,成功，返回自增id，失败，返回0
     * @param array $info
     * @return int
     */
    public function insertInfo(array $info): int
    {
        $data = self::$formatObj->formatArrKey($info,'i');
        $this->data($data)->isUpdate(false)->save();
        $id   = $this->getAttr('id');
        if (empty($id)) {
            return 0;
        }
        return $id;
    }

    /**
     * 更新信息
     * @param array $info
     * @param array $where
     * @return int
     */
    public function updateInfo(array $info,array $where): int
    {
        $data   = self::$formatObj->formatArrKey($info,'i');
        $where  = self::$formatObj->formatArrKey($where,'i');
        $ret    = $this->save($data,$where);
        if (!$ret && is_bool($ret)) {
            return 0;
        }
        return $ret;
    }





}