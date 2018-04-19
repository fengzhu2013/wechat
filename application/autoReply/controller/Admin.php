<?php
namespace app\autoReply\controller;

use app\autoReply\logic\AutoReply;
use app\common\controller\BaseAdmin;
use app\common\service\Status;
use app\common\service\WriteLog;
use think\Loader;

class Admin extends BaseAdmin
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/autoReply/admin/setSubAndDefInfo",
     *     tags={"AutoReply"},
     *     operationId="setSubAndDefInfo",
     *     summary="自定义关注时或默认回复",
     *     description="自定义关注时或默认回复",
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
     *         description="1-关注，2-默认",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="key",
     *         in="formData",
     *         description="素材类型",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="value",
     *         in="formData",
     *         description="内容",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //自定义关注时或默认回复
    public function setSubAndDefInfo()
    {
        /*$this->param = [
            'type'  => '1',
            'key'   => 'news',
            'value' => [
                0   => ['title' => 'xxx','description'  => 'test something!','image' => 'http://mmbiz.qpic.cn/mmbiz_jpg/6nR2NibOuvVoGfzunOSIsFcD367yBrQtmlIdrZ3HlomulQ5HeyVm1MXcMpeJjfZhZ78ITeicxs57MjFXgOMxxrlA/0?wx_fmt=jpeg','url' => 'http://mp.weixin.qq.com/s?__biz=MzI3MzY4NjE1MA==&mid=100000007&idx=1&sn=463c8565a934002773364b20b0017b5f&chksm=6b1ecaee5c6943f8290802a5eb44ae54649789bf91a473196389fddf7d13287e356e8a752fde#rd'],
                1   => ['title' => 'xxx','description'  => 'test something!','image' => 'http://mmbiz.qpic.cn/mmbiz_jpg/6nR2NibOuvVoGfzunOSIsFcD367yBrQtmlIdrZ3HlomulQ5HeyVm1MXcMpeJjfZhZ78ITeicxs57MjFXgOMxxrlA/0?wx_fmt=jpeg','url' => 'http://mp.weixin.qq.com/s?__biz=MzI3MzY4NjE1MA==&mid=100000007&idx=1&sn=463c8565a934002773364b20b0017b5f&chksm=6b1ecaee5c6943f8290802a5eb44ae54649789bf91a473196389fddf7d13287e356e8a752fde#rd'],
            ],
        ];*/
        //验证数据
        $validate = Loader::validate('AutoReply');
        if (!$validate->checkSet($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $AutoReply  = new AutoReply($this->loginLogInfo);
        $ret        = $AutoReply->setSubAndDefInfo($this->param);

        //记录日志信息
        $operatorInfo = $AutoReply->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::AUTO_REPLY,WriteLog::ADD,$this->request->post());

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/autoReply/admin/getSubAndDefInfo",
     *     tags={"AutoReply"},
     *     operationId="getSubAndDefInfo",
     *     summary="获得自定义关注时或默认回复",
     *     description="获得自定义关注时或默认回复",
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
     *         description="1-关注，2-默认",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得关注时或默认回复信息
    public function getSubAndDefInfo()
    {
        //验证数据
        $validate = Loader::validate('AutoReply');
        if (!$validate->checkGetSubAndDefInfo($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $AutoReply = new AutoReply();
        $ret       = $AutoReply->getSubAndDefInfo($this->param);

        //不记录日志
        //返回数据
        $res       = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/autoReply/admin/modifySubAndDefInfo",
     *     tags={"AutoReply"},
     *     operationId="modifySubAndDefInfo",
     *     summary="修改关注时或默认回复信息",
     *     description="修改关注时或默认回复信息",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         name="id",
     *         in="formData",
     *         description="id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="key",
     *         in="formData",
     *         description="素材类型",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="value",
     *         in="formData",
     *         description="内容",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //修改关注时或默认回复信息
    public function modifySubAndDefInfo()
    {
        //验证数据
        $validate = Loader::validate('AutoReply');
        if (!$validate->checkModifySubAndDefInfo($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $AutoReply  = new AutoReply($this->loginLogInfo);
        $ret        = $AutoReply->modifySubAndDefInfo($this->param);

        //记录日志
        $operatorInfo = $AutoReply->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::AUTO_REPLY,WriteLog::UPDATE,$this->request->post());

        //返回数据
        $res        = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/autoReply/admin/deleteReplyInfo",
     *     tags={"AutoReply"},
     *     operationId="deleteReplyInfo",
     *     summary="删除默认，关注或关键词回复信息",
     *     description="删除默认，关注或关键词回复信息",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         name="id",
     *         in="formData",
     *         description="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //删除默认，关注或关键词回复信息
    public function deleteReplyInfo()
    {
        //验证数据
        $validate = Loader::validate('AutoReply');
        if (!$validate->checkDeleteReplyInfo($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $AutoReply  = new AutoReply($this->loginLogInfo);
        $ret        = $AutoReply->deleteReplyInfo($this->param);

        //记录信息
        $operatorInfo = $AutoReply->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::AUTO_REPLY,WriteLog::DELETE,$this->request->post());

        //返回数据
        $res        = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/autoReply/admin/addKeywordsInfo",
     *     tags={"AutoReply"},
     *     operationId="addKeywordsInfo",
     *     summary="新增关键词回复信息",
     *     description="新增关键词回复信息",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         name="key",
     *         in="formData",
     *         description="key",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="formData",
     *         description="type,3-半，4-全",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="value",
     *         in="formData",
     *         description="内容",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="keywords",
     *         in="formData",
     *         description="关键字",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="startTime",
     *         in="formData",
     *         description="开始有效期",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="endTime",
     *         in="formData",
     *         description="截至有效期",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //新增关键词回复信息
    public function addKeywordsInfo()
    {
        //验证数据
        $validate = Loader::validate('AutoReply');
        if (!$validate->checkAddKeywordsInfo($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $AutoReply  = new AutoReply($this->loginLogInfo);
        $ret        = $AutoReply->addKeywordsInfo($this->param);

        //记录日志信息
        $operatorInfo = $AutoReply->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::AUTO_REPLY,WriteLog::ADD,$this->request->post());

        //返回信息
        $res        = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/autoReply/admin/getKeywordsInfo",
     *     tags={"AutoReply"},
     *     operationId="getKeywordsInfo",
     *     summary="获得关键词回复信息",
     *     description="获得关键词回复信息",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         name="id",
     *         in="formData",
     *         description="id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得关键词回复信息
    public function getKeywordsInfo()
    {
        //验证数据
        $validate = Loader::validate('AutoReply');
        if (!$validate->checkGetKeywordsInfo($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $AutoReply  = new AutoReply();
        $ret        = $AutoReply->getKeywordsInfo($this->param);

        //不记录日志信息

        //返回信息
        $res        = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/autoReply/admin/getKeywordsList",
     *     tags={"AutoReply"},
     *     operationId="getKeywordsList",
     *     summary="获得关键词列表",
     *     description="获得关键词列表",
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
    //获得关键词列表
    public function getKeywordsList()
    {
        //没有数据验证

        //逻辑处理
        $AutoReply  = new AutoReply();
        $ret        = $AutoReply->getKeywordsList();

        //不记录日志信息

        //返回信息
        $res        = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/autoReply/admin/modifyKeywordsInfo",
     *     tags={"AutoReply"},
     *     operationId="modifyKeywordsInfo",
     *     summary="修改关键词信息",
     *     description="修改关键词信息",
     *     consumes={"multipart/form-data"},
     *     produces={"multipart/form-data"},
     *     @SWG\Parameter(
     *         name="SESSION_ID",
     *         in="formData",
     *         description="令牌号",
     *         required=true,
     *         type="string"
     *     ),
     *      @SWG\Parameter(
     *         name="id",
     *         in="formData",
     *         description="id",
     *         required=true,
     *         type="integer"
     *     ),
     *      @SWG\Parameter(
     *         name="key",
     *         in="formData",
     *         description="key",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="formData",
     *         description="type,3-半，4-全",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="value",
     *         in="formData",
     *         description="内容",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="keywords",
     *         in="formData",
     *         description="关键字",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="startTime",
     *         in="formData",
     *         description="开始有效期",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="endTime",
     *         in="formData",
     *         description="截至有效期",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //修改关键词信息
    public function modifyKeywordsInfo()
    {
        //验证信息
        $validate = Loader::validate('AutoReply');
        if (!$validate->checkModifyKeywordsInfo($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $AutoReply  = new AutoReply($this->loginLogInfo);
        $ret        = $AutoReply->modifyKeywordsInfo($this->param);

        //记录日志信息
        $operatorInfo   = $AutoReply->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::AUTO_REPLY,WriteLog::UPDATE,$this->request->post());

        //返回信息
        $res        = Status::processStatus($ret);
        return json($res);
    }




}