<?php
namespace app\common\controller;

use app\common\logic\ArrayTool;
use app\common\model\Admin;
use app\common\model\AdminLog;
use app\common\service\Status;
use think\Request;

class BaseAdmin
{
    const LOGIN_EXPIRE_TIME = 7200;            //登陆有效期为2h，单位s

    /**
     * 登陆记录信息
     * @var array
     */
    protected $loginLogInfo = [];

    protected $request;

    protected $param;

    protected $timestamp;

    protected $userId;


    public function __construct()
    {
        $this->request      = Request::instance();
        $this->timestamp    = time();
        //$this->checkLogin();
        //去掉请求参数中的SESSION_ID
        $this->param = ArrayTool::removeKey($this->request->post(),'SESSION_ID');
    }


    //处理登陆信息，若不符合要求，直接输出信息
    protected function checkLogin()
    {
        //验证登陆信息
        $ret = $this->checkIsLogin();
        if (is_string($ret)) {
            $ret = Status::processStatus($ret);
            Status::returnJson($ret);
        }
    }

    //检验是否登陆
    public function checkIsLogin()
    {
        $AdminLog = new AdminLog();
        @$SESSION_ID = $this->request->post('SESSION_ID');

        //通过SESSION_ID获得登陆记录信息，
        $this->loginLogInfo = $AdminLog->getInfoBySESSION_ID($SESSION_ID);
        //登陆信息不能为空
        if (empty($this->loginLogInfo)) {
            return '30002';     //提示非法会话消息
        }

        //查看该SESSION_ID是否被注销
        if ($AdminLog->SESSION_IDIsLogout($SESSION_ID)) {
            return '30004';         //提示登录已注销
        }

        //通过userId获得最新一次登录记录信息
        @$this->userId = $this->loginLogInfo['userId'];
        $newLoginLogInfo = $AdminLog->getLastInfoByUserId($this->userId);
        if (empty($newLoginLogInfo) || empty($newLoginLogInfo['msg'])) {
            return '20001';     //提示系统错误
        }

        $msg = json_decode($newLoginLogInfo['msg'],true);
        //如果两个session_id不相等
        if ($SESSION_ID !== $msg['SESSION_ID']) {
            return '30003';         //提示会话已过期
        }
        //判断有效期
        if (!isset($msg['updateTime']) || $msg['updateTime'] + self::LOGIN_EXPIRE_TIME < $this->timestamp) {
            return '30003';
        }

        $info = Admin::getSelf(['userId' => $this->userId]);
        //验证userId是否是管理员
        if (empty($info)) {
            return '30007';
        }
        //验证userId的状态
        if(!isset($info['status']) || $info['status'] != 1) {
            return '30008';
        }
        //更新会话时间
        if (!$AdminLog->updateActionTime($newLoginLogInfo,$this->timestamp)) {
            return '20003';     //提示系统开小差了
        }

        unset($newLoginLogInfo,$msg);
        return true;
    }


}