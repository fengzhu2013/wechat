<?php
namespace app\menu\model;


use think\Db;
use think\Model;

class Menu extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 插入多条数据
     * @param $info
     * @param $isEmpty
     * @return bool
     */
    public function insertAll($info,$isEmpty = false):bool
    {
        Db::startTrans();
        try {
            //是否清空数据
            if ($isEmpty) {
                self::where('id','>',0)->delete();
            }
            foreach ($info as $val) {
                $val = self::$formatObj->formatArrKey($val,'i');
                $this->data($val,true)->isUpdate(false)->save();
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
        return true;
    }

    /**
     * 获取到多条数据
     * @param $where
     * @return array
     */
    public function getAll($where = []):array
    {
        $ret  = [];
        $list = self::all($where);
        if (empty($list)) {
            return $ret;
        }
        foreach ($list as $val) {
            $ret[] = self::$formatObj->formatArrKey($val->toArray());
        }
        return $ret;
    }

    /**
     * 根据条件删除数据
     * @param array $where
     * @return int
     */
    public function deleteInfo(array $where):int
    {
        return self::where($where)->delete();
    }
}