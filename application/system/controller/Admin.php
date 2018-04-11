<?php

namespace app\system\controller;

use think\Controller;

class Admin extends Controller
{
    const LOGIN_EXPIRE_TIME = 21600;            //登陆有效期为6h，单位s

    /**
     * 是否登陆
     * @var bool
     */
    private $isLogin = false;

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
     * 登陆的身份信息是否拥有调用action的权限
     * @var bool
     */
    private $isPower = false;

    /**
     * 是否有效
     * @var bool
     */
    private $isExpire = false;

    public function __construct()
    {
        //检验是否登陆，若登陆了，给loginInfo赋值
        $this->checkIsLogin();
        //如果没有登录,输出提示登录信息
        if (!$this->isLogin) {

        }

        //登陆了，
    }

    /**
     * 检验是否登陆
     */
    private function checkIsLogin()
    {
        //获得登陆信息
        $this->loginInfo = $this->getLoginInfo();
        //如果登陆信息为空
        if (!count($this->loginInfo))
            $this->isLogin = false;
        //如果登陆了，检验登陆是否过期
        if (!isset($this->loginInfo['loginTime']) || $this->loginInfo['loginTime'] + self::LOGIN_EXPIRE_TIME < time())
            $this->isLogin = false;
        $this->isLogin = true;
    }

    /**
     * 获得登陆信息，如果不存在，返回空的数组
     * @return array
     */
    private function getLoginInfo()
    {
        return [];
    }

    /**
     * 注销，退出登录状态
     */
    public function logout()
    {

    }

    /**
     * 新建一个普通管理员
     */
    public function createAdmin()
    {

    }

    /**
     * 输出登陆信息及权限信息
     */
    public function toStringLoginInfo()
    {

    }

    /**
     * 修改管理员信息
     */
    public function modifyAdminInfo()
    {

    }

    /**
     * 获得管理员列表
     */
    public function getAdminList()
    {

    }

    /**
     * 获得管理员操作记录列表
     */
    public function getWriteLogList()
    {

    }


    //添加系统信息
    public function addSystemInfo()
    {

    }

    //修改系统信息
    public function updateSystemInfo()
    {

    }



}