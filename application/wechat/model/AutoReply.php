<?php
namespace app\wechat\model;

use think\Model;

class AutoReply extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }


    /**
     * 根据条件获得一条信息
     * @param $where
     * @return array
     */
    public function getOneInfoDesc($where)
    {
        $ret    = [];
        $info   = self::where($where)->order('id','desc')->find();
        if (empty($info)) {
            return $ret;
        }
        $ret    = self::$formatObj->formatArrKey($info->toArray());
        return $ret;
    }

    /**
     * 根据关键词获得信息
     * @param string $keywords
     * @return array
     */
    public function getOneInfoByKeywordsDesc(string $keywords):array
    {
        //先查询半匹配关键词
        $map['keywords']    = ['like',"%{$keywords}%"];
        $map['reply_type']  = ['=','3'];
        $info               = self::where($map)->order('id','desc')->find();
        if ($info) {
            return self::$formatObj->formatArrKey($info->toArray());
        }
        //再查询全匹配关键词
        $where              = ['keywords' => $keywords,'reply_type' => '4'];
        $info               = self::where($where)->order('id','desc')->find();
        if (empty($info)) {
            return [];
        }
        return self::$formatObj->formatArrKey($info->toArray());
    }






}