<?php
namespace app\wechat\service;


use app\wechat\controller\Index;

class DataCube
{
    protected $app;

    public function __construct()
    {
        $Index  = new Index();
        $this->app = $Index->getApp();
    }


    /**
     * 获得日群发文章数据
     * @param string $date
     * @return mixed
     */
    public function getArticleSummary(string $date)
    {
        return $this->app->data_cube->articleSummary($date,$date);
    }

    /**
     * 获得群发日数据
     * @param string $date
     * @return mixed
     */
    public function getArticleTotal(string $date)
    {
        return $this->app->data_cube->articleTotal($date,$date);
    }





}