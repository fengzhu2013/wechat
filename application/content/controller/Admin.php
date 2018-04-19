<?php
namespace app\content\controller;


use app\common\controller\BaseAdmin;
use app\common\logic\ArrayTool;
use app\common\service\Status;
use app\common\service\WriteLog;
use app\content\logic\Article;
use think\Loader;

class Admin extends BaseAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/content/admin/getArticleList",
     *     tags={"Content"},
     *     operationId="getArticleList",
     *     summary="获得文章列表",
     *     description="获得文章列表",
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
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="pageSize",
     *         in="formData",
     *         description="页容量",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得文章列表
    public function getArticleList()
    {
        //验证参数
        $validate   = Loader::validate('Article');
        if (!$validate->checkGetArticleList($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }
        //逻辑处理
        $Article    = new Article();
        $ret        = $Article->getArticleList($this->param);

        //不记录日志
        //返回信息
        $res        = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/content/admin/getArticle",
     *     tags={"Content"},
     *     operationId="getArticle",
     *     summary="获得一篇文章的详情",
     *     description="获得一篇文章的详情",
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
     *         name="articleId",
     *         in="formData",
     *         description="文章id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得一篇文章的详情
    public function getArticle()
    {
        //验证参数
        $validate   = Loader::validate('Article');
        if (!$validate->checkGetArticle($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Article    = new Article();
        $ret        = $Article->getArticle($this->param);

        //不记录日志

        //返回信息
        $res        = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/content/admin/modifyArticle",
     *     tags={"Content"},
     *     operationId="modifyArticle",
     *     summary="修改一篇文章",
     *     description="修改一篇文章",
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
     *         name="articleId",
     *         in="formData",
     *         description="文章编号",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="title",
     *         in="formData",
     *         description="标题",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="content",
     *         in="formData",
     *         description="内容",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="summary",
     *         in="formData",
     *         description="摘要",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //修改一篇文章
    public function modifyArticle()
    {
        //验证数据
        $validate   = Loader::validate('Article');
        if (!$validate->checkModifyArticle($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Article    = new Article($this->loginLogInfo);
        $ret        = $Article->modifyArticle($this->param);

        //记录日志
        $operatorInfo = $Article->getInitInfo();
        $msg          = ArrayTool::removeKey($this->request->post(),'content');
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::ARTICLE,WriteLog::UPDATE,$msg);

        //返回信息
        $res        = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/content/admin/deleteArticle",
     *     tags={"Content"},
     *     operationId="deleteArticle",
     *     summary="删除一片文章",
     *     description="删除一片文章",
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
     *         name="articleId",
     *         in="formData",
     *         description="文章编号",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //删除一片文章
    public function deleteArticle()
    {
        //验证数据
        $validate   = Loader::validate('Article');
        if (!$validate->checkDeleteArticle($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Article    = new Article($this->loginLogInfo);
        $ret        = $Article->deleteArticle($this->param);

        //记录日志
        $operatorInfo = $Article->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::ARTICLE,WriteLog::DELETE,$this->request->post());

        //返回数据
        $res        = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/content/admin/addArticle",
     *     tags={"Content"},
     *     operationId="addArticle",
     *     summary="添加一篇文章",
     *     description="添加一篇文章",
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
     *         name="title",
     *         in="formData",
     *         description="标题",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="content",
     *         in="formData",
     *         description="内容",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="summary",
     *         in="formData",
     *         description="摘要",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //添加一篇文章
    public function addArticle()
    {
        //验证信息
        $validate       = Loader::validate('Article');
        if (!$validate->checkAddArticle($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Article        = new Article($this->loginLogInfo);
        $ret            = $Article->addArticle($this->param);

        //记录日志
        $operatorInfo   = $Article->getInitInfo();
        $msg            = ArrayTool::removeKey($this->request->post(),'content');
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::ARTICLE,WriteLog::ADD,$msg);

        //返回信息
        $res            = Status::processStatus($ret);
        return json($res);
    }



}