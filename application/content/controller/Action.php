<?php
namespace app\content\controller;

use app\common\service\Status;
use app\common\service\WriteUserAction;
use app\wechat\service\Oauth;
use think\Loader;
use think\Request;

class Action
{
    protected $request;

    protected $param;

    public function __construct()
    {
        $this->request  = Request::instance();
        $this->param    = $this->request->post();
    }

    /**
     * @SWG\Post(
     *     path="/content/action/getBaseCode",
     *     tags={"Content"},
     *     operationId="getBaseCode",
     *     summary="获得openid的url",
     *     description="获得openid的url，用来跳转，只能在微信中打开",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得openid的url
    public function getBaseCode()
    {
        $param['scope'] = 'snsapi_base';
        $param['url']   = $this->request->root(true).DS.'content/action/getBaseInfo';

        //调用oauth->getCode
        $OAuth  = new Oauth();
        $ret    = Status::processStatus($OAuth->getCodeUrl($param));
        return json($ret);
    }

    /**
     * 获得基本信息，既openid
     */
    public function getBaseInfo()
    {
        //获得用户信息
        $OAuth  = new Oauth();
        $info   = $OAuth->getUserInfo();
        if (empty($info)) {
            $ret = false;
        } else {
            $ret = ['openid' => $info['openid']];
        }
        $res    = Status::processStatus($ret);
        Status::returnJson($res);
    }

    /**
     * @SWG\Post(
     *     path="/content/action/writeView",
     *     tags={"Content"},
     *     operationId="writeView",
     *     summary="记录浏览日志",
     *     description="记录浏览日志",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *      @SWG\Parameter(
     *         name="articleId",
     *         in="formData",
     *         description="文章id",
     *         required=true,
     *         type="integer"
     *     ),
     *      @SWG\Parameter(
     *         name="openid",
     *         in="formData",
     *         description="微信openid",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //记录浏览日志
    public function writeView()
    {
        $param      = $this->param;
        //验证数据
        $validate   = Loader::validate('Action');
        /*$param = [
            'articleId' => 1,
            'openid'    => 'ohnwgw5RXi1B_dwyETYxdDB56ne4',
        ];*/
        if (!$validate->checkWriteView($param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }
        //逻辑处理
        $Action = new \app\content\logic\Action();
        $openid = $param['openid'];
        $Action->view($openid);

        //记录日志
        $operatorInfo = $Action->getOperatorInfo();
        WriteUserAction::writeLog($param['articleId'],WriteUserAction::VIEW,$operatorInfo);

        //返回信息
        $res = Status::processStatus(true);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/content/action/writeShare",
     *     tags={"Content"},
     *     operationId="writeShare",
     *     summary="记录分享日志",
     *     description="记录分享日志",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *      @SWG\Parameter(
     *         name="articleId",
     *         in="formData",
     *         description="文章id",
     *         required=true,
     *         type="integer"
     *     ),
     *      @SWG\Parameter(
     *         name="openid",
     *         in="formData",
     *         description="微信openid",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="formData",
     *         description="分享类型",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="parentNo",
     *         in="formData",
     *         description="父分享编号",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //记录分享日志
    public function writeShare()
    {
        $param  = $this->param;
        /*$param = [
            'openid'    => 'ohnwgw5RXi1B_dwyETYxdDB56ne4',
            'articleId' => 1,
            'type'      => 'timeLine',
            'parentNo'  => '1804231002',
        ];*/
        //验证数据
        $validate   = Loader::validate('Action');
        if (!$validate->checkWriteShare($param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Action     = new \app\content\logic\Action();
        $Action->share($param);

        //记录日志
        $operatorInfo = $Action->getOperatorInfo();
        WriteUserAction::writeLog($param['articleId'],$param['type'],$operatorInfo);

        //返回信息
        return json(Status::processStatus(true));
    }

    /**
     * @SWG\Post(
     *     path="/content/action/getParenNo",
     *     tags={"Content"},
     *     operationId="getParenNo",
     *     summary="获得本次分享编号",
     *     description="获得本次分享编号，针对第一次分享，把分享编号拼接到分享连接上，若不是第一次分享，直接到连接上取值即可（这也是区分是不是第一次分享），不用再调此接口。",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得本次分享编号，针对第一次分享
    public function getParenNo()
    {
        $Action = new \app\content\logic\Action();
        $ret    = $Action->getParentNo();

        $res    = Status::processStatus($ret);
        return json($res);
    }

    /**
     *网页授权，base,获取code
     **/
    public function getCode()
    {
        //注意参数,注意编码
        $redirect_uri = urlencode("http://edu.natapp1.cc/wechat/public/content/action/getOpenId");
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".'wxdda6ad81f6f31882'."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=lingsi#wechat_redirect";
        header('location:'.$url);
    }







}