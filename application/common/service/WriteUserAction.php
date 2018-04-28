<?php
namespace app\common\service;


use app\common\model\UserAction;

class WriteUserAction
{
    const VIEW          = 'view';
    const MESSAGE       = 'message';

    const TIME_LINE     = 'timeLine';


    public static function writeLog($articleId,$type,$operatorInfo)
    {
        $param = [
            'articleId'         => $articleId,
            'userId'            => $operatorInfo['userId'],
            'actionType'        => $type,
            'ancestorShareNo'   => $operatorInfo['ancestorShareNo'],
            'parentShareNo'     => $operatorInfo['parentShareNo'],
            'shareNo'           => $operatorInfo['shareNo'],
            'actionDate'        => date('Y-m-d H:i:s',$operatorInfo['timestamp']),
            'actionMemo'        => $operatorInfo['actionMemo'],
        ];

        $UserAction = new UserAction();
        $UserAction->insertOneInfo($param);
    }
}