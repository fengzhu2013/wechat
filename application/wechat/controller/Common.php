<?php
namespace app\wechat\controller;


use app\common\controller\BaseAdmin;
use app\common\service\Status;
use app\wechat\logic\Media;

class Common extends BaseAdmin
{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @SWG\Post(
     *     path="/wechat/common/getMediaList",
     *     tags={"Common"},
     *     operationId="getMediaList",
     *     summary="获得素材列表",
     *     description="获得素材列表",
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
     *         name="type",
     *         in="formData",
     *         description="素材类型",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="offset",
     *         in="formData",
     *         description="偏移量",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="count",
     *         in="formData",
     *         description="获取的数量",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得素材列表
    public function  getMediaList()
    {
        $Media = new Media();
        $ret   = $Media->getMediaList($this->param);

        //返回信息
        $res   = Status::processStatus($ret);
        return json($res);
    }


    /**
     * @SWG\Post(
     *     path="/wechat/common/getMedia",
     *     tags={"Common"},
     *     operationId="getMedia",
     *     summary="获得素材",
     *     description="获得素材",
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
     *         name="mediaId",
     *         in="formData",
     *         description="素材id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得素材
    public function getMedia()
    {
        $Media = new Media();
        $ret   = $Media->getMedia($this->param);

        //返回信息
        $res   = Status::processStatus($ret);
        return json($res);
    }






}