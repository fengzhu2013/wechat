<?php
namespace app\scene\model;

use think\Db;
use think\Model;

class SceneInfo extends Model
{

    protected $keys = [
        'nameCn','phone','province','city','area','addressCn','startDate','endDate','sceneType',
    ];

    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * 插入多条信息
     * @param array $info 多维数组
     * @return array
     */
    public function addMore(array $info):array
    {
        $ret = [];
        foreach ($info as $key => $val) {
            $ret[$key] = $this->addOne([$key => $val]);
        }
        return $ret;
    }

    /**
     * 一次插入渠道信息
     * @param array $info 是二维数组，单只有一个元素,如[0=>[]];
     * @return bool
     */
    public function addOne(array $info) :bool
    {
        Db::startTrans();
        try{
            foreach ($info as $key => $val) {
                foreach ($val as $secKey => $secVal) {
                    $data = [
                        'sceneId'       => $key,
                        'sceneKey'      => $secKey,
                        'sceneValue'    => $secVal,
                    ];
                    //插入数据库
                    self::data($data)->isUpdate(false)->saveSelf();
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
        return true;
    }


    /**
     * 通过sceneId获得相关信息
     * @param $sceneId
     * @return array 多维数组或为空
     */
    public function getInfo($sceneId):array
    {
        $ret = [];
        $where = ['scene_id' => $sceneId];
        $list = self::all($where);
        if (!$list) {
            return $ret;
        }
        foreach ($list as $val) {
            $ret[] = self::$formatObj->formatArrKey($val->toArray());
        }
        return $ret;
    }

    /**
     * 根据sceneId更新数据，更新成功，返回true
     * @param int $sceneId
     * @param array $info
     * @return bool
     */
    public function updateInfo(int $sceneId,array $info):bool
    {
        $where = ['sceneId' => $sceneId];
        Db::startTrans();
        try{
            foreach ($info as $key => $val) {
                $where['sceneKey'] = $key;
                $data = ['sceneValue' => $val];
                self::updateSelf(self::$formatObj->formatArrKey($data,'i'),self::$formatObj->formatArrKey($where,'i'));
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
        return true;
    }






}