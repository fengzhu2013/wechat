<?php
namespace app\comment\controller;

use app\comment\logic\Comment;
use app\common\controller\BaseAdmin;
use app\common\service\Status;
use think\Loader;

class Admin extends BaseAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/comment/admin/getArticleList",
     *     tags={"Comment"},
     *     operationId="getArticleList",
     *     summary="获得已有的文章列表",
     *     description="获得已有的文章列表",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="SESSION_ID",
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
    //活动已有的文章列表
    public function getArticleList()
    {
        //验证数据
        $validate   = Loader::validate('Comment');
        if (!$validate->checkGetArticleList($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Comment    = new Comment();
        $ret        = $Comment->getArticleList($this->param);

        //不记录日志

        //返回信息
        $res        = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/comment/admin/getComByArticle",
     *     tags={"Comment"},
     *     operationId="getComByArticle",
     *     summary="根据文章获得留言",
     *     description="根据文章获得留言",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="SESSION_ID",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="msgid",
     *         in="formData",
     *         description="msgid",
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
    //根据文章获得留言
    public function getComByArticle()
    {
        //验证数据
        $validate   = Loader::validate('Comment');
        if (!$validate->checkGetComByArticle($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Comment    = new Comment();
        $ret        = $Comment->getComByArticle($this->param);

        //不记录日志

        //返回信息
        $res        = Status::processStatus($ret);
        return json($res);
    }

}