<?php
namespace app\menu\controller;


use app\common\controller\BaseAdmin;
use app\common\service\Status;
use app\common\service\WriteLog;
use app\menu\logic\Menu;
use think\Loader;

class Admin extends BaseAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/menu/admin/addMenu",
     *     tags={"Menu"},
     *     operationId="addMenu",
     *     summary="新增菜单",
     *     description="新增菜单",
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
     *         description="菜单信息",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     * )
     */
    //新增菜单
    public function addMenu()
    {
        $this->param = ['info' => [
            0 => [
                'menuName'      => 'menu1',
                'menuType'      => 'click',
                'menuKey'       => 'tf101',
                'menuValue'     => 'text',
                'menuLocation'  => ['abs' => 1, 'ord' => 1],
            ],
            1 => [
                'menuName'      => 'menu22',
                'menuType'      => 'view',
                'menuKey'       => 'https://baidu.com',
                'menuLocation'  => ['abs' => 2, 'ord' => 2],
            ],
            2 => [
                'menuName'      => 'menu23',
                'menuType'      => 'click',
                'menuKey'       => 'tf202',
                'menuValue'     => ['mediaId' => '3rWvOg_4rmTQbJ6S1wHOhzA7GgR45R040QvSLqI8vIQ'],
                'menuLocation'  => ['abs' => 2,'ord' => 3],
            ],
            3 => [
                'menuName'      => 'menu3',
                'menuType'      => 'click',
                'menuKey'       => 'tf301',
                'menuValue'     => [0 => ['title' => 'xxx','description' => 'test something!','image' => 'http://mmbiz.qpic.cn/mmbiz_jpg/6nR2NibOuvVoGfzunOSIsFcD367yBrQtmlIdrZ3HlomulQ5HeyVm1MXcMpeJjfZhZ78ITeicxs57MjFXgOMxxrlA/0?wx_fmt=jpeg','url' => 'http://mp.weixin.qq.com/s?__biz=MzI3MzY4NjE1MA==&mid=100000007&idx=1&sn=463c8565a934002773364b20b0017b5f&chksm=6b1ecaee5c6943f8290802a5eb44ae54649789bf91a473196389fddf7d13287e356e8a752fde#rd']],
                'menuLocation'  => ['abs' => 3,'ord' => 1],
            ],
            4 => [
                'menuName'      => 'menu2',
                'menuLocation'  => ['abs' => 2,'ord' => 1],
            ],
        ]];
        //验证数据
        $validate = Loader::validate('Menu');
        if (!$validate->checkAddMenu($this->param)) {
            return json(Status::processValidateMsg($validate->getError()));
        }

        //逻辑处理
        $Menu       = new Menu($this->loginLogInfo);
        $ret        = $Menu->addMenu($this->param);

        //记录日志
        $operatorInfo = $Menu->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::MENU,WriteLog::ADD,$this->request->post());

        //返回信息
        $res        = Status::processStatus($ret);
        return json($res);
    }

    /**
     * @SWG\Post(
     *     path="/menu/admin/getMenu",
     *     tags={"Menu"},
     *     operationId="getMenu",
     *     summary="获得菜单",
     *     description="获得菜单",
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
    //获得菜单
    public function getMenu()
    {
        //不用验证数据

        //逻辑处理
        $Menu = new Menu();
        $ret  = $Menu->getMenu();

        //不记录日志信息

        //返回信息
        $res  = Status::processStatus($ret);
        return json($res);
    }

    //修改菜单
    public function modifyMenu()
    {

    }

    /**
     * @SWG\Post(
     *     path="/menu/admin/deleteAllMenu",
     *     tags={"Menu"},
     *     operationId="deleteAllMenu",
     *     summary="删除所有菜单",
     *     description="删除所有菜单",
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
    //删除所有菜单
    public function deleteAllMenu()
    {
        //没有数据验证

        //逻辑处理
        $Menu   = new Menu($this->loginLogInfo);
        $ret    = $Menu->deleteAllMenu();

        //记录日志
        $operatorInfo = $Menu->getInitInfo();
        WriteLog::writeLog($ret,$operatorInfo,WriteLog::MENU,WriteLog::MORE_DELETE);

        //返回信息
        $res    = Status::processStatus($ret);
        return json($res);
    }



}