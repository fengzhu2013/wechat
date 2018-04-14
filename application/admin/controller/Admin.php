<?php
namespace app\admin\controller;

use app\admin\logic\Scene;
use app\admin\logic\System;
use app\common\logic\FormatString;
use app\common\model\AdminLog;
use app\common\service\Status;
use app\common\service\WriteLog;
use think\Controller;
use think\Db;
use think\Request;

class Admin
{

    const LOGIN_EXPIRE_TIME = 7200;            //登陆有效期为2h，单位s

    /**
     * 是否登陆
     * @var bool
     */
    private $isLogin = false;

    /**
     * 登陆信息
     * @var array
     */
    private $loginInfo = [];

    /**
     * 登陆记录信息
     * @var array
     */
    private $loginLogInfo = [];

    /**
     * 登陆的身份信息是否拥有调用action的权限
     * @var bool
     */
    private $isPower = false;

    /**
     * 是否有效
     * @var bool
     */
    private $isExpire = false;

    protected $request;

    private $timestamp;


    private $AdminLogObj;

    private $userId;

    private $param = [];

    public function __construct()
    {
        $this->request = Request::instance();
        $this->timestamp = time();
        $this->checkLogin();
    }

    /**
     * 处理登陆信息，若不符合要求，直接输出信息
     */
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
        $this->AdminLogObj = new AdminLog();
        @$SESSION_ID = $this->request->post('SESSION_ID');

        //通过SESSION_ID获得登陆记录信息，
        $this->loginLogInfo = $this->AdminLogObj->getInfoBySESSION_ID($SESSION_ID);
        //登陆信息不能为空
        if (empty($this->loginLogInfo)) {
            return '30002';     //提示非法会话消息
        }

        //查看该SESSION_ID是否被注销
        if ($this->AdminLogObj->SESSION_IDIsLogout($SESSION_ID)) {
            return '30004';         //提示登录已注销
        }

        //通过userId获得最新一次登录记录信息
        @$this->userId = $this->loginLogInfo['userId'];
        $newLoginLogInfo = $this->AdminLogObj->getLastInfoByUserId($this->userId);
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

        //更新会话时间
        if (!$this->AdminLogObj->updateActionTime($newLoginLogInfo,$this->timestamp)) {
            return '20003';     //提示系统开小差了
        }

        //去掉请求参数中的SESSION_ID
        $this->param = array_diff($this->request->post(),['SESSION_ID' => $SESSION_ID]);
        unset($newLoginLogInfo,$msg);
        return true;
    }




    //注销
    public function logout()
    {
        $logInfo = [
            'userId'        => $this->userId,
            'objectId'      => $this->loginLogInfo['objectId'],
            'objectType'    => 'admin',
            'action'        => 'r',
            'createTime'    => $this->timestamp,
            'msg'           => json_encode($this->request->post()),
            'status'        => 2
        ];
        //插入一条r记录
        $AdminLogObj = new AdminLog($logInfo);
        if ($AdminLogObj->saveSelf()) {
            $res = Status::processStatus(true);
        } else {
            $res = Status::processStatus(false);
        }
        return json($res);
    }

    //添加微信系统必要信息
    public function addSystemInfo()
    {
        //添加信息
        $SystemObj = new System($this->loginLogInfo);
        $ret = $SystemObj->addSystemInfo($this->param);

        //记录操作日志
        $operatorInfo = $SystemObj->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,'system','a',$this->request->post());

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    //修改微信系统信息
    public function modifySystemInfo()
    {
        $SystemObj = new System($this->loginLogInfo);
        $ret = $SystemObj->modifySystemInfo($this->param);

        //记录操作日志
        $operatorInfo = $SystemObj->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,'system','u',$this->request->post());

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    //获得微信系统信息
    public function getSystemInfo()
    {
        $SystemObj = new System();
        $ret = $SystemObj->getSystemInfo($this->param);

        //不记录操作日志

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    //获得用户信息
    public function getUserInfo()
    {
        $SystemObj = new System($this->loginLogInfo);
        $ret = $SystemObj->getUserInfo($this->param);

        //不记录日志信息

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    //修改用户信息
    public function modifyUserInfo()
    {
        $SystemObj = new System($this->loginLogInfo);
        $ret = $SystemObj->modifyUserInfo($this->param);

        //记录日志
        $operatorInfo = $SystemObj->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,'admin','u',$this->request->post());

        //返回信息
        $res = Status::processStatus($ret);
        return json($res);
    }

    //添加管理员
    public function addAdminUser()
    {
        $SystemObj = new System($this->loginLogInfo);
        $ret = $SystemObj->addAdminUser($this->param);

        //记录日志
        $operatorInfo = $SystemObj->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,'admin','a',$this->request->post());

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    //获得管理员列表
    public function getAdminList()
    {
        $SystemObj = new System($this->loginLogInfo);
        $ret = $SystemObj->getAdminList($this->param);

        //不用记录日志

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    //删除一个管理员
    public function deleteAdmin()
    {

    }

    //自定义关注时或默认回复
    public function setSubAndDefInfo()
    {

    }

    //获得关注时或默认回复信息
    public function getSubAndDefInfo()
    {

    }

    //修改关注时或默认回复信息
    public function modifySubAndDefInfo()
    {

    }

    //删除默认，关注或关键词回复信息
    public function deleteReplyInfo()
    {

    }

    //新增关键词回复信息
    public function addKeywordsInfo()
    {

    }

    //获得关键词回复信息
    public function getKeywordsInfo()
    {

    }

    //获得关键词列表
    public function getKeywordsList()
    {

    }

    //修改关键词信息
    public function modifyKeywordsInfo()
    {

    }

    //新增菜单
    public function addMenu()
    {

    }

    //获得菜单
    public function getMenu()
    {

    }

    //修改菜单
    public function modifyMenu()
    {

    }

    //删除菜单
    public function deleteMenu()
    {

    }


    //获得文章列表
    public function getArticleList()
    {

    }

    //获得一篇文章的详情
    public function getArticle()
    {

    }

    //修改一篇文章
    public function modifyArticle()
    {

    }

    //删除一片文章
    public function deleteArticle()
    {

    }

    //添加一篇文章
    public function addArticle()
    {

    }

    //活动已有留言的文章列表
    public function getArticleHasCom()
    {

    }

    //根据文章获得留言
    public function getComByArticle()
    {

    }


    //根据关注时间获得用户列表
    public function getUserListBySubTime()
    {

    }


    //根据活跃度获得用户列表
    public function getUserListByActivity()
    {

    }

    //根据评论数获得用户列表
    public function getUserListByComCount()
    {

    }

    //获得渠道原始数据
    public function getSceneLogList()
    {

    }

    //获得渠道统计数据
    public function getSceneCountList()
    {

    }

    //获得某个渠道的记录列表
    public function getOneSceneList()
    {

    }

    //获得最具影响的几个人
    public function getEffectPersonList()
    {

    }

    //获得高频词列表
    public function getHighWordsList()
    {

    }


    //获得素材列表
    public function  getMediaList()
    {

    }


    //获得素材
    public function getMedia()
    {

    }









}