<?php
namespace app\common\logic;


use app\common\model\Admin;

class BaseLogic
{
    protected $userId;

    protected $objectId;


    /**
     * 登陆身份类型,1-普通，2-超管
     * @var
     */
    protected $loginType;

    /**
     * 登陆信息
     * @var array
     */
    protected $loginInfo = [];

    /**
     * 时间戳
     * @var int
     */
    protected $timestamp;


    public function __construct($loginLogInfo = [],$isVerifyType = false)
    {
        $this->timestamp = time();
        if ($loginLogInfo) {
            @$this->userId = $loginLogInfo['userId'];
            //通过用户id获得登录者管理员个人信息
            if ($isVerifyType) {
                $Admin = new Admin();
                $this->loginInfo = $Admin::getSelf(['userId' => $this->userId]);

                //初始化登陆信息
                $this->initLoginInfo();
            }
        }
    }

    //初始化登陆信息
    protected function initLoginInfo()
    {
        $this->loginType = $this->loginInfo['adminPower'];
    }

    //初始化日志信息
    protected function initLog($info)
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



}