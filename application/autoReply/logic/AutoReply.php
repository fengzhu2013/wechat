<?php
namespace app\autoReply\logic;

use app\common\logic\ArrayTool;
use app\common\logic\BaseLogic;
use function Couchbase\fastlzCompress;
use think\Loader;

class AutoReply extends BaseLogic
{

    public function __construct($loginLogInfo = [])
    {
        parent::__construct($loginLogInfo);
    }

    //自定义关注时或默认回复
    public function setSubAndDefInfo($param)
    {
        //处理数据
        if ($param['key'] == 'video' || $param['key'] == 'news') {
            $param['value'] = json_encode($param['value']);
        }
        $info = [
            'replyKey'      => $param['key'],
            'replyType'     => $param['type'],
            'replyValue'    => $param['value'],
        ];
        //插入数据库
        $AutoReply = new \app\autoReply\model\AutoReply();
        $ret       = $AutoReply->insertInfo($info);
        if (!$ret) {
            return false;
        }
        $this->initLog(['id' => $ret]);
        return true;
    }

    //获得关注时或默认回复信息
    public function getSubAndDefInfo($param)
    {
        //获得数据
        $AutoReply = new \app\autoReply\model\AutoReply();
        $where     = ['reply_type' => $param['type']];
        $ret       = $AutoReply->getOneInfoDesc($where);
        if (empty($ret)) {
            return '50006';
        }
        return $ret;
    }

    //修改关注时或默认回复信息
    public function modifySubAndDefInfo($param)
    {
        //验证id是否存在
        $AutoReply  = new \app\autoReply\model\AutoReply();
        $where      = ['id' => $param['id']];
        $info       = $AutoReply->getOneInfo($where);
        if (empty($info)) {
            return '40004';         //识别标识符错误或不存在
        }

        //处理数据
        if ($param['key'] == 'video' || $param['key'] == 'news') {
            $param['value'] = json_encode($param['value']);
        }
        //修改信息
        $info = [
            'replyKey'      => $param['key'],
            'replyValue'    => $param['value'],
        ];
        $ret        = $AutoReply->modifyInfo($info,$where);
        if (empty($ret)) {
            return '10001';
        }
        $this->initLog($where);
        return true;
    }


    //删除自动回复信息
    public function deleteReplyInfo($param)
    {
        //验证id是否存在
        $AutoReply  = new \app\autoReply\model\AutoReply();
        $where      = ['id' => $param['id']];
        $info       = $AutoReply->getOneInfo($where);
        if (empty($info)) {
            return '40004';
        }

        //删除数据
        if (!$AutoReply->deleteInfo($where)) {
            return false;
        }
        $this->initLog($where);
        return true;
    }

    //新增关键词
    public function addKeywordsInfo($param)
    {
        //处理数据
        if ($param['key'] == 'video' || $param['key'] == 'news') {
            $param['value'] = json_encode($param['value']);
        }

        $info = [
            'replyType'     => $param['type'],
            'replyValue'    => $param['value'],
            'replyKey'      => $param['key'],
            'startTime'     => $param['startTime'],
            'endTime'       => $param['endTime'],
            'keywords'      => $param['keywords'],
        ];

        //插入数据
        $AutoReply = new \app\autoReply\model\AutoReply();
        $ret = $AutoReply->insertInfo($info);

        if (!$ret) {
            return false;
        }
        $this->initLog(['id' => $ret]);
        return true;
    }

    //获得关键词列表
    public function getKeywordsList()
    {
        //获取所有的关键词信息，倒叙排列
        $AutoReply  = new \app\autoReply\model\AutoReply();
        $info       = $AutoReply->getKeywordsList();
        if (empty($info)) {
            return '50006';     //没有记录信息
        }
        return $info;
    }


    //获得关键词信息
    public function getKeywordsInfo($param)
    {
        //验证id是否存在
        $AutoReply  = new \app\autoReply\model\AutoReply();
        $where      = ['id' => $param['id']];
        $info       = $AutoReply->getOneInfo($where);
        if (empty($info)) {
            return '40004';
        }

        //验证是否是关键词
        if (!isset($info['replyType']) || !in_array($info['replyType'],['3','4'])) {
            return '40004';
        }

        //返回信息
        return $info;
    }

    //修改关键词信息
    public function modifyKeywordsInfo($param)
    {
        //验证id是否存在
        $AutoReply  = new \app\autoReply\model\AutoReply();
        $where      = ['id' => $param['id']];
        $info       = $AutoReply->getOneInfo($where);

        if (empty($info)) {
            return '40004';
        }

        //验证是否是关键词
        if (!isset($info['replyType']) || !in_array($info['replyType'],['3','4'])) {
            return '40004';
        }

        //处理数据
        if (isset($param['key'])) {
            $data['replyKey']   = $param['key'];
        }
        if ((isset($param['key']) && isset($param['value'])) && ($param['key'] == 'video' || $param['key'] == 'news')) {
            $data['replyValue'] = json_encode($param['value']);
        }
        if (isset($param['value']) && !isset($param['key'])) {
            if ($info['replyKey'] == 'video' || $info['replyKey'] == 'news') {
                $data['replyValue'] = json_encode($param['value']);
            }
        }
        if (isset($param['type'])) {
            $data['replyType'] = $param['type'];
        }
        if (isset($param['startTime'])) {
            $data['startTime'] = $param['startTime'];
        }
        if (isset($param['endTime'])) {
            $data['endTime'] = $param['endTime'];
        }
        if (isset($param['keywords'])) {
            $data['keywords'] = $param['keywords'];
        }
        $data['id'] = $param['id'];

        //验证是否有更改信息
        if (ArrayTool::compareArr($data,$info)) {
            return '50005';
        }

        $ret = $AutoReply->modifyInfo($data,$where);
        if (!$ret) {
            return false;
        }
        $this->initLog($where);
        return true;
    }





}