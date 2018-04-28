<?php
namespace app\common\service;



use app\common\model\OpenidList;

class WriteOpenidList
{
    const UNTREATED             = '1';
    const TREATING              = '2';
    const TREATED_SUCCESSFUL    = '3';
    const TREATED_FAILED        = '4';


    /**
     * @param array $info
     * @return int
     */
    public static function writeLog(array $info): int
    {
        $data['openid']     = $info['openid'];
        $data['status']     = $info['status']??self::UNTREATED;
        $data['createTime'] = $info['createTime']??time();

        $OpenidList = new OpenidList();
        return $OpenidList->insertInfo($data);
    }

    /**
     * 更新状态
     * @param int $id
     * @param string $status
     * @return int
     */
    public static function updateStatus(int $id,string $status): int
    {
        $where = ['id'      => $id];
        $data  = ['status'  => $status];

        $OpenidList = new OpenidList();
        return $OpenidList->updateInfo($data,$where);
    }


}