<?php
namespace app\admin\controller;

use app\admin\logic\System;
use app\common\service\Status;
use app\common\service\WriteLog;
use think\Request;

class Index
{

    protected $request;

    public function __construct()
    {
    }

    /**
     * @SWG\Post(
     *     path="/admin/index/systemLogin",
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
    /**
     * 后台管理员登陆
     * @param Request $request
     * @return \think\response\Json
     */
    public function systemLogin(Request $request)
    {
        $request = $request::instance();
        $obj = new System();
        $ret = $obj->login($request);
        //记录操作日志
        //获得操作者等信息
        $operatorInfo = $obj->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,'admin','l',$request::instance()->post());

        //加工返回信息
        $res = Status::processStatus($ret);
        return json($res);
    }




}