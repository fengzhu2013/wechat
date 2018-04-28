<?php
namespace app\wechat\service;


use app\wechat\controller\Index;

class User
{
    protected $app;

    public function __construct()
    {
        $Index = new Index();
        $this->app = $Index->getApp();
    }

    /**
     * 获取多个用户信息
     * @param array $openidArr
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     */
    public function getAllUserInfo(array $openidArr)
    {
        return $this->app->user->select($openidArr);
    }



}