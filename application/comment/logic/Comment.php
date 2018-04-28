<?php
namespace app\comment\logic;

use app\comment\model\MassLog;
use app\comment\model\User;
use app\common\logic\BaseLogic;
use app\common\logic\Common;
use app\common\logic\Date;
use app\common\service\WriteLog;
use app\common\service\WriteOpenidList;
use app\wechat\service\DataCube;

class Comment extends BaseLogic
{
    public function __construct(array $loginLogInfo = [], $isVerifyType = false)
    {
        parent::__construct($loginLogInfo, $isVerifyType);
    }

    //获取群发日志
    public function getMassLog($param)
    {
        $startDate  = $param['startDate'];
        $endDate    = $startDate;
        if (isset($param['endDate'])) {
            $endDate = $param['endDate'];
        }

        //确定两个日期相隔天数
        $diffDay    = Date::diffDate($endDate,$startDate);
        if ($diffDay < 0) {
            $temp       = $startDate;
            $startDate  = $endDate;
            $endDate    = $temp;
            $diffDay    = abs($diffDay);
        }

        $MassLog    = new MassLog();
        //开始循环抓取,倒者来抓取
        for ($i=0;$i <= $diffDay;$i++) {
            //查看该数据库中是否存在该日期
            $where  = ['massDate' => $endDate];
            $info   = $MassLog->getInfo($where);
            if (empty($info)) {
                //抓取群发信息
                $DataCube = new DataCube();
                $response = $DataCube->getArticleTotal($endDate);
                //请求错误，结束程序
                if (!isset($response['list'])) {
                    $ret = ['response' => $response,'date' => $endDate];
                    return $ret;
                }
                //存储数据
                if (empty($response['list'])) {
                    $info = ['mass_date' => $endDate];
                    $ret  = $MassLog->insertInfo($info);
                    if (!$ret) {
                        //记录错误日志
                        WriteLog::writeMassErrLog($info,WriteLog::ADD);
                    }
                } else {
                    foreach ($response['list'] as $val) {
                        @$data = ['msgid' => $val['msgid'],'title' => $val['title'],'mass_date' => $endDate,'status' => '1'];
                        $ret   = $MassLog->insertInfo($data);
                        if (!$ret) {
                            //记录错误日志
                            WriteLog::writeMassErrLog($data,WriteLog::ADD);
                        }
                    }
                }
            }
            //日期减少一天
            $endDate = Date::reduceOneDay($endDate);
        }
        return true;
    }


    //获得群发文章列表
    public function getArticleList($param)
    {
        //获得数据
        $MassLog        = new MassLog();
        $map['title']   = ['neq',''];
        $order          = ['id' => 'asc'];

        //获得总数
        $count          = $MassLog->getCount($map);
        if (!$count) {
            return '50006';         //提示没有信息
        }

        //获得具体信息
        $info           = $MassLog->getPage($param,$map,$order);
        $ret            = [
            'data'  => $info,
            'count' => $count,
        ];
        return $ret;
    }


    //获得留言
    public function getComment()
    {
        $MassLog    = new MassLog();

        //处理未处理的数据
        while (true) {
            //一次获取两条未处理中的数据
            $map['status']  = ['eq','1'];
            $map['title']   = ['neq',''];
            $order          = ['id'   => 'asc'];
            $param          = ['page' => 1,'pageSize' => 2];
            $info           = $MassLog->getPage($param,$map,$order);
            if (empty($info)) {
                break;
            }

            //修改状态
            $info[0]['status'] = '2';
            if (isset($info[1]['status'])) {
                $info[1]['status'] = '2';
            }
            $ret               = $MassLog->updateMoreInfo($info);
            if (!$ret) {
                WriteLog::writeMassErrLog($info,WriteLog::UPDATE);
                break;
            }

            //爬取留言
            foreach ($info as $val) {
                $msgid       = $val['msgid'];
                $msg_data_id = intval(substr($msgid,0,strpos($msgid,'_')));
                $index       = intval(str_replace($msg_data_id.'_',null,$msgid)) - 1;

                $where      = ['msgid' => $msgid];
                $order      = ['comId' => 'desc'];
                $Comment    = new \app\comment\model\Comment();
                $oneInfo    = $Comment->getOneComment($where,$order);
                if (empty($oneInfo)) {
                    $lastId = 0;
                } else {
                    $lastId = $oneInfo['comId'];
                }
                $ret         = $this->firstGetComment($msg_data_id,$index,0,49,0,$lastId);
                if (is_array($ret)) {
                    //错误信息
                    WriteLog::writeCommentLog($ret,WriteLog::MORE_ADD);
                }
            }

            //修改状态为已处理'3'
            $info[0]['status'] = '3';
            if (isset($info[1]['status'])) {
                $info[1]['status'] = '3';
            }
            $ret               = $MassLog->updateMoreInfo($info);
            if (!$ret) {
                //记录错误日志
                WriteLog::writeMassErrLog($info,WriteLog::UPDATE);
            } else {
                $operatorInfo = $this->getInitInfo();
                WriteLog::writeLog(true,$operatorInfo,WriteLog::COMMENT,WriteLog::MORE_ADD,$info);
            }
        }
        return true;
    }

