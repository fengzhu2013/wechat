<?php
namespace app\admin\logic;
use app\common\model\Admin;
use app\common\model\SystemInfo;
use think\Loader;


/**
 * 系统信息
 * Class System
 * @package app\admin\logic
 */
class System
{
    protected $request;

    protected $userId;

    protected $objectId;

    /**
     * 登陆身份类型,1-普通，2-超管
     * @var
     */
    private $loginType;

    /**
     * 登陆信息
     * @var array
     */
    private $loginInfo = [];

    /**
     * 时间戳
     * @var int
     */
    private $timestamp;

    public function __construct($loginLogInfo = [])
    {
        $this->timestamp = time();
        if ($loginLogInfo) {
            @$this->userId = $loginLogInfo['userId'];
            //通过用户id获得登录者管理员个人信息
            $AdminObj = new Admin();
            $this->loginInfo = $AdminObj::getSelf(['userId' => $this->userId]);

            //初始化登陆信息
            $this->initLoginInfo();
        }
    }

    //系统登录
    public function login($info)
    {
        if (!$info->has('userId','post') || !$info->has('password','post')) {
            return '50003';         //没有传人相关信息
        }

        if (empty($info->post('userId')) || empty($info->post('userId'))) {
            return '50001';         //传参为空或传参不全
        }
        //通过userId获得相应的信息
        $AdminObj = new Admin();
        $userInfo = $AdminObj->getSelf(['userId' => $info->post('userId')]);
        if (!$userInfo) {
            return '40002';
        }

        //比对密码是否正确
        if (md5($info->post('password')) !== $userInfo['password']) {
            return '40003';         //账号或密码错误
        }

        //初始化记录日志的必要值
        $this->initLog($userInfo);

        //生成SESSION_ID
        $SESSION_ID = createSESSION_ID($userInfo['userId'],$userInfo['password'],$this->timestamp);
        return ['SESSION_ID' => $SESSION_ID];
    }

    //初始化登陆信息
    private function initLoginInfo()
    {
        $this->loginType = $this->loginInfo['adminPower'];
    }

    //初始化日志信息
    private function initLog($info)
    {
        if (isset($info['userId'])) {
            $this->userId = $info['userId'];
        }
        $this->objectId = $info['id'];
    }


    /**
     * 获得日志必要信息
     * @return array
     */
    public function getInitInfo()
    {
        return [
            'userId'        => $this->userId,
            'objectId'      => $this->objectId,
            'createTime'    => $this->timestamp,
            ];
    }

    //添加微信系统必要信息，如appid等
    public function addSystemInfo($param)
    {
        //是否超管
        if ($this->loginType != 2) {
            return '30005';     //提示权限不够
        }

        //验证信息
        $validate = Loader::validate('SystemInfo');
        if (!$validate->check($param)) {
            return '50002';     //提示传参不符合格式要求
        }

        //插入一条数据
        $SystemInfoObj = new SystemInfo($param);
        if ($SystemInfoObj->saveSelf()) {
            $this->initLog(['id' => intval($SystemInfoObj->id)]);
            return true;
        }

        //提示添加失败
        return '10002';
    }



}