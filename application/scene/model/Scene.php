<?php
namespace app\scene\model;

use think\Model;

class Scene extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }


    //获得待领取(status = '1')的二维码总数量
    public function getWaitCount():int
    {
        $where = ['status' => '1'];
        return $this->getCount($where);
    }


    /**
     * 获得最后一个永久二维码的scene_id
     * @return int
     */
    public function getLastSceneId():int
    {
        $scene = self::where([])->order('scene_id','desc')->find();
        if (!$scene) {
            return 0;
        }
        return $scene->scene_id;
    }

    /**
     * 获得一定数量
     * @param int $count
     * @return array
     */
    public function getCountInfo(int $count):array
    {
        $where = ['status' => '1'];
        $param = ['page' => 1,'pageSize' => $count];
        $order = ['sceneId' => 'asc'];
        return $this->getPage($param,$where,$order);
    }

    /**
     * 获得有状态（即领取过）的二维码列
     * @param $page
     * @param $pageSize
     * @return array
     */
    public function getHadInfo($page,$pageSize):array
    {
        $page = $page?intval($page):1;
        $pageSize = $pageSize?intval($pageSize):8;
        $ret = [];
        $offset = ($page - 1) * $pageSize;
        $info   = self::where('status','neq',1)->order('id')->limit($offset,$pageSize)->select();
        if (!$info) {
            return $ret;
        }
        foreach ($info as $val) {
            $ret[] = self::$formatObj->formatArrKey($val->toArray());
        }
        return $ret;
    }

    /**
     * 获得已领取状态的总记录数
     * @return int
     */
    public function getHadCount():int
    {
        return self::where('status','neq',1)->count();
    }


    /**
     * 更新一条数据
     * @param array $data
     * @param array $where
     * @return int
     */
    public function updateOne(array $data,array $where):int
    {
        $data   = self::$formatObj->formatArrKey($data,'i');
        $where  = self::$formatObj->formatArrKey($where,'i');
        return self::where($where)->update($data);
    }


}