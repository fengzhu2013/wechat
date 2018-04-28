<?php
namespace app\wechat\service;

use app\wechat\controller\Index;

class Comment
{
    protected $app;

    public function __construct()
    {
        $Index = new Index();
        $this->app = $Index->getApp();
    }

    public function getCommentList(string $msgId, int $index, int $begin, int $count, int $type = 0)
    {
        return $this->app->comment->list($msgId,$index,$begin,$count,$type);
    }





}