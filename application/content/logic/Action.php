<?php
namespace app\content\logic;


use app\common\logic\ArrayTool;
use app\common\logic\Common;
use app\common\logic\StringTool;
use app\common\model\UserAction;
use app\content\model\User;

class Action
{
    protected $operatorInfo;

    protected $format = ['articleId','userId','actionType','ancestorShareNo','parentShareNo','shareNo','actionDate','actionMemo'];

    protected $timestamp;

    public function __construct()
    {
        $this->timestamp = time();
    }


    /**
     * 阅读逻辑
     * @param string $openid
     */
    public function view(string $openid)
    {
        //根据openid获得userId
        $User   = new User();
        $info   = $User->getInfoByOpenid($openid);

        //如果为空，需要生成$userId
        if (empty($info)) {
            $userId = Common::createUserId();
            //插入用户数据库
            $info   = ['openid' => $openid,'userId' => $userId,'status' => '3'];
            $User->insertOneInfo($info);
            //
        } else {
            $userId = $info['userId'];
        }
        $this->operatorInfo = [
            'userId'    => $userId,
            'timestamp' => $this->timestamp,
        ];
    }


    public function share($param)
    {
        //获得userId
        $openid     = $param['openid'];
        $User       = new User();
        $info       = $User->getInfoByOpenid($openid);
        if (empty($info)) {
            $userId = Common::createUserId();
            //插入用户数据库
            $info   = ['openid' => $openid,'userId' => $userId,'status' => '3'];
            $User->insertOneInfo($info);
        } else {
            $userId = $info['userId'];
        }

        $allNo      = $this->getNoByParentNo($param['parentNo']);
        $this->operatorInfo = [
            'userId'            => $userId,
            'actionType'        => $param['type'],
            'ancestorShareNo'   => $allNo['ancestorShareNo'],
            'parentShareNo'     => $allNo['parentShareNo'],
            'shareNo'           => $allNo['shareNo'],
            'timestamp'         => $this->timestamp,
        ];
    }





    public function getOperatorInfo()
    {
        return ArrayTool::formatArray($this->operatorInfo,$this->format,null);
    }

    /**
     * 通过父分享编号获得三者分享编号
     * @param $parenNo
     * @return array
     */
    protected function getNoByParentNo($parenNo): array
    {
        $shareNo    = $this->createShareNo();
        if (empty($parenNo)) {
            //三者都是当前分享编号
            return [
                'ancestorShareNo'   => $shareNo,
                'parentShareNo'     => $shareNo,
                'shareNo'           => $shareNo,
            ];
        } else {
            //通过父分享编号获得祖先分享号
            $UserAction = new UserAction();
            $ancestorNo = $UserAction->getAncestorNoByShareNo($parenNo);
            if (empty($ancestorNo)) {
                return [
                    'ancestorShareNo'   => $parenNo,
                    'parentShareNo'     => $parenNo,
                    'shareNo'           => $parenNo,
                ];
            }
            return [
                'ancestorShareNo'   => $ancestorNo,
                'parentShareNo'     => $parenNo,
                'shareNo'           => $shareNo,
            ];
        }
    }

    //获得父分享编号，及生成一个新的编号，针对第一次分享
    public function getParentNo()
    {
        $no = $this->createShareNo();
        return ['parentNo' => $no];
    }


    /**
     * 生成分享编号
     * @return int
     */
    protected function createShareNo(): int
    {
        //获得最新一个分享号
        $UserAction = new UserAction();
        $lastNo     = $UserAction->getLastShareNo();

        //拼接今日日期，yymmdd
        $date       = date('y-m-d',$this->timestamp);
        $nowDate    = substr($date,0,2).substr($date,3,2).substr($date,6,2);

        //获得最新一个分享号前6位
        $lastDate   = substr($lastNo,0,6);
        //最新分享号最后几位
        $last       = substr($lastNo,6);

        //如果日期没有变
        if ($nowDate == $lastDate) {
            return intval($lastNo) + 1;
        }
        //日期变了
        $need       = intval($last) + 1;
        $string     = StringTool::lFill($need,'0',4-strlen($need));
        return intval($nowDate.$string);
    }

}