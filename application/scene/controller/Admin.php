<?php
namespace app\scene\controller;

use app\common\controller\BaseAdmin;
use app\common\service\Status;
use app\common\service\WriteLog;
use app\scene\logic\Scene;
use think\Loader;

class Admin extends BaseAdmin
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/scene/admin/getSceneList",
     *     tags={"Scene"},
     *     operationId="getSceneList",
     *     summary="获得渠道列表",
     *     description="活动渠道列表，用于查看渠道信息",
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
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="pageSize",
     *         in="formData",
     *         description="页容量",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //获得渠道列表
    public function getSceneList()
    {
        //验证数据
        $validate   = Loader::validate('Scene');
        if (!$validate->checkGetSceneList($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Scene  = new Scene();
        $ret    = $Scene->getSceneList($this->param);

        //不记录日志

        //返回信息
        $res    = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/scene/admin/addOneScene",
     *     tags={"Scene"},
     *     operationId="addOneScene",
     *     summary="增加一个渠道信息",
     *     description="单个添加渠道信息",
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
     *         name="nameCn",
     *         in="formData",
     *         description="渠道名",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="sceneType",
     *         in="formData",
     *         description="渠道类型",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="province",
     *         in="formData",
     *         description="省份",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="phone",
     *         in="formData",
     *         description="联系方式",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="city",
     *         in="formData",
     *         description="城市",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="area",
     *         in="formData",
     *         description="区域",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="addressCn",
     *         in="formData",
     *         description="具体地址",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="startDate",
     *         in="formData",
     *         description="有效开始日期",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="endDate",
     *         in="formData",
     *         description="有效截至日期",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //添加单个渠道
    public function addOneScene()
    {
        //验证数据
        $validate = Loader::validate('SceneInfo');
        if (!$validate->check($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Scene  = new Scene($this->loginLogInfo);
        $ret    = $Scene->addOneScene($this->param);

        //写日志
        $operatorInfo = $Scene->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::SCENE_INFO,WriteLog::ADD,$this->param);

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/scene/admin/modifyScene",
     *     tags={"Scene"},
     *     operationId="modifyScene",
     *     summary="修改一个渠道信息",
     *     description="修改渠道信息",
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
     *         description="渠道表id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="nameCn",
     *         in="formData",
     *         description="渠道名",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="sceneType",
     *         in="formData",
     *         description="渠道类型",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="province",
     *         in="formData",
     *         description="省份",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="phone",
     *         in="formData",
     *         description="联系方式",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="city",
     *         in="formData",
     *         description="城市",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="area",
     *         in="formData",
     *         description="区域",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="addressCn",
     *         in="formData",
     *         description="具体地址",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="startDate",
     *         in="formData",
     *         description="有效开始日期",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="endDate",
     *         in="formData",
     *         description="有效截至日期",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //修改渠道
    public function modifyScene()
    {
        //验证信息
        $validate = Loader::validate('SceneInfo');
        if (!$validate->checkModifyScene($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Scene  = new Scene($this->loginLogInfo);
        $ret    = $Scene->modifyScene($this->param);

        //记录日志
        $operatorInfo = $Scene->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::SCENE_INFO,WriteLog::UPDATE,$this->request->post());

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/scene/admin/deleteOneScene",
     *     tags={"Scene"},
     *     operationId="addOneScene",
     *     summary="删除一个渠道信息",
     *     description="其实是修改渠道状态信息",
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
     *         name="id",
     *         in="formData",
     *         description="渠道表id",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //删除单个渠道
    public function deleteOneScene()
    {
        //验证字段
        $validate = Loader::validate('Scene');
        if (!$validate->check($this->param,$validate->getRule('modifyRule'))) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Scene  = new Scene($this->loginLogInfo);
        $ret    = $Scene->deleteOneScene($this->param);

        //记录日志
        $operatorInfo = $Scene->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::SCENE,WriteLog::DELETE,$this->request->post());

        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/scene/admin/deleteScenes",
     *     tags={"Scene"},
     *     operationId="deleteScenes",
     *     summary="批量删除渠道信息",
     *     description="其实是修改了渠道状态信息",
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
     *         name="ids",
     *         in="formData",
     *         description="渠道表id数组",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //批量删除渠道
    public function deleteScenes()
    {
        //验证数据
        $validate   = Loader::validate('Scene');
        if (!$validate->checkDeleteScenes($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Scene  = new Scene($this->loginLogInfo);
        $ret    = $Scene->deleteScenes($this->param);

        //写日志
        $operatorInfo = $Scene->getInitInfo();
        $msg          = $Scene->getActionMsg();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::SCENE,WriteLog::MORE_DELETE,$msg);

        //返回信息
        $res = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/scene/admin/addScenes",
     *     tags={"Scene"},
     *     operationId="addScenes",
     *     summary="批量导入渠道信息",
     *     description="上传csv文件，批量导入渠道信息",
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
     *         name="sceneFile",
     *         in="formData",
     *         description="渠道csv文件",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //批量添加多个渠道
    public function addScenes()
    {
        $Scene = new Scene();
        $ret = $Scene->addScenes($this->request);

        //记录日志
        $operatorInfo = $Scene->getInitInfo();
        $msg          = $Scene->getActionMsg();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::SCENE_INFO,WriteLog::MORE_ADD,$msg,$this->userId);
        //返回数据
        $res = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/scene/admin/downloadScenes",
     *     tags={"Scene"},
     *     operationId="downloadScenes",
     *     summary="打包下载渠道信息",
     *     description="打包下载渠道信息,zip格式",
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
    //批量打包下载渠道
    public function downloadScenes()
    {
        $Scene = new Scene($this->loginLogInfo);
        $ret   = $Scene->downloadScenes($this->request);

        //记录日志
        $operatorInfo = $Scene->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::SCENE,WriteLog::OUTPUT,$this->request->post());

        //输出
        $res = Status::processStatus($ret);
        return json($res);
    }


}