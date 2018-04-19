<?php
namespace app\autoReply\model;


use think\Model;

class AutoReply extends Model
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
    public function insertInfo($info)
    {
        $info = self::$formatObj->formatArrKey($info,'i');
        $this->data($info);
        $this->isUpdate(false)->save();
        return $this->getAttr('id');
    }

    /**
     * 根据条件获得一条数据，按id倒叙排列
     * @param $where
     * @return array
     */
    public function getOneInfoDesc($where)
    {
        $ret = [];
        $info = self::where($where)->order('id','desc')->find();
        if (empty($info)) {
            return $ret;
        }
        $ret  = self::$formatObj->formatArrKey($info->toArray());
        return $ret;
    }

    /**
     * 根据条件获得一条信息
     * @param $where
     * @return array
     */
    public function getOneInfo($where)
    {
        return self::getSelf($where);
    }

    /**
     * 修改一条信息
     * @param $data
     * @param $where
     * @return int|string
     */
    public function modifyInfo($data,$where)
    {
        $info = self::$formatObj->formatArrKey($data,'i');
        return self::where($where)->update($info);
    }


    /**
     * @param array $where
     * @return int
     */
    public function deleteInfo(array $where):int
    {
        $where = self::$formatObj->formatArrKey($where,'i');
        return self::destroy($where);
    }

    /**
     * 活动所有的关键词列表
     * @return array
     */
    public function getKeywordsList()
    {
        $ret = [];
        $list = self::all(function($query){
            $query->where('reply_type','4')->whereOr('reply_type','3')->field('id,reply_type,keywords');
        });
        if (empty($list)) {
            return $ret;
        }
        foreach ($list as $val) {
            $ret[] = self::$formatObj->formatArrKey($val->toArray());
        }
        return $ret;
    }


}