<?php
namespace App\system\controller;

use app\common\service\Status;
use app\common\service\WriteLog;
use app\system\logic\System;
use EasyWeChat\Factory;
use think\Loader;
use think\Request;

class Index
{
    protected $request;
    public function __construct()
    {
        $this->request = Request::instance();
    }

    /**
     * @SWG\Post(
     *     path="/system/index/login",
     *     tags={"System"},
     *     operationId="systemLogin",
     *     summary="管理员登陆接口（用户id+密码）",
     *     description="普通管理员及超管登陆该系统，进行相关操作",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="userId",
     *         in="formData",
     *         description="管理员内部唯一标识符",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="登录密码",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //登陆
    public function login()
    {
        //验证数据
        $validate   = Loader::validate('Admin');
        if (!$validate->checkLogin($this->request->post())) {
            return json(Status::processValidateMsg($validate->getError()));
        }
        //逻辑处理
        $System = new System();
        $ret    = $System->login($this->request);
        //记录操作日志
        //获得操作者等信息
        $operatorInfo = $System->getInitInfo();
        $msg = array_merge($this->request->post(),['ip' => $this->request->ip()]);
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::ADMIN,WriteLog::LOGIN,$msg);

        //加工返回信息
        $res = Status::processStatus($ret);
        return json($res);
    }






}