<?php
namespace app\comment\controller;

use app\comment\logic\Comment;
use app\common\service\Status;
use app\common\service\WriteLog;
use think\Loader;
use think\Request;

class Index
{
    protected $request;

    protected $param;

    public function __construct()
    {
        $this->request  = Request::instance();
        $this->param    = $this->request->param();
    }

    /**
     * @SWG\Post(
     *     path="/comment/index/getMassLog",
     *     tags={"Comment"},
     *     operationId="getMassLog",
     *     summary="获得群发日信息",
     *     description="获得群发日信息，获得群发日的群发文章id及title",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="startDate",
     *         in="formData",
     *         description="开始日期",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="endDate",
     *         in="formData",
     *         description="结束日期",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得群发日信息
    public function getMassLog()
    {
        //验证数据
        $validate = Loader::validate('Comment');
        if (!$validate->checkGetMassLog($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //设置时效
        ignore_user_abort(true);
        set_time_limit(0);

        //逻辑处理
        $Comment    = new Comment();
        $ret        = $Comment->getMassLog($this->param);

        //记录日志
        $operatorInfo = $Comment->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::MASS_LOG,WriteLog::MORE_ADD,$this->param);

        $res = Status::processStatus($ret);
        return json($res);
    }


    /**
     * @SWG\Post(
     *     path="/comment/index/getComment",
     *     tags={"Comment"},
     *     operationId="getComment",
     *     summary="获得留言",
     *     description="获得留言",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得留言
    public function getComment()
    {
        //设置时效
        ignore_user_abort(true);
        set_time_limit(0);

        //逻辑处理
        $Comment    = new Comment();
        $ret        = $Comment->getComment();

        $res        = Status::processStatus($ret);
        return json($res);
    }





}