<?php
namespace app\common\model;


use think\Model;

class UserAction extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 插入一条信息
     * @param array $info
     * @return int
     */
    public function insertOneInfo(array $info):int
    {
        $data = self::$formatObj->formatArrKey($info,'i');
        $this->data($data)->isUpdate(false)->save();
        return $this->getAttr("action_id");
    }

    /**
     * 获得最新的一个share
     * @return string
     */
    public function getLastShareNo(): string
    {
        $map['action_type'] = ['neq','view'];
        $info = self::where($map)->field('share_no')->order('action_id','desc')->find();
        if (empty($info) || (isset($info['share_no']) && empty($info['share_no']))) {
            return '1804231000';
        }
        return $info->getAttr('share_no');
    }

    /**
     * 通过分享编号获得祖先分享编号
     * @param $shareNo
     * @return mixed|string
     */
    public function getAncestorNoByShareNo($shareNo)
    {
        $where  = ['share_no' => $shareNo];
        $info   = self::where($where)->field('ancestor_share_no')->find();
        if (empty($info)) {
            return '';
        }
        return $info->getAttr('ancestor_share_no');
    }




}