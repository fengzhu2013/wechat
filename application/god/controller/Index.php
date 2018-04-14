<?php
namespace app\god\controller;

use app\common\service\Status;
use app\common\service\WriteLog;
use app\scene\logic\Scene;
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
     *     path="/god/index/getQrCodeCount",
     *     tags={"God"},
     *     operationId="getOrCodeCount",
     *     summary="从微信服务器中获取一定数量的二维码",
     *     description="从微信服务器中获取一定数量的二维码，提前获取",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="count",
     *         in="formData",
     *         description="数量",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //从微信服务器中获取一定数量的二维码
    public function getQrCodeCount()
    {
        $Scene          = new Scene();
        $ret            = $Scene->getOrCodeCount($this->request->post());

        //记录日志
        $operatorInfo   = $Scene->getInitInfo();
        $msg            = $Scene->getActionMsg();
        WriteLog::writeLog($ret,$operatorInfo,'scene','m_a',$msg);

        //return
        $res            = Status::processStatus($ret);
        return json($res);
    }
}