    /**
     * 第一次抓取评论
     * @param $msg_data_id
     * @param $index
     * @param int $begin
     * @param int $count
     * @param int $type
     * @param int $lastId
     * @return bool
     */
    protected function firstGetComment($msg_data_id,$index,$begin = 0,$count = 49,$type = 0,$lastId)
    {
        $last       = $index + 1;
        $msgid      = $msg_data_id.'_'.$last;
        $Comment    = new \app\wechat\service\Comment();
        $list       = $Comment->getCommentList($msg_data_id,$index,$begin,$count,$type);
        //有问题
        if (isset($list['errcode']) && $list['errcode']) {
            return $list;
        }
        if (!isset($list['comment'])) {
            return $list;
        }
        if (empty($list['comment'])) {
            return true;
        }
        //如果不是第一次抓取
        if ($lastId) {
            $nowId      = $list['comment'][0]['user_comment_id'];
            if ($nowId <= $lastId) {
                //不再抓取
                return true;
            }
            $num = $nowId - $lastId;
            if ($num < $count) {
                $list['comment'] = array_slice($list,0,$num);
            }
        }
        $CommentObj = new \app\comment\model\Comment();
        $needInfo   = $this->handleCommentInfo($list['comment'],$msgid);

        //插入数据
        $CommentObj->insertAllInfoSelf($needInfo);
        /*foreach ($needInfo as $key => $val) {
            @$ret = $CommentObj->insertInfo($val);
            if (!$ret) {
                //记录日志
                WriteLog::writeCommentLog($val,WriteLog::ADD);
            }
        }*/
        //判断是否循环
        if (count($needInfo) < $count) {
            return true;
        }
        //开始循环
        $begin += $count;
        $this->firstGetComment($msg_data_id,$index,$begin,$count,$type);
    }

    protected function secondGetComment($msg_data_id,$index,$begin = 0,$count = 49,$type = 0,$lastId)
    {
        $last       = $index + 1;
        $msgid      = $msg_data_id.'_'.$last;
        $Comment    = new \app\wechat\service\Comment();
        $list       = $Comment->getCommentList($msg_data_id,$index,$begin,$count,$type);
        //有问题
        if (isset($list['errcode']) && $list['errcode']) {
            return $list;
        }
        if (!isset($list['comment'])) {
            return $list;
        }
        if (empty($list['comment'])) {
            return true;
        }
        $indexId = $list['comment'][0]['user_comment_id'];
        if ($indexId <= $lastId) {
            //不插入数据
            return true;
        }
        $num = $indexId - $lastId;
        if ($num < $count) {
            $needArr = array_slice($list['comment'],0,$num);
            $needInfo = $this->handleCommentInfo($needArr,$msgid);
        } else {
            $needInfo = $this->handleCommentInfo($list['comment'],$msgid);
        }
        $CommentObj = new \app\comment\model\Comment();
        foreach ($needInfo as $key => $val) {
            @$ret = $CommentObj->insertInfo($val);
            if (!$ret) {
                //记录日志
                WriteLog::writeCommentLog($val,WriteLog::ADD);
            }
        }
        if ($num < $count + 1 || $list['total'] < $count) {
            return true;
        }

        //开始循环
        $begin += $count;
        $this->secondGetComment($msg_data_id,$index,$begin,$count,$type,$lastId);
    }

    /**
     * 格式化信息，处理成可以直接插入comment表的数据
     * @param array $info
     * @param string $msgid
     * @return array
     */
    protected function handleCommentInfo(array $info,string $msgid): array
    {
        $ret = [];
        foreach ($info as $key => $val) {
            $ret[$key] = [
                'msgid'         => $msgid,
                'comContent'    => $val['content'],
                'createTime'    => $this->timestamp,
                'comDate'       => date('Y-m-d H:i:s',$val['create_time']),
                'comId'         => $val['user_comment_id'],
            ];
            //确认userId
            $User   = new User();
            $userId = $User->getUserIdByOpenid($val['openid']);
            if (!$userId) {
                $userId = Common::createUserId();
                //插入user表中
                $info   = ['userId' => $userId,'openid' => $val['openid'],'status' => '3'];
                $User->insertInfo($info);

                //把openid写入openidList表中
                $list   = ['openid' => $val['openid'],'createTime' => $this->timestamp];
                WriteOpenidList::writeLog($list);
            }
            $ret[$key]['userId'] = $userId;
        }
        return $ret;
    }



    //根据文章获得留言
    public function getComByArticle($param)
    {
        //验证msgid是否存在
        $msgid      = $param['msgid'];
        $MassLog    = new MassLog();
        $where      = ['msgid' => $msgid];
        $info       = $MassLog->getInfo($where);
        if (empty($info)) {
            return '40004';
        }

        //如果群发日期超过7天
        //$today      = date('Y-m-d');
        $Comment    = new \app\comment\model\Comment();
        $order      = ['comId' => 'desc'];
        //获得一条评论信息
        $oneInfo    = $Comment->getOneComment($where,$order);
        if (empty($oneInfo)) {
            $oneInfo['comId'] = 0;
        }
        //如果是在发布文章3天内爬取的留言
        if (Date::diffDate($oneInfo['createTime'],$info['massDate']) < 3) {
            //重新获取可能多的数据
            $msg_data_id = intval(substr($msgid,0,strpos($msgid,'_')));
            $index       = intval(str_replace($msg_data_id.'_',null,$msgid)) - 1;
            $this->secondGetComment($msg_data_id,$index,0,49,0,$oneInfo['comId']);
        }

        //获取数据
        $needCount  = $Comment->getCount($where);
        if (!$needCount) {
            return '50006';
        }
        $needInfo   = $Comment->getPage($param,$where,$order);
        $ret        = [
            'data'  => $needInfo,
            'count' => $needCount,
        ];
        return $ret;
    }


    protected function handlemsgId(string $msgid)
    {
        $MassLog    = new MassLog();
        $where      = ['msgid' => $msgid];
        $info       = $MassLog->getInfo($where);
        if (empty($info)) {
            return '40004';
        }

        //验证群发日期




    }











}