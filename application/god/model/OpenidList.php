<?php
namespace app\god\model;


use think\Db;
use think\Model;

class OpenidList extends Model
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 根据条件获得$num条数据，按id升序排列
     * @param array $where
     * @param int $num
     * @return array
     */
    public function getOpenidList(array $where,int $num): array
    {
        $where = self::$formatObj->formatArrKey($where,'i');
        $pages = ['page' => 1,'pageSize' => $num];
        $order = ['id' => 'asc'];
        return $this->getPage($pages,$where,$order);
    }


    /**
     * 修改状态
     * @param array $data
     * @param string $status
     * @return int
     */
    public function modifyStatus(array $data,string $status):int
    {
        $info   = ['status' => $status];
        $map['openid'] = ['in',$data];
        return $this->isUpdate(true)->save($info,$map);
    }



}