<?php
namespace app\system\controller;

use app\common\controller\BaseAdmin;
use app\common\service\Status;
use app\common\service\WriteLog;
use app\system\logic\System;
use app\system\model\AdminLog;

class Admin extends BaseAdmin
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/system/admin/logout",
     *     tags={"System"},
     *     operationId="logout",
     *     summary="注销登录",
     *     description="管理员注销登录",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
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
        $AdminLog = new AdminLog($logInfo);
        if ($AdminLog->saveSelf()) {
            $res = Status::processStatus(true);
        } else {
            $res = Status::processStatus(false);
        }
        return json($res);
    }


    /**
     * @SWG\Post(
     *     path="/system/admin/addSystemInfo",
     *     tags={"System"},
     *     operationId="addSystemInfo",
     *     summary="添加微信系统必要信息",
     *     description="添加微信系统必要信息，如appid等，适用超级管理员",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="appid",
     *         in="formData",
     *         description="微信appid",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="appsecret",
     *         in="formData",
     *         description="微信 appsecret",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="token",
     *         in="formData",
     *         description="微信token",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="aesKey",
     *         in="formData",
     *         description="密钥",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //添加微信系统必要信息
    public function addSystemInfo()
    {
        //添加信息
        $System = new System($this->loginLogInfo);
        $ret    = $System->addSystemInfo($this->param);

        //记录操作日志
        $operatorInfo = $System->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,'system','a',$this->request->post());

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/system/admin/modifySystemInfo",
     *     tags={"System"},
     *     operationId="modifySystemInfo",
     *     summary="修改微信系统必要信息",
     *     description="修改微信系统必要信息，如appid等,适用超级管理员",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="formData",
     *         description="记录id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="appid",
     *         in="formData",
     *         description="微信appid",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="appsecret",
     *         in="formData",
     *         description="微信 appsecret",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="token",
     *         in="formData",
     *         description="微信token",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="aesKey",
     *         in="formData",
     *         description="密钥",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //修改微信系统信息
    public function modifySystemInfo()
    {
        $System = new System($this->loginLogInfo);
        $ret    = $System->modifySystemInfo($this->param);

        //记录操作日志
        $operatorInfo = $System->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,'system','u',$this->request->post());

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/system/admin/getSystemInfo",
     *     tags={"System"},
     *     operationId="getSystemInfo",
     *     summary="获得微信系统必要信息",
     *     description="获得微信系统必要信息，如appid等，适用所有管理员",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得微信系统信息
    public function getSystemInfo()
    {
        $System = new System();
        $ret    = $System->getSystemInfo($this->param);

        //不记录操作日志

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/system/admin/getUserInfo",
     *     tags={"System"},
     *     operationId="getUserInfo",
     *     summary="获得用户信息",
     *     description="获得用户信息，如adminName等,适用所有管理员",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="userId",
     *         in="formData",
     *         description="用户Id",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得用户信息
    public function getUserInfo()
    {
        $System = new System($this->loginLogInfo);
        $ret    = $System->getUserInfo($this->param);

        //不记录日志信息

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }


    /**
     * @SWG\Post(
     *     path="/system/admin/modifyUserInfo",
     *     tags={"System"},
     *     operationId="modifyUserInfo",
     *     summary="修改用户信息",
     *     description="修改用户信息，如adminName等,只有超级管理员才能修改其他管理员的信息",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="userId",
     *         in="formData",
     *         description="用户Id",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         name="adminName",
     *         in="formData",
     *         description="用户名",
     *         required=false,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="用户密码",
     *         required=false,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         name="status",
     *         in="formData",
     *         description="用户状态",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //修改用户信息
    public function modifyUserInfo()
    {
        $System = new System($this->loginLogInfo);
        $ret    = $System->modifyUserInfo($this->param);

        //记录日志
        $operatorInfo = $System->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,'admin','u',$this->request->post());

        //返回信息
        $res = Status::processStatus($ret);
        return json($res);
    }


    /**
     * @SWG\Post(
     *     path="/system/admin/addAdminUser",
     *     tags={"System"},
     *     operationId="addAdminUser",
     *     summary="增加管理员",
     *     description="增加一个普通管理员，针对超级管理员",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="userId",
     *         in="formData",
     *         description="用户Id",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         name="adminName",
     *         in="formData",
     *         description="用户名",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="用户密码",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //添加管理员
    public function addAdminUser()
    {
        $System = new System($this->loginLogInfo);
        $ret    = $System->addAdminUser($this->param);

        //记录日志
        $operatorInfo = $System->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,'admin','a',$this->request->post());

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }


    /**
     * @SWG\Post(
     *     path="/system/admin/getAdminList",
     *     tags={"System"},
     *     operationId="getAdminList",
     *     summary="获得管理员列表",
     *     description="获得管理员列表，针对超级管理员，用于查看或修改信息",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="page",
     *         in="formData",
     *         description="当前页",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="pageSize",
     *         in="formData",
     *         description="页容量",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得管理员列表
    public function getAdminList()
    {
        $System = new System($this->loginLogInfo);
        $ret    = $System->getAdminList($this->param);

        //不用记录日志

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }






}