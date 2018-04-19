<?php
namespace app\wechat\service;

use app\wechat\controller\Index;

class Menu
{
    protected $app;

    public function __construct()
    {
        $Index = new Index();
        $this->app = $Index->getApp();
    }

    /**
     * 生成自定义菜单
     * @param $info
     * @return mixed
     */
    public function createMenu($info)
    {
        return $this->app->menu->create($info);
    }

    /**
     * 删除全部菜单
     * @return mixed
     */
    public function deleteAllMenu()
    {
        return $this->app->menu->delete();
    }



}