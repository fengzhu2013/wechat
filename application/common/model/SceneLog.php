<?php
namespace app\common\model;

use think\Model;

class SceneLog extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 插入一条信息，返回自增id
     * @param $info
     * @return mixed
     */
    public function insertInfo(array $info)
    {
        $info = self::$formatObj->formatArrKey($info,'i');
        $this->data($info);
        $this->isUpdate(false)->save();
        return $this->id;
    }
}