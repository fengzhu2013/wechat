<?php
namespace app\god\controller;

use app\common\logic\Common;
use app\common\service\Status;
use app\common\service\WriteLog;
use app\scene\logic\Scene;
use app\wechat\logic\ResponseMsg;
use EasyWeChat\Kernel\Messages\Article;
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
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::SCENE,WriteLog::MORE_ADD,$msg);

        //return
        $res            = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/god/index/uploadImg",
     *     tags={"God"},
     *     operationId="uploadImg",
     *     summary="上传图片素材",
     *     description="上传图片素材",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    public function uploadImg()
    {
        $Index  = new \app\wechat\controller\Index();
        $app    = $Index->getApp();
        $result = $app->material->uploadImage(ROOT_PATH.DS."public".DS.'qrcode'.DS.'2.jpg');

        //return
        $res            = Status::processStatus($result);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/god/index/uploadArticle",
     *     tags={"God"},
     *     operationId="uploadArticle",
     *     summary="上传图文素材",
     *     description="上传图文素材",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    public function uploadArticle()
    {
        $Index  = new \app\wechat\controller\Index();
        $app    = $Index->getApp();
        // 上传单篇图文
        $article = new Article([
            'title' => 'xxx',
            'thumb_media_id' => '3rWvOg_4rmTQbJ6S1wHOh9TB-gvMmKZdQkgjmSmnx8g',
            'show_cover'     => 1,
            'content'        => 'test something!',
            'source_url'     => 'https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738729',
        ]);
        $mediaId = $app->material->uploadArticle($article);
        $ret = ['mediaId' => $mediaId];
        $res = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/god/index/responseSubscribe",
     *     tags={"God"},
     *     operationId="responseSubscribe",
     *     summary="查看关注时回复",
     *     description="查看关注时回复",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    public function responseSubscribe()
    {
        $Index = new \app\wechat\controller\Index();

        $ResponseMsg = new ResponseMsg();
        //$ret   = \app\wechat\logic\Common::getUserIdByOpenId('ohnwgw1-6Sc_f5qH703eRBGsjnnU');
        $ret = $ResponseMsg->responseMenuKey('tf202');
        var_dump($ret);

    }




}