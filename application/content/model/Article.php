<?php
namespace app\content\model;

use think\Model;

class Article extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 插入一条信息
     * @param array $info
     * @return int 若成功，返回主键
     */
    public function insertOneInfo(array $info):int
    {
        $data = self::$formatObj->formatArrKey($info,'i');
        $this->data($data)->isUpdate(false)->save();
        return $this->getAttr('article_id');
    }

    /**
     * 更新一条信息,返回受影响记录条数
     * @param array $info
     * @param array $where
     * @return int
     */
    public function modifyInfo(array $info,array $where):int
    {
        $data  = self::$formatObj->formatArrKey($info,'i');
        $where = self::$formatObj->formatArrKey($where,'i');
        return $this->save($data,$where);
    }



}